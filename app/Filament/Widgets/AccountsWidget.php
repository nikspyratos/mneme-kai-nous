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
            $card = Card::make($account->name . ' Balance', $account->formattedBalance);
            if ($account->type == AccountType::DEBT->value) {
                $card->description('Capital: ' . $account->formattedDebt . ' | Debt paid off: ' . $account->debtPaidOffPercentage . '%')
                    ->color('danger');
            }
            $data[] = $card;
        });

        return $data;
    }
}
