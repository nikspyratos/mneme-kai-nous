<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Brick\Money\Currency;
use Brick\Money\Money;

trait FormatsMoneyColumns
{
    public function formatKeyAsMoneyString(string $key): string
    {
        if (isset($this->$key)) {
            $amount = round($this->$key / 100, 2);

            return Money::of($amount, Currency::of($this->currency))->formatTo('en_ZA');
        }

        return 'N/A';
    }

    public function formatValueAsMoneyString($value): string
    {
        $amount = round($value, 2);

        return Money::of($amount, Currency::of($this->currency))->formatTo('en_ZA');
    }
}
