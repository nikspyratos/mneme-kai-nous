<?php

declare(strict_types=1);

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['balance'])) {
            $data['balance'] = (int)Str::replace([',', '.'], '',  $data['balance']) * 100;
        }
        if (isset($data['debt'])) {
            $data['debt'] = (int)Str::replace([',', '.'], '',  $data['debt']) * 100;
        }
        if (isset($data['overdraft_amount'])) {
            $data['overdraft_amount'] = (int)Str::replace([',', '.'], '',  $data['overdraft_amount']) * 100;
        }
        $record->update($data);

        return $record;
    }
}
