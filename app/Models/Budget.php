<?php

namespace App\Models;

use App\Models\Traits\CategorisesTransactions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory, CategorisesTransactions;

    public $fillable = [
        'name',
        'currency',
        'amount',
        'period_type',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled',
    ];

    public function currentTally(): ?Tally
    {
        $today = Carbon::today();

        return $this->tallies
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }

    public function scopeWithCurrentTallies($query)
    {
        $today = Carbon::today();

        return $query->with('tallies')
            ->where('tallies.start_date', '<=', $today)
            ->where('tallies.end_date', '>=', $today);
    }

    public function tallies(): HasMany
    {
        return $this->hasMany(Tally::class);
    }
}
