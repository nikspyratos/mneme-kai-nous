<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\Widget;

class QuoteWidget extends Widget
{
    protected static string $view = 'filament.widgets.quote';

    public function getViewData(): array
    {
        $quote = Quote::inRandomOrder()->first();

        return [
            'quote' => $quote,
        ];
    }
}
