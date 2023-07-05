<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LoadsheddingSchedule;
use App\Services\EskomSePushApiClient;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateLoadsheddingSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-loadshedding-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates loadshedding schedules with latest data on the stored zones';

    /**
     * Execute the console command.
     */
    public function handle(EskomSePushApiClient $eskomSePushApiClient): void
    {
        $schedules = LoadsheddingSchedule::whereEnabled(true);
        foreach ($schedules as $schedule) {
            $schedule->data = $eskomSePushApiClient->getZoneData($schedule->api_id);
            $events = collect($schedule->data['events']);
            $today = Carbon::today();
            $todayTimes = [];
            foreach ($events as $event) {
                $start = Carbon::createFromFormat(DateTime::ATOM, $event['start']);
                $end = Carbon::createFromFormat(DateTime::ATOM, $event['end']);
                if ($start->day == $today->day) {
                    $todayTimes[] = $start->toTimeString('minute') . ' - ' . $end->toTimeString('minute');
                }
            }
            $schedule->today_times = $todayTimes;
            $schedule->save();
        }
    }
}
