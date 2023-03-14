<?php

namespace App\Exports\Sheets;

use App\Enums\TransactionTypes;
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
        return Transaction::taxRelevant($this->startDate, $this->endDate)->whereType(TransactionTypes::CREDIT);
    }

    public function title(): string
    {
        return 'Income';
    }
}
