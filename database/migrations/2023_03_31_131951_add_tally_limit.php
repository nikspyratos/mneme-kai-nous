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
        Schema::table('tallies', function (Blueprint $table) {
            $table->unsignedInteger('limit')->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tallies', function (Blueprint $table) {
            $table->dropColumn('limit');
        });
    }
};
