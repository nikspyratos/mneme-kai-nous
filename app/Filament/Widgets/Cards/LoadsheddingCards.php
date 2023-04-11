<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Cards;

use App\Models\LoadsheddingSchedule;
use Filament\Widgets\StatsOverviewWidget\Card;

class LoadsheddingCards
{
    public static function getCards(): array
    {
        $cards = [];
        $schedules = LoadsheddingSchedule::whereNotNull('today_times')->get();
        $content = '';
        foreach ($schedules as $schedule) {
            $cards[] = Card::make($schedule->name . ' Loadshedding', $schedule->today_times_formatted);
        }

        return $cards;
    }
}
