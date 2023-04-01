<?php

namespace App\Filament\Widgets;

use App\Models\Tally;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TalliesTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Tallies';

    protected int|string|array $columnSpan = 2;

    protected function getTableQuery(): Builder
    {
        return Tally::forCurrentBudgetMonth();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('Name'),
            TextColumn::make('balance')->formatStateUsing(fn (Tally $record): string => $record->formatted_balance),
            TextColumn::make('limit')->formatStateUsing(fn (Tally $record): string => $record->formatted_limit),
            TextColumn::make('percentage')
                ->formatStateUsing(fn (Tally $record): string => $record->getBalancePercentageOfBudget() . '%')
                ->color(fn (Tally $record): string => $this->getPercentageColor($record)),
        ];
    }

    private function getPercentageColor(Tally $tally)
    {
        $percentage = $tally->getBalancePercentageOfBudget();
        $color = 'success';
        if ($percentage >= 65) {
            $color = 'warning';
        } elseif ($percentage >= 85) {
            $color = 'danger';
        }

        return $color;
    }
}
