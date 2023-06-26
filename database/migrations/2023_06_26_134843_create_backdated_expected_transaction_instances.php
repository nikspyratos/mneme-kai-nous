<?php

declare(strict_types=1);

use App\Models\ExpectedTransaction;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $expectedTransactions = ExpectedTransaction::with(['transactions', 'budget'])->get();
        foreach ($expectedTransactions as $expectedTransaction) {
            foreach ($expectedTransaction->transactions as $transaction) {
                $tallyId = null;
                $periodStartDate = TallyRolloverDateCalculator::getPreviousDate($transaction->created_at);
                $periodEndDate = TallyRolloverDateCalculator::getNextDate($transaction->created_at);
                if ($expectedTransaction->budget) {
                    $tallyId = $expectedTransaction
                        ->budget
                        ->tallies()
                        ->whereStartDate($periodStartDate)
                        ->whereEndDate($periodEndDate)
                        ->first()
                        ?->id;
                }
                $backdatedExpectedTransaction = ExpectedTransaction::create(
                    array_merge(
                        $expectedTransaction->toArray(),
                        [
                            'name' => $expectedTransaction->name . ': ' . $periodEndDate->monthName . ' ' . $periodEndDate->year,
                            'tally_id' => $tallyId,
                            'next_due_date' => $transaction->date,
                            'is_paid' => true,
                        ]
                    )
                );
                $backdatedExpectedTransaction->transactions()->attach($transaction->id);
                $expectedTransaction->transactions()->detach([$transaction->id]);
            }
            $expectedTransaction->delete(); //This old template now has incorrect data according to new format
        }
    }

    public function down(): void
    {
    }
};
