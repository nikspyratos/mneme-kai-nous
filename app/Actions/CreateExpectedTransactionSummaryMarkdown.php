<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enumerations\ExpenseGroups;
use App\Enumerations\TransactionTypes;
use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateExpectedTransactionSummaryMarkdown
{
    use AsAction;

    public function handle(Carbon $date)
    {
        $startDate = TallyRolloverDateCalculator::getPreviousDate(clone $date);
        $endDate = TallyRolloverDateCalculator::getNextDate(clone $date);
        $expectedExpenses = ExpectedTransactionTemplate::whereEnabled(true)
            ->whereNotNull('due_period')
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->get();
        $onceOffExpectedExpenses = ExpectedTransaction::whereEnabled(true)
            ->whereNull('due_period')
            ->where('type', '=', TransactionTypes::DEBIT->value)
            ->whereBetween('next_due_date', [$startDate, $endDate])
            ->get();
        $expectedIncomes = ExpectedTransactionTemplate::whereEnabled(true)
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
            $expectedExpensesRequiredSum -= $expectedExpensesGroup['total'];
        }

        return [
            'expectedExpenses' => $expectedExpenses,
            'expectedExpensesGroups' => $expectedExpensesGroups,
            'expectedExpensesRequiredSum' => $expectedExpensesRequiredSum,
            'expectedExpensesSum' => $expectedExpensesSum,
            'expectedIncomes' => $expectedIncomes,
            'expectedIncomesSum' => $expectedIncomesSum,
            'onceOffExpectedExpenses' => $onceOffExpectedExpenses,
        ];
    }
}
