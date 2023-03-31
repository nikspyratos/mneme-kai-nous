<?php

namespace App\Filament\Resources\ExpectedTransactionResource\Pages;

use App\Filament\Resources\ExpectedTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateExpectedTransaction extends CreateRecord
{
    protected static string $resource = ExpectedTransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['amount'] *= 100;

        return static::getModel()::create($data);
    }
}
