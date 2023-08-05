<?php

declare(strict_types=1);

use App\Enumerations\InvestecTransactionTypes;
use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Transaction::whereIn('category', InvestecTransactionTypes::values())->update(['category' => null]);
    }

    public function down(): void
    {
    }
};
