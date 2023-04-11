<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionResource\Pages;

use App\Filament\Resources\ExpectedTransactionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpectedTransactions extends ListRecords
{
    protected static string $resource = ExpectedTransactionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
