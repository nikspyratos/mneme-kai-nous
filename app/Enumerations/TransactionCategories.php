<?php

declare(strict_types=1);

namespace App\Enumerations;

enum TransactionCategories: string
{
    case FOOD = 'Food';
    case DEBT = 'Debt';
    case UTILITIES = 'Utilities';
    case ENTERTAINMENT = 'Entertainment';
    case PRODUCT = 'Product';
    case SERVICE = 'Service';
    case MEDICAL = 'Medical';
    case TRANSFER = 'Transfer';
    case BUSINESS = 'Business';
    case LEARNING = 'Learning';
    case HOBBY = 'Hobby';
    case VEHICLE = 'Vehicle';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
