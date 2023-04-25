<?php

declare(strict_types=1);

namespace App\Enumerations;

enum DuePeriods: string
{
    case MONTHLY = 'Monthly';
    case WEEKLY = 'Weekly';
}
