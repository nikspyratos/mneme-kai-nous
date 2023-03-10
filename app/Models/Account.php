<?php

namespace App\Models;

use App\Enums\AccountType;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'bank_name',
        'account_number',
        'currency',
        'balance',
        'debt',
        'type',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        $amount = round($this->balance / 100, 2);

        return Money::of($amount, $this->currency)->formatTo('en_ZA');
    }

    public function getFormattedDebtAttribute(): string
    {
        $amount = round($this->debt / 100, 2);

        return Money::of($amount, $this->currency)->formatTo('en_ZA');
    }

    public function getDebtPaidOffPercentageAttribute()
    {
        if ($this->type == AccountType::DEBT->value) {
            $amount = round(($this->debt - $this->balance) / $this->debt, 2);

            return Money::of($amount, $this->currency)->formatTo('en_ZA');
        }

        return null;
    }
}
