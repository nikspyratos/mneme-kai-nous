<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerations\TransactionTypes;
use App\Models\Traits\CategorisesTransactions;
use App\Models\Traits\FormatsMoneyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class ExpectedTransaction extends Model
{
    use HasFactory, CategorisesTransactions, FormatsMoneyColumns;

    public $fillable = [
        'expected_transaction_template_id',
        'tally_id',
        'name',
        'description',
        'group',
        'currency',
        'amount',
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
        'identifier' => 'array',
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

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExpectedTransactionTemplate::class, 'expected_transaction_template_id');
    }

    public function tally(): BelongsTo
    {
        return $this->belongsTo(Tally::class);
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->formatKeyAsMoneyString('amount');
    }
}
