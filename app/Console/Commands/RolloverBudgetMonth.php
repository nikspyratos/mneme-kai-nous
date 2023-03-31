<?php

namespace App\Console\Commands;

use App\Enums\BudgetPeriodTypes;
use App\Models\Budget;
use App\Models\Tally;
use App\Services\LogSnag;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RolloverBudgetMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rollover-budget-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates new tallies for each budget and carry over known balances, for the set month rollover day.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $nextRolloverDate = TallyRolloverDateCalculator::getNextDate();
        $nextMonthDay = Carbon::today()->startOfMonth()->addMonth()->setDay(TallyRolloverDateCalculator::getRolloverDay());
        if ($nextMonthDay->isWeekend()) {
            $nextMonthDay = $nextMonthDay->previousWeekday();
        }
        $budgets = Budget::wherePeriodType(BudgetPeriodTypes::MONTHLY->value);
        $hasCreatedTally = false;
        $budgets->each(function (Budget $budget) use ($nextRolloverDate, $nextMonthDay, &$hasCreatedTally) {
            $this->info('Processing budget: ' . $budget->name);
            $shouldCreateTally = false;
            $startDate = Carbon::today();
            if ($budget->tallies()->forCurrentMonth()->count() == 0) {
                $startDate = TallyRolloverDateCalculator::getPreviousDate();
                $shouldCreateTally = true;
                $this->info('Should create because there is a missing tally.');
            } elseif (Carbon::today()->day == $nextRolloverDate->day) {
                $shouldCreateTally = true;
                $this->info('Should create because today is the rollover day.');
            } else {
                $this->info('Will not create tally.');
                $this->info('Existing tallies: '. $budget->tallies()->forCurrentMonth()->count());
                $this->info('Rollover day: ' . $nextRolloverDate->day . ', today: ' . Carbon::today()->day . '.');
            }
            if ($shouldCreateTally) {
                $tally = Tally::create([
                    'budget_id' => $budget->id,
                    'name' => $budget->name . ' Spent ' . $startDate->toDateString() . ' - ' . $nextMonthDay->toDateString(),
                    'currency' => $budget->currency,
                    'balance' => 0,
                    'limit' => $budget->amount,
                    'start_date' => $startDate,
                    'end_date' => $nextMonthDay,
                ]);
                $this->info('Created Tally: ' . $tally->name);
                $hasCreatedTally = true;
            }
            $this->info('---');
        });
        if ($hasCreatedTally) {
            (new LogSnag)->log('Rollover', 'Budgets rolled over', true);
        }
    }
}
