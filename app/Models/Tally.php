<?php

namespace App\Models;

use App\Enums\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tally
 *
 * @property int $id
 * @property int $budget_id
 * @property string $name
 * @property string $currency
 * @property int|null $balance
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $limit
 * @property-read \App\Models\Budget|null $budget
 * @property-read string $formatted_balance
 * @property-read string $formatted_limit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tally forCurrentBudgetMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally forPeriod(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
