<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->dropColumn(['due_period', 'due_day']);
        });
    }

    public function down(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->integer('due_day')->nullable()->after('amount');
            $table->string('due_period')->nullable()->after('amount');
        });
    }
};
