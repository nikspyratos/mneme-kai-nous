<?php

namespace App\Models;

use App\Services\TallyRolloverDateCalculator;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tally extends Model
{
    use HasFactory;

    public $fillable = [
        'budget_id',
        'name',
        'currency',
        'balance',
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

    public function scopeForCurrentMonth($query)
    {
        return $query->where('start_date', '>=', TallyRolloverDateCalculator::getPreviousDate())
            ->where('end_date', '<=', TallyRolloverDateCalculator::getNextDate());
    }

    public function getFormattedBalanceAttribute(): string
    {
        $amount = round($this->balance / 100, 2);

        return Money::of($amount, $this->currency)->formatTo('en_ZA');
    }

    public function getBalancePercentageOfBudget(): int
    {
        return $this->balance / $this->budget->balance * 100;
    }
}
