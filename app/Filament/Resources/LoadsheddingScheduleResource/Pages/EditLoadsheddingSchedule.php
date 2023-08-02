<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoadsheddingScheduleResource\Pages;

use App\Filament\Resources\LoadsheddingScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLoadsheddingSchedule extends EditRecord
{
    protected static string $resource = LoadsheddingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
