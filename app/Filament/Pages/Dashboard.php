<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AccountsWidget;
use App\Filament\Widgets\ExpectedTransactionsWidget;
use App\Filament\Widgets\GeneralWidget;
use App\Filament\Widgets\TalliesTableWidget;
use App\Models\Tally;
use Filament\Pages\Actions\Action;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Storage;

class Dashboard extends BasePage
{
    public function downloadDatabase()
    {
        return Storage::disk('database')->download('database.sqlite');
    }

    public function recalculateTallyBalances()
    {
        Tally::forCurrentBudgetMonth()->get()->each->calculateBalance();
    }

    protected function getColumns(): int|array
    {
        return 5;
    }

    protected function getActions(): array
    {
        return [
            Action::make('download_database')
                ->label('Download Database')
                ->color('success')
                ->icon('heroicon-o-database')
                ->action('downloadDatabase'),
            Action::make('recalculate_tallies')
                ->label('Recalculate Tally Balances')
                ->color('success')
                ->icon('heroicon-o-calculator')
                ->action('recalculateTallyBalances'),
        ];
    }

    protected function getWidgets(): array
    {
        return [
            AccountsWidget::class,
            ExpectedTransactionsWidget::class,
            TalliesTableWidget::class,
            GeneralWidget::class,
        ];
    }
}
