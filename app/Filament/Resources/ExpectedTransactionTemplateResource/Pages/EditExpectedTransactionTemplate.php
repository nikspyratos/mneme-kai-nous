<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;

use App\Filament\Resources\ExpectedTransactionTemplateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExpectedTransactionTemplate extends EditRecord
{
    protected static string $resource = ExpectedTransactionTemplateResource::class;

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
