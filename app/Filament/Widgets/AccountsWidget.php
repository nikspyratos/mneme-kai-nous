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
            $content = $account->formattedBalance;
            $description = $account->bank_name;
            if ($account->isSyncable()) {
                $synced = $account->isBalanceInSyncWithTransactions() ? 'true' : 'false';
                $description .= " | Synced: {$synced}";
            }
            if ($account->type == AccountTypes::DEBT->value) {
                $content = $account->formattedDebtBalance . ' / ' . $account->formattedDebt;
                $description .= ' | Paid off: ' . $account->debtPaidOffPercentage . '%';
            }
            $card = Card::make($account->name, $content)
                ->description($description)
                ->color('success');
            $data[] = $card;
        });

        return $data;
    }
}
