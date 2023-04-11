<?php

namespace App\Models;

use App\Enums\DuePeriods;
use App\Enums\TransactionTypes;
use App\Models\Traits\CategorisesTransactions;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\ExpectedTransaction
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $group
 * @property string $currency
 * @property int|null $amount
 * @property string|null $due_period
 * @property int|null $due_day
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \Illuminate\Support\Collection|null $identifier
 * @property string|null $identifier_transaction_type
 * @property int $enabled
 * @property string $type
 * @property int $is_tax_relevant
 * @property Carbon|null $next_due_date
 * @property int|null $budget_id
 * @property bool $is_paid
 * @property-read \App\Models\Budget|null $budget
 * @property-read string $formatted_amount
 * @property-read string $identifier_string
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDueDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDuePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIdentifierTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIsTaxRelevant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereNextDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
        'is_paid',
        'identifier',
        'identifier_transaction_type', //Ideally this should be set WITHOUT identifier
        'enabled',
        'type',
        'is_tax_relevant',
    ];

    public $casts = [
        'next_due_date' => 'date',
        'is_paid' => 'boolean',
        'identifier' => 'collection',
    ];

    public static function boot()
    {
        parent::boot();

        $tallyUpdateFunction = function ($expectedTransaction) {
            Log::info('Updating tally for expected transaction');
            if ($expectedTransaction->getOriginal('enabled') && ! $expectedTransaction->enabled) {
                Log::info('Expected transaction was enabled, now disabled');
                $tallyIds = $expectedTransaction->transactions->pluck('tally_id');
                $tallies = Tally::whereIn('id', $tallyIds)->get();
                foreach ($tallies as $tally) {
                    $tally->updateBalance($expectedTransaction->amount * -1, TransactionTypes::from($expectedTransaction->type));
                }
            } else {
                Log::info('Expected transaction was not enabled, or is still enabled');
            }
        };
        static::updating($tallyUpdateFunction);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class);
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
