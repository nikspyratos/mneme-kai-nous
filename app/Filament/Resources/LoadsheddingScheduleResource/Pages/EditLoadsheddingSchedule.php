<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoadsheddingScheduleResource\Pages;

use App\Filament\Resources\LoadsheddingScheduleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoadsheddingSchedule extends EditRecord
{
    protected static string $resource = LoadsheddingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
