<?php

namespace App\Filament\Widgets;

use App\Actions\CalculateTotalSpendDue;
use App\Enums\AccountTypes;
use App\Enums\Currencies;
use App\Models\Account;
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
                $description .= ' | Total spend due: ' . Money::of(CalculateTotalSpendDue::run() / 100, Currencies::RANDS->value)->formatTo('en_ZA');
            }
            $card = Card::make($name, $content)
                ->description($description)
                ->color($color);
            $data[] = $card;
        });

        return $data;
    }
}
