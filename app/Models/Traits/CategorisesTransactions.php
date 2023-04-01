<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait CategorisesTransactions
{
    public function transactionIsForThis(string $transactionDescription): bool
    {
        return (
            $this->identifier
            && Str::contains($transactionDescription, $this->identifier)
        )
        || (
            $this->identifier_transaction_type
            && Str::contains($transactionDescription, $this->identifier_transaction_type)
        );
    }

    public function getIdentifierStringAttribute(): string
    {
        return $this->identifier
            ? implode(', ', $this->identifier->pluck('identifier')->toArray())
            : '';
    }
}
