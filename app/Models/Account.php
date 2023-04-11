<?php

namespace App\Models;

use App\Enums\AccountTypes;
use App\Enums\Banks;
use App\Enums\TransactionTypes;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property string $name
 * @property string|null $bank_name
 * @property string|null $account_number
 * @property string $currency
 * @property int|null $balance
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $debt
 * @property bool $has_overdraft
 * @property int|null $overdraft_amount
 * @property string|null $bank_identifier
 * @property array|null $data
 * @property bool $is_primary
 * @property bool|null $is_main
 * @property-read float|null $available_credit_percentage
 * @property-read float|null $debt_paid_off_percentage
 * @property-read string $formatted_balance
 * @property-read string $formatted_debt
 * @property-read string $formatted_debt_balance
 * @property-read string $formatted_overdraft_amount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 *
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBankIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDebt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereHasOverdraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereOverdraftAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory, FormatsMoneyColumns;

    public $fillable = [
        'name',
        'bank_name',
        'account_number',
        'currency',
        'balance',
        'debt',
        'type',
        'has_overdraft',
        'overdraft_amount',
        'bank_identifier',
        'data',
        'is_primary',
        'is_main',
    ];

    public $casts = [
        'has_overdraft' => 'boolean',
        'data' => 'json',
        'is_primary' => 'boolean',
        'is_main' => 'boolean',
    ];

    public static function firstOrCreateInvestec(
        string $accountNumber,
        string $currency,
        int $balance = 0,
        ?string $bankIdentifier = null,
        ?string $accountName = null,
        ?array $data = null
    ): self {
        return Account::firstOrCreate([
            'account_number' => $accountNumber,
            'bank_name' => Banks::INVESTEC->value,
            'currency' => $currency,
        ],
            [
                'name' => $accountName ?? Banks::INVESTEC->value . ' ' . $accountNumber,
                'type' => AccountTypes::TRANSACTIONAL->value,
                'balance' => $balance,
                'bank_identifier' => $bankIdentifier,
                'data' => $data,
            ]);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedDebtBalanceAttribute(): string
    {
        return $this->formatValueAsMoneyString(($this->debt - $this->balance) / 100);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return $this->formatKeyAsMoneyString('balance');
    }

    public function getFormattedDebtAttribute(): string
    {
        return $this->formatKeyAsMoneyString('debt');
    }

    public function getFormattedOverdraftAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('overdraft_amount');
    }

    public function getDebtPaidOffPercentageAttribute(): ?float
    {
        if ($this->type == AccountTypes::DEBT->value) {
            return round(($this->debt - $this->balance) / $this->debt, 2);
        }

        return null;
    }

    public function getAvailableCreditPercentageAttribute(): ?float
    {
        if ($this->type == AccountTypes::CREDIT->value) {
            return round($this->balance / $this->debt * 100, 2);
        }

        return null;
    }

    public function updateBalance(int $amount, TransactionTypes $transactionType = TransactionTypes::DEBIT)
    {
        if ($transactionType->value == TransactionTypes::DEBIT->value) {
            $this->balance -= $amount;
        } else {
            $this->balance += $amount;
        }
        if ($this->balance < 0 && ! $this->has_overdraft) {
            Log::error('Non-overdraft account has negative balance', ['account' => $this->name, 'balance' => $this->balance]);
        }
        if ($this->has_overdraft && abs($this->balance) > $this->overdraft_amount) {
            Log::error('Overdraft exceeded on account', ['account' => $this->name, 'balance' => $this->balance, 'overdraft' => $this->overdraft_amount]);
        }
        $this->save();
    }
}
