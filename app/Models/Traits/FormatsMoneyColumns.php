<?php

namespace App\Models\Traits;

use Brick\Money\Money;

trait FormatsMoneyColumns
{
    public function formatMoneyColumn(string $key): string
    {
        if ($this->$key) {
            $amount = round($this->$key / 100, 2);

            return Money::of($amount, $this->currency)->formatTo('en_ZA');
        }

        return '';
    }
}
