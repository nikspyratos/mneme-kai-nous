<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use Illuminate\Support\Carbon;

class LifePercentage extends PieChartWidget
{
    protected static ?string $heading = 'Death percentage';

    protected function getData(): array
    {
        /** @var Carbon $birthdate */
        $birthdate = auth()->user()->birthdate;
        $currentDate = Carbon::today();
        $weeksPassed = $currentDate->diffInWeeks($birthdate);
        $percentageComplete = round(($weeksPassed / 3900) * 100, 2);
        $percentageLeft = round(((3900 - $weeksPassed) / 3900) * 100, 2);

        return [
            'datasets' => [
                [
                    'label' => 'How much time is left? Assuming a 75-year age.',
                    'data' => [$percentageLeft, $percentageComplete],
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'],
                ],
            ],
            'labels' => ['Left', 'Complete'],
        ];
    }
}
