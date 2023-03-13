<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BankZeroImport implements ToModel, WithMultipleSheets
{
    public function __construct(
        private Account $account,
    ) {
    }

    public function sheets(): array
    {
        return [
            1 => new BankZeroTransactionSheetImport,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Transaction([
            'account_id' => $this->account->id,
            'expected_transaction_id' => null,
            'budget_id' => null,
            'tally_id' => null,
            'date' => Carbon::createFromFormat('Y-m-d H:i', $row['Date'] . ' ' . $row['Time']),
            'type' => $row['Type'],
            'description' => $row['Description 1'],
            'detail' => $row['Description 2'],
            'currency' => $this->account->currency,
            'amount' => $row['Amount'],
            'fee' => $row['Fee'],
            'listed_balance' => $row['Balance'],
        ]);
    }
}
