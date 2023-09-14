<?php

declare(strict_types=1);

namespace App\Console;

use App\Models\Tally;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:update-investec-accounts')->hourlyAt(30);
        $schedule->call(function () {
            Tally::forCurrentBudgetMonth()->get()->each->calculateBalance();
        })->hourlyAt(35);

        $schedule->command('app:prune-files')->dailyAt('04:00');
//        $schedule->command('app:update-loadshedding-schedules')->dailyAt('04:30');
//        $schedule->command('app:send-logsnag-report')->dailyAt('06:00');

        $schedule->command('telescope:prune --hours=48')->monthly();
//        $schedule->command('app:rollover-budget-month')->monthlyOn(config('app.financial_month_rollover_day'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
