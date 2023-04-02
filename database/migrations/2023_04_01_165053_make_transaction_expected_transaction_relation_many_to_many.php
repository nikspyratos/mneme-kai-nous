<?php

use App\Models\ExpectedTransaction;
use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expected_transaction_transaction', function (Blueprint $table) {
            $table->foreignIdFor(ExpectedTransaction::class);
            $table->foreignIdFor(Transaction::class);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expected_transaction_transaction');
    }
};
