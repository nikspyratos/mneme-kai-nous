<?php

declare(strict_types=1);

use App\Models\Tally;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            $table->foreignIdFor(Tally::class)->nullable()->after('expected_transaction_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('expected_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('expected_transactions', 'tally_id')) {
                $table->dropColumn('tally_id');
            }
        });
    }
};
