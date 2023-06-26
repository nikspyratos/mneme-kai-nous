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
            if (Schema::hasColumn('expected_transactions', 'budget_id')) {
                $table->dropColumn('budget_id');
            }
        });
    }

    public function down(): void
    {
        //Data is retrievable but not going to write that data migration
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->foreignIdFor(Budget::class)->nullable()->after('expected_transaction_template_id');
        });
    }
};
