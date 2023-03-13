<?php

namespace App\Http\Controllers\Api;

use App\Enums\Currencies;
use App\Enums\InvestecTransactionTypes;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        // NOTE: API access fetches non-card transactions,
        // so for my purposes of budget tracking this endpoint is superfluous.
        try {
            $account = Account::firstOrCreateInvestec(
                $request->input('accountNumber'),
                Str::upper($request->input('currencyCode') ?? Currencies::RANDS->value)
            );
            $transaction = Transaction::create([
                'account_id' => $account->id,
                'expected_transaction_id' => null,
                'budget_id' => null,
                'tally_id' => null,
                'date' => Carbon::parse($request->input('dateTime')) //Need to homogenise the time because the bank API only gives date
                    ->setHour(0)
                    ->setMinute(0)
                    ->setSecond(0),
                'category' => InvestecTransactionTypes::CARD->value,
                'description' => $request->input('reference'),
                'currency' => Str::upper($request->input('currencyCode')),
                'amount' => $request->input('centsAmount'),
                'fee' => null,
                'listed_balance' => null,
                'data' => $request->except(['accountNumber', 'currencyCode', 'dateTime', 'reference', 'centsAmount']),
            ]);
            $account->updateBalance($transaction->amount);
        } catch (Exception $e) {
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
