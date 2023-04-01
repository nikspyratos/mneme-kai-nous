<?php

namespace App\Models;

use App\Enums\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tally extends Model
{
    use HasFactory, FormatsMoneyColumns;

    public $fillable = [
        'budget_id',
        'name',
        'currency',
        'balance',
        'limit',
        'start_date',
        'end_date',
    ];

    public $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeForCurrentBudgetMonth($query)
    {
        return $query->where('start_date', '>=', TallyRolloverDateCalculator::getPreviousDate())
            ->where('end_date', '<=', TallyRolloverDateCalculator::getNextDate());
    }

    public function getFormattedBalanceAttribute(): string
    {
        return $this->formatKeyAsMoneyString('balance');
    }

    public function getFormattedLimitAttribute(): string
    {
        return $this->formatKeyAsMoneyString('limit');
    }

    public function getBalancePercentageOfBudget(): int
    {
        return $this->balance / $this->limit * 100;
    }

    public function updateBalance(int $amountInCents, TransactionTypes $transactionType)
    {
        if ($transactionType == TransactionTypes::DEBIT) {
            $this->balance += $amountInCents;
        } else {
            $this->balance -= $amountInCents;
        }
        $this->save();
    }

    public function calculateBalance(): int
    {
        $this->balance = 0;
        $this->balance += $this->transactions()->inCurrentBudgetMonth()->where('type', TransactionTypes::DEBIT->value)->sum('amount');
        $this->balance -= $this->transactions()->inCurrentBudgetMonth()->where('type', TransactionTypes::CREDIT->value)->sum('amount');
        $this->save();

        return $this->balance;
    }
}
