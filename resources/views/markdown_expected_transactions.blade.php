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
*Total (w/o {{ $expectedExpensesGroup['title'] }}): {{ $expectedExpensesSum - $expectedExpensesGroup['total'] }}*
@endforeach
*Total (only required): {{ $expectedExpensesRequiredSum }}*
@endif
