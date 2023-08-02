<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function scopeForPeriod($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate);
    }

    public function scopeForRecentBudgetMonths($query)
    {
        return $this->scopeForPeriod($query, TallyRolloverDateCalculator::getPreviousDate(Carbon::today()->subMonths(3)), TallyRolloverDateCalculator::getNextDate());
    }

    public function scopeForCurrentBudgetMonth($query)
    {
        return $this->scopeForPeriod($query, TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate());
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
        return (int) ($this->balance / $this->limit * 100);
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
        $expectedTransactionsSum = 0;
        $this->balance = 0;
        $this->balance += $this->transactions()->inCurrentBudgetMonth()->where('type', TransactionTypes::DEBIT->value)->sum('amount');
        $this->balance -= $this->transactions()->inCurrentBudgetMonth()->where('type', TransactionTypes::CREDIT->value)->sum('amount');
        ExpectedTransaction::where(function ($query) {
            $query->where('next_due_date', null)
                ->orWhereBetween(
                    'next_due_date',
                    [
                        TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                    ]
                );
        })
            ->where('enabled', true)
            ->where('type', TransactionTypes::CREDIT->value)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->with('transactions', function ($query) {
                return $query->where('tally_id', $this->id);
            })
            ->get()
            ->each(function ($expectedTransaction) use (&$expectedTransactionsSum) {
                $isForThisTally = $expectedTransaction->transactions->whereBetween(
                    'date',
                    [
                        TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
                    ]
                )->count() > 0;
                if ($isForThisTally) {
                    $expectedTransactionsSum += $expectedTransaction->amount;
                }
            });
        $this->balance -= $expectedTransactionsSum;

        $this->save();

        return $this->balance;
    }
}
