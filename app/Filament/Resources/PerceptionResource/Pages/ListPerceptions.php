<?php

namespace App\Filament\Resources\PerceptionResource\Pages;

use App\Filament\Resources\PerceptionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerceptions extends ListRecords
{
    protected static string $resource = PerceptionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
