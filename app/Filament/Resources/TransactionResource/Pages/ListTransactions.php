<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('export')
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
        ];
    }

    public function exportTaxRelated(array $data)
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        return (new TransactionsExport($startDate, $endDate))->download('transactions.xlsx');
    }
}
