<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('enabled')->default(true);
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->boolean('enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
};
