<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypes;
use App\Models\ExpectedTransaction;
use App\Services\TallyRolloverDateCalculator;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpectedTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Expected Expenses';

    protected int|string|array $columnSpan = 2;

    protected function getTableQuery(): Builder
    {
        return ExpectedTransaction::whereEnabled(true)
            ->where(function ($query) {
                $query->where('next_due_date', null)
                    ->orWhereBetween(
                        'next_due_date',
                        [
                            TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                        ]
                    );
            });
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('Name'),
            TextColumn::make('amount')
                ->formatStateUsing(function (ExpectedTransaction $record): string {
                    $typePrefix = $record->type === TransactionTypes::DEBIT->value ? '-' : '+';

                    return $typePrefix . $record->formatted_amount;
                }),
            TextColumn::make('next_due_date')->label('Next Due')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->next_due_date?->toDateString() ?? ''),
            ToggleColumn::make('is_paid')
                ->label('Is Paid'),
            ToggleColumn::make('enabled'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('is_paid')
                ->query(fn (Builder $query): Builder => $query->where('is_paid', true)),
            Filter::make('unpaid')
                ->query(fn (Builder $query): Builder => $query->where('is_paid', false))
                ->default(),
        ];
    }
}
