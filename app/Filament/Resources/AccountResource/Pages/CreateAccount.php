<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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

        return $data;
    }
}
