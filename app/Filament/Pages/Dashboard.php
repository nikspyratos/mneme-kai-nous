<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\AccountsWidget;
use App\Filament\Widgets\ExpectedTransactionsWidget;
use App\Filament\Widgets\GeneralWidget;
use App\Filament\Widgets\SpendingAnalysisWidget;
use App\Filament\Widgets\TalliesTableWidget;
use App\Models\Tally;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Storage;

class Dashboard extends BasePage
{
    public function getColumns(): int|array
    {
        return 5;
    }

    public function getWidgets(): array
    {
        return [
            AccountsWidget::class,
            ExpectedTransactionsWidget::class,
            TalliesTableWidget::class,
            SpendingAnalysisWidget::class,
            GeneralWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_database')
                ->label('Download Database')
                ->color('success')
                ->icon('heroicon-o-circle-stack')
                ->action(fn () => Storage::disk('root')->download('sqlite/database.sqlite')),
            Action::make('recalculate_tallies')
                ->label('Recalculate Tally Balances')
                ->color('success')
                ->icon('heroicon-o-calculator')
                ->action(fn () => Tally::forCurrentBudgetMonth()->get()->each->calculateBalance()),
        ];
    }
}
