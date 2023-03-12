<?php

namespace App\Http\Controllers\Api;

use App\Enums\AccountType;
use App\Enums\Banks;
use App\Enums\Currencies;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        try {
            $bankName = Banks::INVESTEC->value;
            $account = Account::firstOrCreate([
                'account_number' => $request->input('accountNumber'),
                'bank_name' => $bankName,
                'currency' => Str::upper($request->input('currencyCode') ?? Currencies::RANDS->value),
            ],
            [
                'name' => "$bankName " . $request->input('accountNumber'),
                'type' => AccountType::TRANSACTIONAL->value,
            ]);
            $transaction = Transaction::create([
                'account_id' => $account->id,
                'expense_id' => null,
                'budget_id' => null,
                'tally_id' => null,
                'date' => $request->input('dateTime'),
                'category' => $request->input('merchant.category.name'),
                'description' => $request->input('reference'),
                'detail' => $request->input('merchant.name'),
                'currency' => Str::upper($request->input('currencyCode')),
                'amount' => $request->input('centsAmount'),
                'fee' => null,
                'listed_balance' => null,
                'data' => $request->except(['accountNumber', 'currencyCode', 'dateTime', 'reference', 'centsAmount'])
            ]);
            $account->updateBalance($transaction->amount);
        } catch (\Exception $e) {
            Log::error(self::class . ': ' . $e->getMessage());
        } finally {
            Log::debug(
                'Transaction create request',
                [
                    'account' => $account ?? $account?->name,
                    'data' => $request->all(),
                ]
            );
        }


        return response()->json();
    }
}
