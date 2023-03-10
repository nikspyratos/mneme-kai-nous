<?php

namespace App\Console\Commands;

use App\Enums\BudgetPeriodTypes;
use App\Models\Budget;
use App\Models\Tally;
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
        if (Carbon::today()->day == $nextRolloverDate->day) {
            $budgets = Budget::wherePeriodType(BudgetPeriodTypes::MONTHLY);
            $nextMonthDay = Carbon::today()->addMonth()->setDay(TallyRolloverDateCalculator::getRolloverDay());
            if ($nextMonthDay->isWeekend()) {
                $nextMonthDay = $nextMonthDay->previousWeekday();
            }
            $budgets->each(function ($budget) use ($nextMonthDay) {
                Tally::create([
                    'budget_id' => $budget->id,
                    'name' => $budget->name,
                    'currency' => $budget->currency,
                    'balance' => 0,
                    'start_date' => Carbon::today(),
                    'end_date' => $nextMonthDay,
                ]);
            });
            //TODO: LogSnag notification of rollover
        }
    }
}
