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
}
