<?php

declare(strict_types=1);

namespace App\Exports;

use App\Exports\Sheets\ExpenseSheet;
use App\Exports\Sheets\IncomeSheet;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionsExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(private ?Carbon $startDate = null, private ?Carbon $endDate = null)
    {
    }

    public function sheets(): array
    {
        $sheets[] = new IncomeSheet($this->startDate, $this->endDate);
        $sheets[] = new ExpenseSheet($this->startDate, $this->endDate);

        return $sheets;
    }
}
