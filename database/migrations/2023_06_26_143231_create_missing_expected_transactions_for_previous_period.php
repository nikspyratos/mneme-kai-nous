<?php

declare(strict_types=1);

use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    //NOTE: This is intended to run for the budget period of June-July to create backdated ETs
    // for May-June as I did 0 categorisation in that month

    public function up(): void
    {
        $endDate = TallyRolloverDateCalculator::getPreviousDate();
        $expectedTransactionTemplates = ExpectedTransactionTemplate::whereEnabled(true)->get();
        foreach ($expectedTransactionTemplates as $template) {
            ExpectedTransaction::create(
                array_merge(
                    Arr::only($template->toArray(), get_fillable(ExpectedTransaction::class)),
                    [
                        'name' => $template->name . ': ' . $endDate->monthName . ' ' . $endDate->year,
                        'next_due_date' => $endDate,
                        'is_paid' => false,
                    ]
                )
            );
        }
    }

    public function down(): void
    {
        //Nah
    }
};
