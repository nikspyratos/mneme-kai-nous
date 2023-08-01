<?php

declare(strict_types=1);

namespace App\Filament\Resources\TallyResource\Pages;

use App\Filament\Resources\TallyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTally extends EditRecord
{
    protected static string $resource = TallyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['balance'])) {
            $data['balance'] *= 100;
        }
        if (isset($data['limit'])) {
            $data['limit'] *= 100;
        }
        $record->update($data);

        return $record;
    }
}
