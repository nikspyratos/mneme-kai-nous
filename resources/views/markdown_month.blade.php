## {{ $month }} {{ $year }}

@if (count($budgets) > 0)
    ### Budgets
    @foreach ($budgets as $budget)
        {{ $budget->formatted_amount }} {{ $budget->name }}
    @endforeach
@endif
@if (count($expectedExpensesGroups) > 0)

    ### Regular Expenses
    @foreach ($expectedExpensesGroups as $expectedExpensesGroup)
        **{{ $expectedExpensesGroup['title'] }}**
        @foreach ($expectedExpensesGroup['transactions'] as $expectedTransaction)
            @if ($expectedTransaction->type == \App\Enumerations\TransactionTypes::CREDIT)
                +{{ $expectedTransaction->formatted_amount }} {{ $expectedTransaction->name }}
            @else
                {{ $expectedTransaction->formatted_amount }} {{ $expectedTransaction->name }}
            @endif
        @endforeach
        *Total: {{ $expectedExpensesGroup['total'] }}*
    @endforeach
    **Totals**
    *Total: {{ $expectedExpensesSum }}*
    @foreach ($expectedExpensesGroups as $expectedExpensesGroup)
        @if (!$expectedExpensesGroup['required'])
            *Total (w/o {{ $expectedExpensesGroup['title'] }}): {{ $expectedExpensesSum - $expectedExpensesGroup['total'] }}*
        @endif
    @endforeach
    *Total (only required): {{ $expectedExpensesRequiredSum }}*
@endif
@if (count($onceOffExpectedExpenses) > 0)

    ### Expected expenses (one-off):
    @foreach ($onceOffExpectedExpenses as $expectedTransaction)
        {{ $expectedTransaction->formatted_amount }} {{ $expectedTransaction->name }}
    @endforeach
@endif
@if (count($expectedIncomes) > 0)

    ### Expected income/contributions:
    @foreach ($expectedIncomes as $expectedIncome)
        {{ $expectedIncome->formatted_amount }} {{ $expectedIncome->name }}
    @endforeach
    *Total: {{ $expectedIncomesSum }}*
@endif
@if (count($formattedTransactions) > 0)

    ### Transactions
    @foreach ($formattedTransactions as $formattedTransaction)
        {{ $formattedTransaction }}
    @endforeach
@endif
@if (count($accounts) > 0)

    ### Balances
    @foreach ($accounts as $account)
        *{{ $account->name }}: {{ $account->formatted_balance }}*
    @endforeach
@endif
@if (count($tallies) > 0)
    @foreach ($tallies as $tally)
        *{{ $tally->name }}: {{ $tally->balance }} ({{ $tally->getBalancePercentageOfBudget() }})*
    @endforeach
@endif
---
