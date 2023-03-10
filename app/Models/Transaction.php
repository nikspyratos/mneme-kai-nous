<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public $fillable = [
        'account_id',
        'expense_id',
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
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
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
