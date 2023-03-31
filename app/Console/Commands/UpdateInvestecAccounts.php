<?php

namespace App\Console\Commands;

use App\Enums\Banks;
use App\Enums\Currencies;
use App\Enums\TransactionTypes;
use App\Models\Account;
use App\Models\Budget;
use App\Models\ExpectedTransaction;
use App\Models\Transaction;
use App\Services\InvestecApiClient;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateInvestecAccounts extends Command
{
    protected $signature = 'app:update-investec-accounts
    {--from=}
    {--to=}';

    protected $description = 'Retrieves latest balance & transactions for each Investec account';

    public function handle(InvestecApiClient $investecApiClient): void
    {
        $transactionsFrom = Carbon::today()->subDay();
        $transactionsTo = Carbon::today()->addDay();
        try {
            if ($this->hasOption('from')) {
                $transactionsFrom = Carbon::createFromFormat('Y-m-d', $this->option('from'));
            }
            if ($this->hasOption('to')) {
                $transactionsTo = Carbon::createFromFormat('Y-m-d', $this->option('to'));
            }
        } catch (InvalidFormatException $e) {
            if ($transactionsFrom->gt($transactionsTo)) {
                $this->error('--from is greater than --to, make sure inputs are correct (either input dates or being a valid date)');
            }
        } finally {
            $transactionsFrom = $transactionsFrom->format('Y-m-d');
            $transactionsTo = $transactionsTo->format('Y-m-d');
        }
        $budgets = Budget::whereEnabled(true)->withCurrentTallies();
        $expectedTransactions = ExpectedTransaction::whereEnabled(true)->get();

        // Get API accounts and their balances
        $this->info('Fetching Investec accounts from API...');
        $investecAccounts = collect($investecApiClient->getAccounts());
        $investecAccountsBalances = collect();
        foreach ($investecAccounts as $investecAccount) {
            $investecAccountsBalances->push($investecApiClient->getAccountBalance($investecAccount['accountId']));
        }

        // Get local accounts
        $this->info('Fetching Investec accounts from database...');
        /** @var Collection $accounts */
        $accounts = Account::whereBankName(Banks::INVESTEC->value)
            ->whereIn('bank_identifier', $investecAccounts->pluck('accountId'))
            ->get();

        // Create missing accounts
        $this->info('Reconciling missing accounts...');
        if ($investecAccounts->count() !== $accounts->count()) {
            foreach ($investecAccounts as $investecAccount) {
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
            }
        }

        // Sync bank identifier, balance, and create/update transactions
        foreach ($accounts as $account) {
            $this->info('Updating bank accounts and transactions for account ' . $account->name . '...');
            if (! $account->bank_identifier) {
                $account->bank_identifier = $investecAccounts->firstWhere('accountNumber', $account->account_number)['accountId'];
            }
            $account->balance = $investecAccountsBalances->firstWhere('accountId', $account->bank_identifier)['currentBalance'] * 100;
            $account->save();
            $investecTransactions = collect($investecApiClient->getTransactions($account->bank_identifier, $transactionsFrom, $transactionsTo));
            foreach ($investecTransactions as $investecTransaction) {
                $identifiedExpectedTransactionId = null;
                $identifiedBudgetId = null;
                $identifiedTallyId = null;
                $isTaxRelevant = false;
                foreach ($expectedTransactions as $expectedTransaction) {
                    if ($expectedTransaction->transactionIsForThis($investecTransaction['description'])) {
                        $identifiedExpectedTransactionId = $expectedTransaction->id;
                        $isTaxRelevant = $expectedTransaction->is_tax_relevant;
                    }
                }
                foreach ($budgets as $budget) {
                    if ($budget->transactionIsForThis($investecTransaction['description'])) {
                        $identifiedBudgetId = $budget->id;
                        $tally = $budget->currentTally();
                        if ($tally) {
                            $identifiedTallyId = $tally->id;
                            $tally->balance += $investecTransaction['amount'] * 100;
                            $tally->save();
                        } else {
                            Log::error('Current tally does not exist for budget ' . $identifiedBudgetId);
                        }
                    }
                }
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
                        'expected_transaction_id' => $identifiedExpectedTransactionId,
                        'budget_id' => $identifiedBudgetId,
                        'tally_id' => $identifiedTallyId,
                        'listed_balance' => $investecTransaction['runningBalance'] * 100,
                        'data' => collect($investecTransaction)
                            ->except(['transactionDate', 'transactionType', 'description', 'amount', 'runningBalance'])
                            ->toArray(),
                        'is_tax_relevant' => $isTaxRelevant,
                        'type' => TransactionTypes::from(Str::ucfirst(Str::lower($investecTransaction['type'])))->value,
                    ]
                );
            }
        }
        $this->info('Complete!');
    }
}
