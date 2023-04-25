<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enumerations\AccountTypes;
use App\Enumerations\Currencies;
use App\Enumerations\TransactionTypes;
use App\Models\Account;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use App\Services\TallyRolloverDateCalculator;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateTotalSpendDue
{
    use AsAction;

    public function handle()
    {
        $tallies = Tally::forCurrentBudgetMonth()->whereCurrency(Currencies::RANDS->value)->get();
        $creditAccounts = Account::whereType(AccountTypes::CREDIT->value)->whereCurrency(Currencies::RANDS->value)->get();
        $expectedExpenses = ExpectedTransaction::whereType(TransactionTypes::DEBIT->value)
            ->whereCurrency(Currencies::RANDS->value)
            ->whereBetween(
                'next_due_date',
                [
                    TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                ]
            )
            ->whereIsPaid(false)
            ->get();
        $talliesTotal = 0;
        foreach ($tallies as $tally) {
            if ($tally->balance < $tally->limit) {
                $talliesTotal += $tally->limit - $tally->balance;
            }
        }
        $creditTotal = ($creditAccounts->sum('debt') - $creditAccounts->sum('balance'));
        $expectedExpensesTotal = $expectedExpenses->sum('amount');

        return $talliesTotal + $creditTotal + $expectedExpensesTotal;
    }
}
