<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Account;
use App\Models\Budget;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class ConvertTransactionsToMarkdown
{
    use AsAction;

    public function handle(Carbon $date)
    {
        $startDate = TallyRolloverDateCalculator::getPreviousDate(clone $date);
        $endDate = TallyRolloverDateCalculator::getNextDate(clone $date);

        $accounts = Account::all();
        $budgets = Budget::whereEnabled(true)->get();
        $tallies = [];
        foreach ($budgets as $budget) {
            $tallies[] = $budget->currentTally();
        }
        $tallies = array_filter($tallies);

        [
            $expectedExpenses,
            $expectedExpensesGroups,
            $expectedExpensesRequiredSum,
            $expectedExpensesSum,
            $expectedIncomes,
            $expectedIncomesSum,
            $onceOffExpectedExpenses
        ] = CreateExpectedTransactionSummaryMarkdown::run($date);

        $formattedTransactions = [];
        foreach ($accounts as $account) {
            if ($account->transactions()->whereBetween('date', [$startDate, $endDate])->count() > 0) {
                $transactions = $account->transactions()->whereBetween('date', [$startDate, $endDate])->orderBy('date')->get();
                foreach ($transactions as $transaction) {
                    $formattedTransactions[] = sprintf(
                        '%s (%s) %s %s',
                        $transaction->date->format('dM'),
                        $account->name,
                        $transaction->formatted_amount,
                        $transaction->description
                    );
                }
            }
        }

        return view(
            'markdown_month',
            [
                'accounts' => $accounts,
                'budgets' => $budgets,
                'tallies' => $tallies,
                'expectedExpenses' => $expectedExpenses,
                'expectedExpensesSum' => $expectedExpensesSum,
                'expectedExpensesRequiredSum' => $expectedExpensesRequiredSum,
                'expectedIncomes' => $expectedIncomes,
                'expectedIncomesSum' => $expectedIncomesSum,
                'onceOffExpectedExpenses' => $onceOffExpectedExpenses,
                'expectedExpensesGroups' => $expectedExpensesGroups,
                'formattedTransactions' => $formattedTransactions,
                'month' => $date->monthName,
                'year' => $date->year,
            ]
        )->render();
    }
}
