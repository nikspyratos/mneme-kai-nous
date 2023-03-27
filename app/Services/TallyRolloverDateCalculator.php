<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class TallyRolloverDateCalculator
{
    public static function getRolloverDay()
    {
        return config('app.financial_month_rollover_day', '25');
    }

    public static function getPreviousDate(Carbon $date = null)
    {
        $rolloverDay = self::getRolloverDay();
        if (! $date) {
            $date = Carbon::today();
        }
        if ($date->day > $rolloverDay) {
            $rolloverDate = $date->setDay($rolloverDay);
        } else {
            $rolloverDate = $date->subMonth()->setDay($rolloverDay);
        }
        if ($rolloverDate->isWeekend()) {
            $rolloverDate = $rolloverDate->previousWeekday();
        }

        return $rolloverDate;
    }

    public static function getNextDate(Carbon $date = null)
    {
        $rolloverDay = self::getRolloverDay();
        if (! $date) {
            $date = Carbon::today();
        }
        if ($date->day > $rolloverDay) {
            $rolloverDate = $date->addMonth()->setDay($rolloverDay);
        } else {
            $rolloverDate = $date->setDay($rolloverDay);
        }
        if ($rolloverDate->isWeekend()) {
            $rolloverDate = $rolloverDate->previousWeekday();
        }

        return $rolloverDate;
    }
}
