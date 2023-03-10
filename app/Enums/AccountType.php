<?php

namespace App\Enums;

enum AccountType: string
{
    case TRANSACTIONAL = 'transactional';
    case DEBT = 'debt';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case POINTS = 'points';
}
