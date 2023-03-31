<?php

namespace App\Console\Commands;

use App\Enums\DuePeriods;
use App\Enums\TransactionTypes;
use App\Models\ExpectedTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateExpectedTransactionsDueDates extends Command
{
    protected $signature = 'app:update-expected-transactions-due-dates';

    protected $description = 'Updates the next due date for expected transactions that have a due period';

    public function handle(): void
    {
        ExpectedTransaction::whereDuePeriod(DuePeriods::MONTHLY->value)
            ->where(function ($query) {
                $query->whereNull('next_due_date')
                    ->orWhere('next_due_date', '<', Carbon::today());
            })
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->each(function (ExpectedTransaction $expectedTransaction) {
                $expectedTransaction->update([
                    'next_due_date' => $expectedTransaction->getNextDueDate()->toDateString(),
                ]);
            });
    }
}
