<?php

namespace App\Filament\Resources\TallyResource\Pages;

use App\Filament\Resources\TallyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTally extends EditRecord
{
    protected static string $resource = TallyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['amount'] *= 100;
        $record->update($data);

        return $record;
    }
}
