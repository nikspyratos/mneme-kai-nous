<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Cards\LifePercentageCards;
use App\Filament\Widgets\Cards\LoadsheddingCards;
use App\Filament\Widgets\Cards\QuoteCards;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;

class GeneralWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getCards(): array
    {
        return array_merge(
            LifePercentageCards::getCards(),
            QuoteCards::getCards(),
            LoadsheddingCards::getCards()
        );
    }
}