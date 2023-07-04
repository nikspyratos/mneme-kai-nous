<?php

declare(strict_types=1);

namespace App\Enumerations;

enum ExpenseGroups: string
{
    case RICHMOND_203 = '203 Richmond';
    case FOURSEASONS_1105 = '1105 Four Seasons';
    case TT = 'TT';
    case PAYMENTS = 'Payments';
    case OTHER = 'Other';
    case DISCRETIONARY = 'Discretionary';

    public function isRequired(): bool
    {
        return match ($this) {
            self::RICHMOND_203, self::OTHER, self::PAYMENTS => true,
            default => false,
        };
    }
}
