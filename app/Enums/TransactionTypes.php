<?php

namespace App\Enums;

enum TransactionTypes: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
