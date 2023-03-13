<?php

namespace App\Models;

use App\Models\Traits\CategorisesTransactions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory, CategorisesTransactions;

    public $fillable = [
        'name',
        'description',
        'group',
        'currency',
        'amount',
        'due_period',
        'due_day',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled,'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getNextDueDateAttribute(): ?Carbon
    {
        if (Carbon::today()->day > $this->due_day) {
            if ($this->due_period == 'monthly') {
                return Carbon::today()->addMonth()->setDay($this->due_day);
            } elseif ($this->due_period == 'weekly') {
                return Carbon::today()->addWeek()->setDay($this->due_day);
            }
        }

        return null;
    }
}
