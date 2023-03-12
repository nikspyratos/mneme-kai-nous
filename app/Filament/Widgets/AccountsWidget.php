<?php

namespace App\Filament\Widgets;

use App\Enums\AccountType;
use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AccountsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $data = [];
        $accounts = Account::all();
        $accounts->each(function ($account) use (&$data) {
            $synced = $account->isBalanceInSyncWithTransactions() ? 'true' : 'false';
            $content = $account->formatted_balance;
            $description = $account->bank_name . " | Synced: $synced";
            if ($account->type == AccountType::DEBT->value) {
                $content .= ' / ' . $account->formattedDebt;
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
