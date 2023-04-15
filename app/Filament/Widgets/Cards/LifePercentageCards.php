<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Cards;

use Filament\Widgets\StatsOverviewWidget\Card;

class LifePercentageCards
{
    public static function getCards(): array
    {
        $data = [];
        foreach ([30, 50, 75, 85] as $year) {
            [$percentageLeft, $percentageComplete] = auth()->user()->getDeathPercentage($year);
            $data[] = "{$year}y: {$percentageComplete}%";
        }

        return [
            Card::make('Life Percentage', implode(' | ', $data))
                ->color('primary')
                ->icon('heroicon-o-clock'),
        ];
    }
}
