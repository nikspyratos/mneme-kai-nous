<?php

namespace App\Enums;

enum TransactionTypes: string
{
    case DEBIT = 'Debit';
    case CREDIT = 'Credit';

    public static function getOppositeType(TransactionTypes $type): TransactionTypes
    {
        return match ($type->value) {
            self::DEBIT->value => self::CREDIT,
            self::CREDIT->value => self::DEBIT,
        };
    }
}
