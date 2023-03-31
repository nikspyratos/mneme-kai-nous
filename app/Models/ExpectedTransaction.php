<?php

namespace App\Models;

use App\Enums\DuePeriods;
use App\Models\Traits\CategorisesTransactions;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ExpectedTransaction extends Model
{
    use HasFactory, CategorisesTransactions, FormatsMoneyColumns;

    public $fillable = [
        'budget_id',
        'name',
        'description',
        'group',
        'currency',
        'amount',
        'due_period',
        'due_day',
        'next_due_date',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled',
        'type',
        'is_tax_relevant',
    ];

    public $casts = [
        'next_due_date' => 'date',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getNextDueDate(): ?Carbon
    {
        if (Carbon::today()->day > $this->due_day) {
            if ($this->due_period == DuePeriods::MONTHLY->value) {
                return Carbon::today()->startOfMonth()->addMonth()->setDay($this->due_day);
            } elseif ($this->due_period == DuePeriods::WEEKLY->value) {
                return Carbon::today()->startofWeek()->addWeek()->setDay($this->due_day);
            }
        }

        return null;
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount');
    }

    public function getTransactionsDue()
    {
    }
}
