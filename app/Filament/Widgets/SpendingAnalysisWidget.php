<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enumerations\TransactionCategories;
use App\Enumerations\TransactionTypes;
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
        $categories = TransactionCategories::values();

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
            /** @phpstan-ignore-next-line */
            TextColumn::make('total')->label('Total')->formatStateUsing(fn (Transaction $record): string => $record->formatValueAsMoneyString($record->total / 100)),
        ];
    }
}
