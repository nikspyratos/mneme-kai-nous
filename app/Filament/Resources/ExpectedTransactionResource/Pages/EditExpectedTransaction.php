<?php

namespace App\Filament\Resources\ExpectedTransactionResource\Pages;

use App\Filament\Resources\ExpectedTransactionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExpectedTransaction extends EditRecord
{
    protected static string $resource = ExpectedTransactionResource::class;

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
