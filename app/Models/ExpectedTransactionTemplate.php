<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\DuePeriods;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ExpectedTransactionTemplate extends Model
{
    use HasFactory, FormatsMoneyColumns;

    public $fillable = [
        'budget_id',
        'name',
        'description',
        'group',
        'currency',
        'amount',
        'due_period',
        'due_day',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled',
        'type',
        'is_tax_relevant',
    ];

    public $casts = [
        'is_paid' => 'boolean',
        'identifier' => 'array',
    ];

    public function getDueDayFormattedAttribute(): string
    {
        $suffix = 'th';
        if (in_array($this->due_day, [1, 21, 31])) {
            $suffix = 'st';
        } elseif (in_array($this->due_day, [2, 22])) {
            $suffix = 'nd';
        } elseif (in_array($this->due_day, [3, 23])) {
            $suffix = 'rd';
        }

        return $this->due_day . $suffix . ' ' . $this->due_period;
    }

    public function getNextDueDate(): ?Carbon
    {
        $today = Carbon::today();
        if ($today->day > $this->due_day) {
            if ($this->due_period == DuePeriods::MONTHLY->value) {
                $nextDueDate = Carbon::today()->startOfMonth()->addMonth()->setDay($this->due_day);
                //Set it to the next period if it's already been paid
                if ($this->is_paid && $today->month < $nextDueDate->month) {
                    $nextDueDate = $nextDueDate->startOfMonth()->addMonth()->setDay($this->due_day);
                }

                return $nextDueDate;
            } elseif ($this->due_period == DuePeriods::WEEKLY->value) {
                return Carbon::today()->startofWeek()->addWeek()->setDay($this->due_day);
            }
        }

        return null;
    }

    public function expectedTransactions(): HasMany
    {
        return $this->hasMany(ExpectedTransaction::class);
    }

    public function getLatestExpectedTransaction(): ExpectedTransaction
    {
        /** @var ExpectedTransaction */
        return $this->expectedTransactions()->latest()->first();
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount');
    }
}
