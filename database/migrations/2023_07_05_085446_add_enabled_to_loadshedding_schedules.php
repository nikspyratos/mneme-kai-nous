<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loadshedding_schedules', function (Blueprint $table) {
            $table->boolean('enabled')->default(true)->after('is_home');
        });
    }

    public function down(): void
    {
        Schema::table('loadshedding_schedules', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
};
