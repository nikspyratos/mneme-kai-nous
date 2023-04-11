<?php

declare(strict_types=1);

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getActions(): array
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
        if (isset($data['debt'])) {
            $data['debt'] *= 100;
        }
        if (isset($data['overdraft_amount'])) {
            $data['overdraft_amount'] *= 100;
        }
        $record->update($data);

        return $record;
    }
}
