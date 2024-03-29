<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoadsheddingScheduleResource\Pages;

use App\Filament\Resources\LoadsheddingScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoadsheddingSchedules extends ListRecords
{
    protected static string $resource = LoadsheddingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
