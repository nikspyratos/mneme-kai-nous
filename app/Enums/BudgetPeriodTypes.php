<?php

declare(strict_types=1);

namespace App\Enums;

enum BudgetPeriodTypes: string
{
    case MONTHLY = 'Monthly';
    //NOTE: Weekly is not yet required, so not build
}
