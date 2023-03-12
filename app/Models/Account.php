<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\Banks;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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
        'has_overdraft',
        'bank_identifier',
        'data'
    ];

    public $casts = [
        'data' => 'object'
    ];

    public static function firstOrCreateInvestec(
        string $accountNumber,
        string $currency,
        int $balance = 0,
        ?string $bankIdentifier = null,
        ?string $accountName = null,
        ?array $data = null
    ): self
    {
        return Account::firstOrCreate([
            'account_number' => $accountNumber,
            'bank_name' => Banks::INVESTEC->value,
            'currency' => $currency,
        ],
        [
            'name' => $accountName ?? Banks::INVESTEC->value . " " . $accountNumber,
            'type' => AccountType::TRANSACTIONAL->value,
            'bank_identifier' => $bankIdentifier,
            'data' => $data
        ]);
    }

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

    public function updateBalance(int $amount)
    {
        $this->balance -= $amount;
        if ($this->balance < 0 && !$this->has_overdraft) {
            Log::error('Non-overdraft account has negative balance', ['account' => $this->name, 'balance' => $this->balance]);
        }
        if ($this->has_overdraft && abs($this->balance) > $this->overdraft_amount) {
            Log::error('Overdraft exceeded on account', ['account' => $this->name, 'balance' => $this->balance, 'overdraft' => $this->overdraft_amount]);
        }
        $this->save();
    }

    public function isBalanceInSyncWithTransactions(): bool
    {
        return $this->transactions()->latest()->first()?->listed_balance !== $this->balance;
    }
}
