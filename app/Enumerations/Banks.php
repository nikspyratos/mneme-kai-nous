<?php

declare(strict_types=1);

namespace App\Enumerations;

enum Banks: string
{
    case INVESTEC = 'Investec';
    case BANKZERO = 'Bank Zero';
    case ABSA = 'Absa';
    case TYME = 'Tyme Bank';
    case STANDARD_BANK = 'Standard Bank';
    case UNASSIGNED = 'Unassigned';
}
