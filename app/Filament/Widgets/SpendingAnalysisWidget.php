<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionCategories;
use App\Enums\TransactionTypes;
use App\Models\Transaction;
use App\Services\TallyRolloverDateCalculator;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SpendingAnalysisWidget extends BaseWidget
{
    protected static ?string $heading = 'Spending Analysis';

    protected int|string|array $columnSpan = 1;

    protected function getTableQuery(): Builder
    {
        //Remove groceries from the list
        $categories = TransactionCategories::values();
        unset($categories[0]);

        return Transaction::selectRaw('id, currency, category, SUM(amount) as total')
            ->where('type', TransactionTypes::DEBIT->value)
            ->whereIn('category', $categories)
            ->whereBetween('date', [TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate()])
            ->groupBy('category');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('category')->label('Name'),
            TextColumn::make('total')->label('Total')->formatStateUsing(fn (Transaction $record): string => $record->formatValueAsMoneyString($record->total / 100)),
        ];
    }
}
