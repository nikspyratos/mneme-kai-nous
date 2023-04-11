<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Tally;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TalliesTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Tallies';

    protected int|string|array $columnSpan = 1;

    protected function getTableQuery(): Builder
    {
        return Tally::forCurrentBudgetMonth();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('tally')->label('Tally')
                ->formatStateUsing(fn (Tally $record): string => $record->name . ': ' . $record->formatted_balance . ' / ' . $record->formatted_limit . ' - ' . $record->getBalancePercentageOfBudget() . '%')
                ->color(fn (Tally $record): string => $this->getPercentageColor($record)),
        ];
    }

    private function getPercentageColor(Tally $tally)
    {
        $percentage = $tally->getBalancePercentageOfBudget();
        $color = 'success';
        if ($percentage >= 65 && $percentage < 100) {
            $color = 'warning';
        } elseif ($percentage >= 100) {
            $color = 'danger';
        }

        return $color;
    }
}
