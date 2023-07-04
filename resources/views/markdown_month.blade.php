## {{ $month }} {{ $year }}

@if (count($budgets) > 0)
### Budgets
@foreach ($budgets as $budget)
{{ $budget->formatted_amount }} {{ $budget->name }}
@endforeach
@endif

@include('markdown_expected_transactions', ['expectedExpensesGroups', 'expectedExpensesSum'])

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
