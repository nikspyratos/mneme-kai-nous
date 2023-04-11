<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Cards;

use Filament\Widgets\StatsOverviewWidget\Card;

class LifePercentageCards
{
    public static function getCards(): array
    {
        [$percentageLeft, $percentageComplete] = auth()->user()->getDeathPercentage();

        return [Card::make('Life Percentage', "Lived: {$percentageComplete}% | Left: {$percentageLeft}%")];
    }
}
