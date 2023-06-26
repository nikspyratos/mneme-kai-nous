<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expected_transaction_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Budget::class)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('group');
            $table->string('currency')->default(\App\Enumerations\Currencies::RANDS->value);
            $table->bigInteger('amount');
            $table->string('due_period')->nullable();
            $table->integer('due_day')->nullable();
            $table->string('identifier')->nullable();
            $table->string('identifier_transaction_type')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('type')->default(\App\Enumerations\TransactionTypes::DEBIT->value);
            $table->boolean('is_tax_relevant')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expected_transaction_templates');
    }
};
