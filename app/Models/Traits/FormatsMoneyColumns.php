<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Enumerations\TransactionTypes;
use Brick\Money\Currency;
use Brick\Money\Money;

trait FormatsMoneyColumns
{
    public function formatKeyAsMoneyString(string $key): string
    {
        if (isset($this->$key)) {
            $amount = round($this->$key / 100, 2);
            $formatted = Money::of($amount, Currency::of($this->currency))->formatTo('en_ZA');
            if ($this->type == TransactionTypes::DEBIT->value) {
                $formatted = '-' . $formatted;
            } else {
                $formatted = '+' . $formatted;
            }

            return $formatted;
        }

        return 'N/A';
    }

    public function formatValueAsMoneyString($value): string
    {
        $amount = round($value, 2);

        return Money::of($amount, Currency::of($this->currency))->formatTo('en_ZA');
    }
}
