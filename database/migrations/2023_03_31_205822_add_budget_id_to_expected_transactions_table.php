<?php

declare(strict_types=1);

use App\Models\Budget;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->foreignIdFor(Budget::class)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->dropForeign(['budget_id']);
            $table->dropColumn('budget_id');
        });
    }
};
