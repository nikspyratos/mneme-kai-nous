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
        [$percentageLeft, $percentageComplete] = auth()->user()->getDeathPercentage();

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
