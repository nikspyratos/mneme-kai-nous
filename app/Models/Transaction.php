<?php

namespace App\Models;

use App\Enums\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use App\Services\TallyRolloverDateCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $tally_id
 * @property Carbon $date
 * @property string|null $type
 * @property string|null $description
 * @property string|null $detail
 * @property string $currency
 * @property int|null $amount
 * @property int|null $fee
 * @property int|null $listed_balance
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $category
 * @property array|null $data
 * @property int $is_tax_relevant
 * @property int|null $parent_id
 * @property-read \App\Models\Account|null $account
 * @property-read \App\Models\Budget|null $budget
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\ExpectedTransaction|null $expectedTransaction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpectedTransaction> $expectedTransactions
 * @property-read int|null $expected_transactions_count
 * @property-read string $formatted_amount
 * @property-read string $formatted_fee
 * @property-read string $formatted_listed_balance
 * @property-read Transaction|null $parent
 * @property-read \App\Models\Tally|null $tally
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction inCurrentBudgetMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction taxRelevant(?\Illuminate\Support\Carbon $startDate = null, ?\Illuminate\Support\Carbon $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereIsTaxRelevant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereListedBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTallyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
