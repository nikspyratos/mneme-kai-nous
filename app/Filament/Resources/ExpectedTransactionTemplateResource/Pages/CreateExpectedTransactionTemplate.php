<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;

use App\Filament\Resources\ExpectedTransactionTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateExpectedTransactionTemplate extends CreateRecord
{
    protected static string $resource = ExpectedTransactionTemplateResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['amount'] *= 100;

        return static::getModel()::create($data);
    }
}
