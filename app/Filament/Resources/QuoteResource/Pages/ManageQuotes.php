<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Imports\QuotesImport;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ManageQuotes extends ManageRecords
{
    protected static string $resource = QuoteResource::class;

    public function importQuotes(array $data)
    {
        Excel::import(new QuotesImport, Storage::path($data['file']));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import quotes')
                ->color('success')
                ->icon('heroicon-o-document-plus')
                ->action('importQuotes')
                ->form([
                    FileUpload::make('file')
                        ->disk(Storage::getDefaultDriver())
                        ->label('File')
                        ->rules('required'),
                ]),
        ];
    }
}
