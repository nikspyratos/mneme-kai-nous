<?php

namespace App\Enums;

enum BudgetPeriodTypes: string
{
    case MONTHLY = 'monthly';
    //NOTE: Weekly is not yet required, so not build
}
