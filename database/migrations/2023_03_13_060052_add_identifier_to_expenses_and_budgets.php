<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('identifier')->nullable();
            $table->string('identifier_transaction_type')->nullable();
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->string('identifier')->nullable();
            $table->string('identifier_transaction_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'identifier_transaction_type']);
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'identifier_transaction_type']);
        });
    }
};
