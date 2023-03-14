<?php

namespace App\Enums;

enum TransactionTypes: string
{
    case DEBIT = 'Debit';
    case CREDIT = 'Credit';
}
