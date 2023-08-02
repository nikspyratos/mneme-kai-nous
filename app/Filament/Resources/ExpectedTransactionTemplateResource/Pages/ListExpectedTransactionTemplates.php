<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;

use App\Actions\CreateExpectedTransactionSummaryMarkdown;
use App\Filament\Resources\ExpectedTransactionTemplateResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\Carbon;

class ListExpectedTransactionTemplates extends ListRecords
{
    protected static string $resource = ExpectedTransactionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('show_summary')
                ->label('Show Summary')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->action(fn () => [])
                ->form([
                    Textarea::make('summary')
                        ->default(
                            view(
                                'markdown_expected_transactions',
                                CreateExpectedTransactionSummaryMarkdown::run(Carbon::today())
                            )->render()
                        ),
                ]),
        ];
    }
}
