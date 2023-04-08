<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Actions\ConvertTransactionsToMarkdown;
use App\Enums\Banks;
use App\Enums\TransactionTypes;
use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use App\Imports\BankZeroImport;
use App\Models\Account;
use App\Models\ExpectedTransaction;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function importBankZero(array $data)
    {
        $monthYear = explode('_', pathinfo($data['file'], PATHINFO_FILENAME))[2];
        $month = substr($monthYear, 0, 3);
        $year = substr($monthYear, strlen($monthYear) - 2, 2);
        $sheetName = "{$month} {$year} Transactions";
        Excel::import(new BankZeroImport(Account::find($data['account']), $sheetName), Storage::path($data['file']));
    }

    public function exportTaxRelated(array $data)
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        return (new TransactionsExport($startDate, $endDate))->download('transactions.xlsx');
    }

    public function exportMarkdown(array $data)
    {
        $monthYear = Carbon::parse($data['month_year']);
        $filename = 'markdown_' . Carbon::now()->timestamp . '.md';
        Storage::disk('local')->put($filename, ConvertTransactionsToMarkdown::run($monthYear));

        return Storage::disk('local')->download($filename);
    }

    public function updateExpectedTransactions(Transaction $record, array $data)
    {
        $record->expectedTransactions()->sync($data['expected_transactions']);
        ExpectedTransaction::whereId($data['expected_transactions'])->update(['is_paid' => true]);
    }

    public function splitTransaction(Transaction $record, array $data)
    {
        $amount = $data['amount'] * 100;
        Transaction::create([
            'parent_id' => $record->id,
            'account_id' => $record->account_id,
            'tally_id' => $record->tally_id,
            'date' => $record->date,
            'type' => $record->type,
            'category' => $record->category,
            'description' => $record->description,
            'detail' => $data['detail'],
            'currency' => $record->currency,
            'amount' => $amount,
            'fee' => $record->fee,
            'listed_balance' => $record->listed_balance,
            'data' => $record->data,
            'is_tax_relevant' => $record->is_tax_relevant,
        ]);
        $record->update([
            'amount' => $record->amount - $amount,
        ]);
    }

    public function owedTransaction(Transaction $record, array $data)
    {
        $amount = $data['amount'] * 100;
        $expectedTransaction = ExpectedTransaction::create([
            'name' => 'Owed for transaction ' . $record->id,
            'description' => $record->detail,
            'group' => 'Owed',
            'currency' => $record->currency,
            'amount' => $amount,
            'is_paid' => $data['is_paid'],
            'date' => $record->date,
            'type' => TransactionTypes::getOppositeType(TransactionTypes::from($record->type)),
            'is_tax_relevant' => $record->is_tax_relevant,
        ]);
        $record->expectedTransactions()->attach($expectedTransaction->id);
        $record->tally->update([
            'balance' => $record->tally->balance - $amount,
        ]);
    }

    protected function getActions(): array
    {
        $firstTransactionDate = Transaction::orderBy('date')->first()->date;
        $lastTransactionDate = Transaction::orderByDesc('date')->first()->date;

        return [
            Actions\CreateAction::make(),
            Action::make('import_bankzero')
                ->label('Import from BankZero')
                ->color('success')
                ->icon('heroicon-o-document-add')
                ->action('importBankZero')
                ->form([
                    Select::make('account')
                        ->label('Account')
                        ->options(Account::whereBankName(Banks::BANKZERO->value)->pluck('name', 'id'))
                        ->required(),
                    FileUpload::make('file')
                        ->label('File')
                        ->disk(Storage::getDefaultDriver())
                        ->preserveFilenames()
                        ->helperText('Do not change the original downloaded filename or this will not work.')
                        ->required(),
                ]),
            Action::make('export_tax')
                ->label('Export Tax-Related')
                ->color('success')
                ->icon('heroicon-o-document-download')
                ->action('exportTaxRelated')
                ->form([
                    DatePicker::make('start_date')
                        ->default(Carbon::today()->subMonths(6)->startOfMonth())
                        ->required(),
                    DatePicker::make('end_date')
                        ->default(Carbon::today()->endOfMonth())
                        ->required(),
                ]),
            Action::make('export_markdown')
                ->label('Export Markdown')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->action('exportMarkdown')
                ->form([
                    DatePicker::make('month_year')
                        ->default(Carbon::today()->startOfMonth())
                        ->minDate($firstTransactionDate)
                        ->maxDate($lastTransactionDate)
                        ->helperText('Pick any date in the month to select that month & year')
                        ->required(),
                ]),
        ];
    }
}
