<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Storage;

class Dashboard extends BasePage
{
    public function downloadDatabase()
    {
        //TODO Find a way to make this work
        return Storage::disk('database')->download('database.sqlite');
    }

    protected function getColumns(): int|array
    {
        return 5;
    }

    protected function getActions(): array
    {
        return [
//            Filament\Pages\Actions\Action::make('download_database')
//                ->label('Download Database')
//                ->color('success')
//                ->icon('heroicon-o-database')
//                ->action('downloadDatabase'),
        ];
    }
}
