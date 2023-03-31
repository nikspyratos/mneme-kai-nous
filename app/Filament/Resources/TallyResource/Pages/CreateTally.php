<?php

namespace App\Filament\Resources\TallyResource\Pages;

use App\Filament\Resources\TallyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTally extends CreateRecord
{
    protected static string $resource = TallyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['balance'] *= 100;
        $data['limit'] *= 100;

        return static::getModel()::create($data);
    }
}
