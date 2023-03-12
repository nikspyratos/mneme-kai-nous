<?php

namespace App\Console\Commands;

use App\Enums\Banks;
use App\Enums\Currencies;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\InvestecApiClient;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UpdateInvestecAccounts extends Command
{
    protected $signature = 'app:update-investec-accounts
    {--from=}
    {--to=}';

    protected $description = 'Retrieves latest balance & transactions for each Investec account';

    public function handle(InvestecApiClient $investecApiClient): void
    {
        $transactionsFrom = Carbon::today()->subDay()->format('Y-m-d');
        $transactionsTo = Carbon::today()->addDay()->format('Y-m-d');
        try {
            if ($this->hasOption('from')) {
                $transactionsFrom = Carbon::createFromFormat('Y-m-d', $this->option('from'))->format('Y-m-d');
            }
            if ($this->hasOption('to')) {
                $transactionsTo = Carbon::createFromFormat('Y-m-d', $this->option('to'))->format('Y-m-d');
            }
        } catch (InvalidFormatException $e) {
        }
        // Get API accounts and their balances
        $this->info('Fetching Investec accounts from API...');
        $investecAccounts = collect($investecApiClient->getAccounts());
        $investecAccountsBalances = collect();
        $investecAccounts->each(function ($investecAccount) use ($investecApiClient, &$investecAccountsBalances) {
            $investecAccountsBalances->push($investecApiClient->getAccountBalance($investecAccount['accountId']));
        });

        // Get local accounts
        $this->info('Fetching Investec accounts from database...');
        /** @var Collection $accounts */
        $accounts = Account::whereBankName(Banks::INVESTEC->value)
            ->whereIn('bank_identifier', $investecAccounts->pluck('accountId'))
            ->get();

        // Create missing accounts
        $this->info('Reconciling missing accounts...');
        if ($investecAccounts->count() !== $accounts->count()) {
            $investecAccounts->each(function ($investecAccount) use ($investecAccountsBalances, &$accounts) {
                $balanceData = $investecAccountsBalances->firstWhere('accountId', $investecAccount['accountId']);
                $newAccount = Account::firstOrCreateInvestec(
                    $investecAccount['accountNumber'],
                    $balanceData['currency'] ?? Currencies::RANDS->value,
                    $balanceData['currentBalance'] * 100,
                    $investecAccount['accountId'],
                    $investecAccount['accountName'] . ' ' . $investecAccount['productName'],
                    collect($investecAccount)->except(['accountName', 'productName', 'accountNumber', 'accountId'])->toArray()
                );
                $accounts->push($newAccount);
                $this->info('Created new account: ' . $newAccount->name);
            });
        }

        // Sync bank identifier, balance, and create/update transactions
        $accounts->each(function ($account) use ($investecApiClient, $investecAccounts, $investecAccountsBalances, $transactionsFrom, $transactionsTo) {
            $this->info('Updating bank accounts and transactions for account ' . $account->name . '...');
            if (! $account->bank_identifier) {
                $account->bank_identifier = $investecAccounts->firstWhere('accountNumber', $account->account_number)['accountId'];
            }
            $account->balance = $investecAccountsBalances->firstWhere('accountId', $account->bank_identifier)['currentBalance'] * 100;
            $account->save();
            $investecTransactions = collect($investecApiClient->getTransactions($account->bank_identifier, $transactionsFrom, $transactionsTo));
            $investecTransactions->each(function ($investecTransaction) use ($account) {
                Transaction::updateOrCreate(
                    [
                        'account_id' => $account->id,
                        'date' => Carbon::createFromFormat('Y-m-d', $investecTransaction['transactionDate'])
                            ->setHour(0)
                            ->setMinute(0)
                            ->setSecond(0),
                        'category' => $investecTransaction['transactionType'],
                        'description' => $investecTransaction['description'],
                        'currency' => $account->currency,
                        'amount' => $investecTransaction['amount'] * 100,
                    ],
                    [
                        'listed_balance' => $investecTransaction['runningBalance'] * 100,
                        'data' => collect($investecTransaction)
                            ->except(['transactionDate', 'transactionType', 'description', 'amount', 'runningBalance'])
                            ->toArray(),
                    ]
                );
            });
        });
        $this->info('Complete!');
    }
}
