<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class ConvertTransactionsToMarkdown
{
    use AsAction;

    public function handle(Collection $transactions)
    {
    }
}
