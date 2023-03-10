<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'currency',
        'amount',
        'period_type', //monthly
    ];

    public function currentTally(): ?Tally
    {
        $today = Carbon::today();

        return $this->tallies
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }

    public function tallies(): HasMany
    {
        return $this->hasMany(Tally::class);
    }
}
