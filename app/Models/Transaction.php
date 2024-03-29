<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory, FormatsMoneyColumns;

    public $fillable = [
        'parent_id',
        'account_id',
        'tally_id',
        'date',
        'type',
        'category',
        'description',
        'detail',
        'currency',
        'amount',
        'fee',
        'listed_balance',
        'data',
        'is_tax_relevant',
    ];

    public $casts = [
        'date' => 'datetime',
        'data' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        $tallyUpdateFunction = function (Transaction $transaction) {
            $isNotChild = empty($transaction->parent_id);
            if ($isNotChild && ! empty($transaction->tally_id) && empty($transaction->getOriginal('tally_id'))) {
                Tally::find($transaction->tally_id)
                    ->updateBalance($transaction->amount, TransactionTypes::from($transaction->type));
            } elseif ($isNotChild && ! empty($transaction->getOriginal('tally_id')) && empty($transaction->tally_id)) {
                //Reverse the balance calculation
                Tally::find($transaction->getOriginal('tally_id'))
                    ->updateBalance($transaction->amount * -1, TransactionTypes::from($transaction->type));
            }
        };
        static::creating($tallyUpdateFunction);
        static::updating($tallyUpdateFunction);
    }

    public function scopeTaxRelevant($query, ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = $query->whereIsTaxRelevant(true);
        if ($startDate) {
            $query = $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query = $query->where('date', '<=', $endDate);
        }

        return $query;
    }

    public function scopeInCurrentBudgetMonth($query)
    {
        return $query->whereBetween(
            'date',
            [
                TallyRolloverDateCalculator::getPreviousDate(), TallyRolloverDateCalculator::getNextDate(),
            ]
        );
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount');
    }

    public function getFormattedAmountWithSignsAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount', true);
    }

    public function getFormattedFeeAttribute(): string
    {
        return $this->formatKeyAsMoneyString('fee');
    }

    public function getFormattedListedBalanceAttribute(): string
    {
        return $this->formatKeyAsMoneyString('listed_balance');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Transaction::class, 'parent_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expectedTransactions(): BelongsToMany
    {
        return $this->belongsToMany(ExpectedTransaction::class);
    }

    public function expectedTransaction(): BelongsTo
    {
        return $this->belongsTo(ExpectedTransaction::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function tally(): BelongsTo
    {
        return $this->belongsTo(Tally::class);
    }
}
