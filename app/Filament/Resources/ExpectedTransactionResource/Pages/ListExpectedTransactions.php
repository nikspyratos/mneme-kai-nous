<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionResource\Pages;

use App\Filament\Resources\ExpectedTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpectedTransactions extends ListRecords
{
    protected static string $resource = ExpectedTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
