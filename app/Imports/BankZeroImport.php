<?php

namespace App\Imports;

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BankZeroImport implements WithMultipleSheets, SkipsUnknownSheets, WithHeadingRow
{
    public function __construct(
        private Account $account,
        private string $sheetName
    ) {
    }

    public function sheets(): array
    {
        return [
            $this->sheetName => new BankZeroTransactionSheetImport($this->account),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        Log::info(self::class . ': Unknown sheet', [$sheetName]);
    }
}
