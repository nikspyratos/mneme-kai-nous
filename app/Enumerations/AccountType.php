<?php

namespace App\Enumerations;

enum AccountType: string
{
    case MONEY = 'money';
    case DEBT = 'debt';
    case INVESTMENTS = 'investments';
}
