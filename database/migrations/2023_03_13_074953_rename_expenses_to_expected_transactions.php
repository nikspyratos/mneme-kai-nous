<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->rename('expected_transactions');
        });
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->string('type')->default('debit');
            $table->boolean('is_tax_relevant')->default(false);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('expense_id', 'expected_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_tax_relevant']);
        });
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->rename('expenses');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('expected_transaction_id', 'expense_id');
        });
    }
};
