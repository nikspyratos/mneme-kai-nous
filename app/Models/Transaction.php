<?php

namespace App\Models;

use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory, FormatsMoneyColumns;

    public $fillable = [
        'account_id',
        'expected_transaction_id',
        'budget_id',
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

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatMoneyColumn('amount');
    }

    public function getFormattedFeeAttribute(): string
    {
        return $this->formatMoneyColumn('fee');
    }

    public function getFormattedListedBalanceAttribute(): string
    {
        return $this->formatMoneyColumn('listed_balance');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
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
