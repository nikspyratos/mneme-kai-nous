<?php

namespace App\Enums;

enum Currencies: string
{
    case RANDS = 'ZAR';
    case EURO = 'EUR';
    case POUNDS = 'GBP';
    case DOLLARS = 'USD';
    //Crypto
    case BITCOIN = 'BTC';
    //Points
    case INVESTEC_REWARDS = 'INV';

    public function symbol(): string
    {
        return match ($this) {
            Currencies::RANDS => 'R',
            Currencies::EURO => '€',
            Currencies::POUNDS => '£',
            Currencies::DOLLARS => '$',
            Currencies::BITCOIN => 'BTC',
            Currencies::INVESTEC_REWARDS => 'INV',
        };
    }
}
