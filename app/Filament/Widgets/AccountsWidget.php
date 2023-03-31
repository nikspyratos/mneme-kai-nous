<?php

namespace App\Filament\Widgets;

use App\Enums\AccountTypes;
use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AccountsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $data = [];
        $accounts = Account::whereIsPrimary(true)->get();
        $accounts->each(function ($account) use (&$data) {
            $content = $account->formatted_balance;
            $description = $account->bank_name;
            if ($account->isSyncable()) {
                $synced = $account->isBalanceInSyncWithTransactions() ? 'true' : 'false';
                $description .= " | Synced: {$synced}";
            }
            $color = 'success';
            if ($account->type == AccountTypes::DEBT->value) {
                $content = $account->formatted_debt_balance . ' / ' . $account->formatted_debt;
                $description .= ' | Paid off: ' . $account->debt_paid_off_percentage . '%';
                $color = 'success';
            } elseif ($account->type == AccountTypes::CREDIT->value) {
                $availableCreditPercentage = $account->available_credit_percentage;
                $content = $account->formatted_balance . ' / ' . $account->formatted_debt;
                $description .= ' | Available: ' . $availableCreditPercentage . '%';
                if ($availableCreditPercentage <= 75 && $availableCreditPercentage > 50) {
                    $color = 'warning';
                } elseif ($availableCreditPercentage < 50) {
                    $color = 'danger';
                }
            }
            $card = Card::make($account->name, $content)
                ->description($description)
                ->color($color);
            $data[] = $card;
        });

        return $data;
    }
}
