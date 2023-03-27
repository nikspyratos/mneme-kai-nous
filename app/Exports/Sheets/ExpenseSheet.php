<?php

namespace App\Exports\Sheets;

use App\Enums\TransactionTypes;
use App\Models\Transaction;
use Brick\Money\Money;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseSheet implements FromQuery, WithTitle, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithDefaultStyles
{
    private $total;

    public function __construct(private ?Carbon $startDate = null, private ?Carbon $endDate = null)
    {
    }

    public function query()
    {
        return Transaction::taxRelevant($this->startDate, $this->endDate)->whereType(TransactionTypes::DEBIT->value);
    }

    public function title(): string
    {
        return 'Expenses';
    }

    public function headings(): array
    {
        return [
            'Date',
            'Amount',
            'Description',
            'Total',
        ];
    }

    public function map($row): array
    {
        $this->total += $row->amount / 100;

        return [
            $row->date->toDateString(),
            $row->formatted_amount,
            $row->description,
            Money::of($this->total, $row->currency)->formatTo('en_ZA'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return ['font' => ['size' => 12]];
    }
}
