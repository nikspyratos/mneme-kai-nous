<?php

namespace App\Filament\Widgets;

use App\Models\Tally;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TalliesWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $data = [];
        $tallies = Tally::forCurrentMonth()->get();
        $tallies->each(function ($tally) use (&$data) {
            $card = Card::make($tally->name . ' Balance', $tally->formattedBalance);
            $data[] = $card;
        });

        return $data;
    }
}
