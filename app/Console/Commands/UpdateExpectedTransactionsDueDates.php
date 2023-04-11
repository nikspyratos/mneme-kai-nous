<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DuePeriods;
use App\Enums\TransactionTypes;
use App\Models\ExpectedTransaction;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Console\Command;

class UpdateExpectedTransactionsDueDates extends Command
{
    protected $signature = 'app:update-expected-transactions-due-dates';

    protected $description = 'Updates the next due date for expected transactions that have a due period';

    public function handle(): void
    {
        $expectedTransactions = ExpectedTransaction::whereDuePeriod(DuePeriods::MONTHLY->value)
            ->whereIsPaid(true)
            ->where(function ($query) {
                $query->whereNull('next_due_date')
                    ->orWhere('next_due_date', '<', TallyRolloverDateCalculator::getNextDate());
            })
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->get();
        /** @var ExpectedTransaction $expectedTransaction */
        foreach ($expectedTransactions as $expectedTransaction) {
            if ($expectedTransaction->transactions()->inCurrentBudgetMonth()->exists()) {
                $expectedTransaction->update([
                    'next_due_date' => $expectedTransaction->getNextDueDate()?->toDateString(),
                    'is_paid' => false,
                ]);
            }
        }
    }
}
