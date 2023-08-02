<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Actions\ConvertTransactionsToMarkdown;
use App\Enumerations\Banks;
use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use App\Imports\BankZeroImport;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        $firstTransactionDate = Transaction::orderBy('date')->first()->date;
        $lastTransactionDate = Transaction::orderByDesc('date')->first()->date;

        return [
            CreateAction::make(),
            Action::make('import_bankzero')
                ->label('Import from BankZero')
                ->color('success')
                ->icon('heroicon-o-document-plus')
                ->action(function (array $data) {
                    $monthYear = explode('_', pathinfo($data['file'], PATHINFO_FILENAME))[2];
                    $month = substr($monthYear, 0, 3);
                    $year = substr($monthYear, strlen($monthYear) - 2, 2);
                    $sheetName = "{$month} {$year} Transactions";
                    Excel::import(new BankZeroImport(Account::find($data['account']), $sheetName), Storage::path($data['file']));
                })
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
                ->icon('heroicon-o-document-arrow-down')
                ->action(function (array $data) {
                    $startDate = Carbon::parse($data['start_date']);
                    $endDate = Carbon::parse($data['end_date']);

                    return (new TransactionsExport($startDate, $endDate))->download('transactions.xlsx');
                })
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
                ->action(function (array $data) {
                    $monthYear = Carbon::parse($data['month_year']);
                    $filename = 'markdown_' . Carbon::now()->timestamp . '.md';
                    Storage::disk('local')->put($filename, ConvertTransactionsToMarkdown::run($monthYear));

                    return Storage::disk('local')->download($filename);
                })
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
