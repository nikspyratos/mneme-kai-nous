<?php

namespace App\Exports\Sheets;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class IncomeSheet implements FromQuery, WithTitle
{
    public function __construct(private ?Carbon $startDate = null, private ?Carbon $endDate = null)
    {
    }

    public function query()
    {
        return Transaction::taxRelevant($this->startDate, $this->endDate)->whereType(TransactionType::CREDIT);
    }

    public function title(): string
    {
        return 'Income';
    }
}
