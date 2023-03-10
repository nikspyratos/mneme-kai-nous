<?php

namespace App\Enums;

enum TransactionCategories: string
{
    case GROCERIES = 'groceries';
    case FOOD = 'food';
    case DEBT = 'debt';
    case UTILITIES = 'utilities';
    case ENTERTAINMENT = 'entertainment';
    case PRODUCT = 'product';
    case SERVICE = 'service';
    case MEDICAL = 'medical';
    case TRANSFER = 'transfer';
    case BUSINESS = 'business';
    case LEARNING = 'learning';
    case HOBBY = 'hobby';
    case VEHICLE = 'vehicle';

}
