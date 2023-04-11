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
            $table->date('next_due_date')->nullable()->after('due_day');
        });
    }

    public function down(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->dropColumn('next_due_date');
        });
    }
};
