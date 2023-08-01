<?php

declare(strict_types=1);

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSummary extends EditRecord
{
    protected static string $resource = SummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
