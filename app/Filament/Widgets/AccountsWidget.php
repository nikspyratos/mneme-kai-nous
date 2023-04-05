<?php

namespace App\Filament\Widgets;

use App\Enums\AccountTypes;
use App\Enums\Currencies;
use App\Enums\TransactionTypes;
use App\Models\Account;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use App\Services\TallyRolloverDateCalculator;
use Brick\Money\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AccountsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $data = [];
        $accounts = Account::whereIsPrimary(true)->get();
        $accounts->each(function ($account) use (&$data) {
            $name = $account->type . ': ' . $account->name;
            $content = $account->formatted_balance;
            $description = $account->bank_name;
            $color = 'success';
            if ($account->type == AccountTypes::DEBT->value) {
                $content = $account->formatted_debt_balance . ' / ' . $account->formatted_debt;
                $description .= ' | Paid off: ' . $account->debt_paid_off_percentage . '%';
            } elseif ($account->type == AccountTypes::CREDIT->value) {
                $availableCreditPercentage = $account->available_credit_percentage;
                $content = $account->formatted_balance . ' / ' . $account->formatted_debt;
                $description .= ' | Available: ' . $availableCreditPercentage . '% | Pay: ' . $account->formatted_debt_balance;
                if ($availableCreditPercentage <= 75 && $availableCreditPercentage > 50) {
                    $color = 'warning';
                } elseif ($availableCreditPercentage < 50) {
                    $color = 'danger';
                }
            }
            if ($account->has_overdraft) {
                $description .= ' | Overdraft: ' . $account->formatted_overdraft_amount;
                if ($account->balance <= 0 && abs($account->balance) > $account->overdraft_amount) {
                    $color = 'danger';
                }
            }
            if ($account->is_main) {
                $name = 'Main ' . $name;
                $description .= ' | Total spend due: ' . Money::of($this->getTotalSpendDueInCents() / 100, Currencies::RANDS->value)->formatTo('en_ZA');
            }
            $card = Card::make($name, $content)
                ->description($description)
                ->color($color);
            $data[] = $card;
        });

        return $data;
    }

    private function getTotalSpendDueInCents()
    {
        $tallies = Tally::forCurrentBudgetMonth()->whereCurrency(Currencies::RANDS->value)->get();
        $creditAccounts = Account::whereType(AccountTypes::CREDIT->value)->whereCurrency(Currencies::RANDS->value)->get();
        $expectedExpenses = ExpectedTransaction::whereType(TransactionTypes::DEBIT->value)
            ->whereCurrency(Currencies::RANDS->value)
            ->whereBetween(
                'next_due_date',
                [
                    TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                ]
            )
            ->whereIsPaid(false)
            ->get();
        $talliesTotal = ($tallies->sum('limit') - $tallies->sum('balance'));
        $creditTotal = ($creditAccounts->sum('debt') - $creditAccounts->sum('balance'));
        $expectedExpensesTotal = $expectedExpenses->sum('amount');

        return $talliesTotal + $creditTotal + $expectedExpensesTotal;
    }
}
