<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('summaries');
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summaries');
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('currency')->default('ZAR');
            $table->bigInteger('amount')->nullable();
            $table->timestamps();
        });
    }
};
