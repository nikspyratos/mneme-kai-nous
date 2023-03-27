<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankZeroTransactionSheetImport implements ToModel, WithHeadingRow
{
    public function __construct(
        private Account $account,
    ) {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        //https://stackoverflow.com/a/26149838/5952111
        $excelDateToRealDate = Carbon::today()
            ->setYear(1899)
            ->setMonth(12)
            ->setDay(30)
            ->addDays($row['date']);

        return new Transaction([
            'account_id' => $this->account->id,
            'expected_transaction_id' => null,
            'budget_id' => null,
            'tally_id' => null,
            'date' => $excelDateToRealDate,
            'type' => $row['type'],
            'description' => $row['description_1'],
            'detail' => $row['description_2'],
            'currency' => $this->account->currency,
            'amount' => $row['amount'],
            'fee' => $row['fee'],
            'listed_balance' => $row['balance'],
        ]);
    }
}
