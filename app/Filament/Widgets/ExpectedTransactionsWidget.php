<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypes;
use App\Models\ExpectedTransaction;
use App\Services\TallyRolloverDateCalculator;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpectedTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Expected Expenses';

    protected int|string|array $columnSpan = 2;

    protected function getTableQuery(): Builder
    {
        return ExpectedTransaction::whereType(TransactionTypes::DEBIT->value)
            ->whereBetween(
                'next_due_date',
                [
                    TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                ]
            );
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('Name'),
            TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->formatted_amount),
            TextColumn::make('next_due_date')->label('Next Due')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->next_due_date->toDateString()),
            IconColumn::make('is_paid')
                ->label('Is Paid')
                ->boolean()
                ->getStateUsing(fn (ExpectedTransaction $record): bool => $record->transactions()->inCurrentBudgetMonth()->exists()),
        ];
    }
}
