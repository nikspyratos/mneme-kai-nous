<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Cards;

use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget\Card;

class QuoteCards
{
    public static function getCards(): array
    {
        $quote = Quote::inRandomOrder()->first();

        return [Card::make('Quote', $quote->content)->description($quote->author)->color('primary')];
    }
}
