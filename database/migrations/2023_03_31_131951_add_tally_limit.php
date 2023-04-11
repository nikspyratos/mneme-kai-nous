<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tallies', function (Blueprint $table) {
            $table->unsignedInteger('limit')->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('tallies', function (Blueprint $table) {
            $table->dropColumn('limit');
        });
    }
};
