<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\AccountTypes;
use App\Enums\Banks;
use App\Enums\Currencies;
use App\Enums\InvestecTransactionTypes;
use App\Enums\TransactionTypes;
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
    public function createTransactionFromInvestec(Request $request)
    {
        // NOTE: API access fetches non-card transactions
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
                'type' => TransactionTypes::DEBIT->value,
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
                    'account' => ! empty($account) ? $account->name : 'Unknown',
                    'data' => $request->all(),
                ]
            );
        }

        return response()->json();
    }

    public function createTransactionFromSms(Request $request)
    {
        request()->validate([
            'content' => 'required',
            'timestamp' => 'required',
        ], $request->all());
        Log::debug('Incoming SMS', ['content' => $request->input('content'), 'timestamp' => $request->input('timestamp')]);

        if (Str::startsWith($request->input('content'), 'WFS :')) {
            $smsDetails = explode(', ', $request->input('content'));
            if (count($smsDetails) >= 5) {
                //e.g. SPYRAN 003
                $accountIdentifier = explode('; ', $request->input('content'))[1];

                $account = Account::where('name', 'LIKE', '%Woolworths%')
                    ->orWhere(function ($query) use ($accountIdentifier) {
                        $query->where('name', 'LIKE', '%Woolworths%')
                            ->where('name', 'LIKE', "%{$accountIdentifier}");
                    })
                    ->first();
                if (! $account) {
                    $account = Account::create(
                        [
                            'account_number' => null,
                            'bank_name' => Banks::ABSA->value,
                            'currency' => Currencies::RANDS->value,
                            'name' => 'Woolworths Credit ' . $accountIdentifier,
                            'type' => AccountTypes::CREDIT->value,
                            'balance' => 50000_00,
                            'debt' => 50000_00,
                        ],
                    );
                }

                $isTransaction = false;
                if ($smsDetails[1] == 'Pur') {
                    $isTransaction = true;
                    $type = TransactionTypes::DEBIT;
                    //e.g. 29/03/23 - d/m/Y
                    $date = Carbon::createFromFormat('d/m/y', explode(' ', $smsDetails[2])[0]);
                    $description = $smsDetails[3];
                    //e.g. R468.00
                    $amount = $this->amountInCents(Str::substr($smsDetails[4], 1));
                    //e.g. Total Avail Bal R42,350.00.
                    $balance = $this->amountInCents(explode(' ', $smsDetails[5])[3]);
                } elseif (Str::contains($smsDetails[1], 'Transf.')) {
                    $isTransaction = true;
                    $type = TransactionTypes::CREDIT;
                    //e.g. Transf. 19/03/23 INTERNAL FUNDS TRANSFER
                    $dateAndDescription = explode(' ', $smsDetails[1]);
                    $date = Carbon::createFromFormat('d/m/y', $dateAndDescription[1]);
                    $description = implode(' ', array_slice($dateAndDescription, 2));
                    //e.g. R468.00
                    $amount = $this->amountInCents($smsDetails[3]);
                    //e.g. Total Avail Bal R42,350.00.
                    $balance = $this->amountInCents(explode(' ', $smsDetails[4])[3]);
                }

                if ($isTransaction) {
                    Transaction::create([
                        'account_id' => $account->id,
                        'expected_transaction_id' => null,
                        'budget_id' => null,
                        'tally_id' => null,
                        'date' => $date,
                        'type' => $type->value,
                        'category' => null,
                        'description' => $description,
                        'currency' => $account->currency,
                        'amount' => $amount,
                        'fee' => null,
                        'listed_balance' => $balance,
                        'data' => $request->all(),
                    ]);
                    $account->update(['balance' => $balance]);
                } else {
                    Log::error('WFS SMS contents not recognised', ['content' => $request->input('content')]);
                }
            }
        }

        return response()->json();
    }

    public function createTransactionFromPush(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'message' => 'required',
            'timestamp' => 'required',
            'bank' => 'required|in:' . implode(',', array_column(Banks::cases(), 'value')),
        ], $request->all());

        if ($request->input('bank') == Banks::BANKZERO->value) {
            $bankZeroTransactionPushTitles = [
                'Nik: Card Online',
            ];
            if (in_array($request->input('title'), $bankZeroTransactionPushTitles)) {
                $account = Account::firstWhere('bank_name', Banks::BANKZERO->value);
                if (! $account) {
                    $account = Account::create([
                        'account_number' => null,
                        'bank_name' => Banks::BANKZERO->value,
                        'currency' => Currencies::RANDS->value,
                        'name' => 'Bank Zero',
                        'type' => AccountTypes::TRANSACTIONAL->value,
                        'balance' => 0,
                        'debt' => 0,
                    ]);
                }

                $transactionDetails = explode(', ', $request->input('message'));
                $amountAndFirstDescriptionHalf = explode(' ', $transactionDetails[0]);
                $amount = $this->amountInCents($amountAndFirstDescriptionHalf[0]);
                $secondDescriptionHalfAndDate = explode(' ', $transactionDetails[1]);
                $description = $amountAndFirstDescriptionHalf[1] . ' ' . $secondDescriptionHalfAndDate[0];
                $dateDay = $secondDescriptionHalfAndDate[2];
                $dateMonthYear = $secondDescriptionHalfAndDate[3];
                $date = Carbon::createFromFormat('d-M-y', "{$dateDay}-{$dateMonthYear}");
                $balance = $this->amountInCents(explode(' ', $transactionDetails[2])[1]) * 100;

                Transaction::create([
                    'account_id' => $account->id,
                    'expected_transaction_id' => null,
                    'budget_id' => null,
                    'tally_id' => null,
                    'date' => $date,
                    'type' => TransactionTypes::DEBIT->value,
                    'category' => null,
                    'description' => $description,
                    'currency' => $account->currency,
                    'amount' => $amount,
                    'fee' => null,
                    'listed_balance' => $balance,
                    'data' => $request->all(),
                ]);
                $account->update(['balance' => $balance]);
            }
        }

        return response()->json();
    }

    private function amountInCents(string $money): int
    {
        return (int) Str::replace(['R', '.', ','], '', $money);
    }
}
