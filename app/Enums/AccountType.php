<?php

namespace App\Enums;

enum AccountType: string
{
    case TRANSACTIONAL = 'transactional';
    case DEBT = 'debt';
    case CREDIT = 'credit';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case POINTS = 'points';
}
