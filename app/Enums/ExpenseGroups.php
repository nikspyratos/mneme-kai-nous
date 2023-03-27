<?php

namespace App\Enums;

enum ExpenseGroups: string
{
    case RICHMOND_203 = 'The Richmond 203';
    case FOURSEASONS_1105 = 'Four Seasons 1105';
    case TT = 'Audi TT';
    case PAYMENTS = 'Payments';
    case OTHER = 'Other';

    public function isRequired(): bool
    {
        return match ($this) {
            self::RICHMOND_203, self::OTHER, self::PAYMENTS => true,
            default => false,
        };
    }
}
