<?php

namespace App\Models;

use App\Models\Traits\CategorisesTransactions;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Budget
 *
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property int|null $amount
 * @property string $period_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \Illuminate\Support\Collection|null $identifier
 * @property string|null $identifier_transaction_type
 * @property int $enabled
 * @property-read string $formatted_amount
 * @property-read string $identifier_string
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tally> $tallies
 * @property-read int|null $tallies_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereIdentifierTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget wherePeriodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget withCurrentTallies()
 *
 * @mixin \Eloquent
 */
class Budget extends Model
{
    use HasFactory, CategorisesTransactions, FormatsMoneyColumns;

    public $fillable = [
        'name',
        'currency',
        'amount',
        'period_type',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled',
    ];

    public $casts = [
        'identifier' => 'collection',
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

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount');
    }

    public function tallies(): HasMany
    {
        return $this->hasMany(Tally::class);
    }
}
