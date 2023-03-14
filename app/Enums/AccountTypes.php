<?php

namespace App\Enums;

enum AccountTypes: string
{
    case TRANSACTIONAL = 'transactional';
    case DEBT = 'debt';
    case CREDIT = 'credit';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case POINTS = 'points';
}
