<?php

declare(strict_types=1);

namespace App\Enums;

enum DuePeriods: string
{
    case MONTHLY = 'Monthly';
    case WEEKLY = 'Weekly';
}
