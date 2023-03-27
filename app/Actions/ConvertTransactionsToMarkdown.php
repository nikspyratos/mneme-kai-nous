<?php

namespace App\Actions;

use App\Enums\ExpenseGroups;
use App\Enums\TransactionTypes;
use App\Models\Account;
use App\Models\Budget;
use App\Models\ExpectedTransaction;
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

        $expectedExpenses = ExpectedTransaction::whereEnabled(true)
            ->whereNotNull('due_period')
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->get();
        $onceOffExpectedExpenses = ExpectedTransaction::whereEnabled(true)
            ->whereNull('due_period')
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->whereBetween('next_due_date', [$startDate, $endDate])
            ->get();
        $expectedIncomes = ExpectedTransaction::whereEnabled(true)
            ->where('type', '=', TransactionTypes::CREDIT->value)
            ->get();

        $expectedExpensesSum = ($expectedExpenses->sum('amount') / 100) + ($onceOffExpectedExpenses->sum('amount') / 100);
        $expectedIncomesSum = $expectedIncomes->sum('amount') / 100;

        $expectedExpensesGroups = [];

        foreach ($expectedExpenses as $expectedTransaction) {
            $expectedExpensesGroups[$expectedTransaction->group]['title'] = $expectedTransaction->group;
            $expectedExpensesGroups[$expectedTransaction->group]['transactions'][] = $expectedTransaction;
            if (isset($expectedExpensesGroups[$expectedTransaction->group]['total'])) {
                $expectedExpensesGroups[$expectedTransaction->group]['total'] += $expectedTransaction->amount / 100;
            } else {
                $expectedExpensesGroups[$expectedTransaction->group]['total'] = $expectedTransaction->amount / 100;
            }
            if (! isset($expectedExpensesGroups[$expectedTransaction->group]['required'])) {
                $expectedExpensesGroups[$expectedTransaction->group]['required'] = ExpenseGroups::from($expectedTransaction->group)->isRequired();
            }
        }

        $expectedExpensesRequiredSum = $expectedExpensesSum;
        foreach ($expectedExpensesGroups as $expectedExpensesGroup) {
            if (! $expectedExpensesGroup['required']) {
                $expectedExpensesRequiredSum -= $expectedExpensesGroup['total'];
            }
        }

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
