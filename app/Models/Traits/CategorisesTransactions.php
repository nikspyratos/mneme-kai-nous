<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CategorisesTransactions
{
    public function transactionIsForThis(string $transactionDescription): bool
    {
        return (
            ! empty($this->identifier)
            && Str::contains($transactionDescription, Arr::flatten($this->identifier))
        )
        || (
            ! empty($this->identifier_transaction_type)
            && Str::contains($transactionDescription, $this->identifier_transaction_type)
        );
    }

    public function getIdentifierStringAttribute(): string
    {
        return $this->identifier
            ? implode(', ', collect($this->identifier)->pluck('identifier')->toArray())
            : '';
    }
}
