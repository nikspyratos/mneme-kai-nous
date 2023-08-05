<?php

declare(strict_types=1);

use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $templates = ExpectedTransactionTemplate::all();
        foreach ($templates as $template) {
            ExpectedTransaction::where('name', 'LIKE', '' . $template->name . ':%')
                ->update(['expected_transaction_template_id' => $template->id]);
        }
    }

    public function down(): void
    {
        //
    }
};
