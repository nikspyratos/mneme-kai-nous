<?php

namespace App\Filament\Widgets;

use App\Models\Tally;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;

class TalliesWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $data = [];
        $tallies = Tally::forCurrentMonth()->get();
        $tallies->each(function ($tally) use (&$data) {
            $percentage = $tally->getBalancePercentageOfBudget();
            if ($percentage < 50) {
                $color = 'success';
            } elseif ($percentage < 75) {
                $color = 'warning';
            } else {
                $color = 'danger';
            }
            $card = Card::make(
                $tally->name . ' Balance',
                $tally->formatted_balance . ' / ' . $tally->formatted_limit
            )
            ->description("$percentage%")
            ->color($color);
            $data[] = $card;
        });

        return $data;
    }
}
