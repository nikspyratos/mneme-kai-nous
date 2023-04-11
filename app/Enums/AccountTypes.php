<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountTypes: string
{
    case TRANSACTIONAL = 'Transactional';
    case DEBT = 'Debt';
    case CREDIT = 'Credit';
    case SAVINGS = 'Savings';
    case INVESTMENT = 'Investment';
    case POINTS = 'Points';
}
