<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class TallyRolloverDateCalculator
{
    public static function getRolloverDay()
    {
        return config('app.financial_month_rollover_day', '25');
    }

    public static function getPreviousDate()
    {
        $rolloverDay = self::getRolloverDay();
        if (Carbon::today()->day > $rolloverDay) {
            $rolloverDate = Carbon::today()->setDay($rolloverDay);
        } else {
            $rolloverDate = Carbon::today()->subMonth()->setDay($rolloverDay);
        }
        if ($rolloverDate->isWeekend()) {
            $rolloverDate = $rolloverDate->previousWeekday();
        }

        return $rolloverDate;
    }

    public static function getNextDate()
    {
        $rolloverDay = self::getRolloverDay();
        if (Carbon::today()->day > $rolloverDay) {
            $rolloverDate = Carbon::today()->addMonth()->setDay($rolloverDay);
        } else {
            $rolloverDate = Carbon::today()->setDay($rolloverDay);
        }
        if ($rolloverDate->isWeekend()) {
            $rolloverDate = $rolloverDate->previousWeekday();
        }

        return $rolloverDate;
    }
}
