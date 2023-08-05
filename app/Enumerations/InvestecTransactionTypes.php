<?php

declare(strict_types=1);

namespace App\Enumerations;

enum InvestecTransactionTypes: string
{
    case CARD = 'CardPurchases';
    case ATM_WITHDRAWAL = 'ATMWithdrawals';
    case ONLINE_BANKING = 'OnlineBankingPayments';
    case DEPOSITS = 'Deposits';
    case DEBIT_ORDER = 'DebitOrders';
    case BUDGET_INSTALMENT = 'BudgetInstalments';
    case FASTER_PAYMENT_FEE = 'FasterPay';
    case FEES_AND_INTEREST = 'FeesAndInterest';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
