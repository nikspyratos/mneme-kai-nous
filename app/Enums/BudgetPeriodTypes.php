<?php

namespace App\Enums;

enum BudgetPeriodTypes: string
{
    case MONTHLY = 'Monthly';
    //NOTE: Weekly is not yet required, so not build
}
