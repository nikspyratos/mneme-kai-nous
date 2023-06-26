<?php

declare(strict_types=1);

use App\Enumerations\DuePeriods;
use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;

return new class extends Migration
{
    public function up(): void
    {
        $expectedTransactions = ExpectedTransaction::whereEnabled(true)->whereDuePeriod(DuePeriods::MONTHLY->value)->get();
        foreach ($expectedTransactions as $expectedTransaction) {
            $expectedTransactionTemplate = ExpectedTransactionTemplate::create(
                Arr::only(
                    $expectedTransaction->toArray(),
                    get_fillable(ExpectedTransactionTemplate::class)
                )
            );
            $expectedTransaction->update(['expected_transaction_template_id' => $expectedTransactionTemplate->id]);
        }
    }

    public function down(): void
    {
        \App\Models\ExpectedTransactionTemplate::truncate();
    }
};
