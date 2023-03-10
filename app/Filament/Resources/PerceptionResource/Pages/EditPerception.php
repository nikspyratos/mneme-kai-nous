<?php

namespace App\Filament\Resources\PerceptionResource\Pages;

use App\Filament\Resources\PerceptionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerception extends EditRecord
{
    protected static string $resource = PerceptionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
