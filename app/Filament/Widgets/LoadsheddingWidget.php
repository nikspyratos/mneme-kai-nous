<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\LoadsheddingSchedule;
use Filament\Widgets\Widget;

class LoadsheddingWidget extends Widget
{
    protected static string $view = 'filament.widgets.loadshedding-widget';

    public function getViewData(): array
    {
        $schedules = LoadsheddingSchedule::whereNotNull('today_times')->get();

        return [
            'schedules' => $schedules,
        ];
    }
}
