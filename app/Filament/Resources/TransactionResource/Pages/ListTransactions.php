<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Actions\ConvertTransactionsToMarkdown;
use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

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

    protected function getActions(): array
    {
        $firstTransactionDate = Transaction::orderBy('date')->first()->date;
        $lastTransactionDate = Transaction::orderByDesc('date')->first()->date;

        return [
            Actions\CreateAction::make(),
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
