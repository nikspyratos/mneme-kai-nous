<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionResource\Pages;

use App\Filament\Resources\ExpectedTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExpectedTransaction extends EditRecord
{
    protected static string $resource = ExpectedTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['amount'] *= 100;
        $record->update($data);

        return $record;
    }
}
