<?php

namespace App\Console;

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
        $schedule->command('telescope:prune --hours=48')->monthly();
        $schedule->command('app:rollover-budget-month')->daily();
        //TODO Perhaps change to hourly/every X hours schedule
        $schedule->command('app:update-investec-accounts')->daily();
        $schedule->command('app:update-loadshedding-schedules')->daily();
        $schedule->command('app:send-logsnag-report')->dailyAt('07:00');
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
