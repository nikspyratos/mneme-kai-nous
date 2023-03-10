<?php

namespace App\Filament\Resources\TallyResource\Pages;

use App\Filament\Resources\TallyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTally extends EditRecord
{
    protected static string $resource = TallyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
