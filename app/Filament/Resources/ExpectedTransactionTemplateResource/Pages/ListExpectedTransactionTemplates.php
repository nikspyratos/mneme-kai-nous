<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;

use App\Actions\CreateExpectedTransactionSummaryMarkdown;
use App\Filament\Resources\ExpectedTransactionTemplateResource;
use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use App\Services\TallyRolloverDateCalculator;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ListExpectedTransactionTemplates extends ListRecords
{
    protected static string $resource = ExpectedTransactionTemplateResource::class;

    public function createExpectedTransactionInstance(ExpectedTransactionTemplate $record, array $data)
    {
        $dueDate = Carbon::parse($data['due_date']);
        if ($dueDate->day > TallyRolloverDateCalculator::getRolloverDay()) {
            $monthNameDate = TallyRolloverDateCalculator::getNextDate($dueDate);
        } else {
            $monthNameDate = $dueDate;
        }
        ExpectedTransaction::create(
            array_merge(
                Arr::only($record->toArray(), get_fillable(ExpectedTransaction::class)),
                [
                    'name' => $record->name . ': ' . $monthNameDate->monthName . ' ' . $monthNameDate->year,
                    'next_due_date' => $dueDate,
                    'is_paid' => false,
                ]
            )
        );
    }

    public function showSummary(): string
    {
        return view(
            'markdown_expected_transactions',
            CreateExpectedTransactionSummaryMarkdown::run(Carbon::today())
        )->render();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('show_summary')
                ->label('Show Summary')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->action(fn () => [])
                ->form([
                    Textarea::make('summary')
                        ->default($this->showSummary()),
                ]),
        ];
    }
}
