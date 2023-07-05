<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\DuePeriods;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ExpectedTransactionTemplate
 *
 * @property int $id
 * @property int|null $budget_id
 * @property string $name
 * @property string|null $description
 * @property string $group
 * @property string $currency
 * @property int $amount
 * @property string|null $due_period
 * @property int|null $due_day
 * @property array|null $identifier
 * @property string|null $identifier_transaction_type
 * @property int $enabled
 * @property string $type
 * @property int $is_tax_relevant
 * @property bool $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpectedTransaction> $expectedTransactions
 * @property-read int|null $expected_transactions_count
 * @property-read string $formatted_amount
 *
 * @method static \Database\Factories\ExpectedTransactionTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereDueDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereDuePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereIdentifierTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereIsTaxRelevant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransactionTemplate whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpectedTransaction> $expectedTransactions
 *
 * @mixin \Eloquent
 */
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
