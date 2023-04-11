<?php

declare(strict_types=1);

namespace App\Filament\Resources\TallyResource\Pages;

use App\Filament\Resources\TallyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTallies extends ListRecords
{
    protected static string $resource = TallyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
