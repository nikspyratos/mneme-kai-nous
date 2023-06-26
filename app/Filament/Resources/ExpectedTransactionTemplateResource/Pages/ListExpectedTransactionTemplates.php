<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;

use App\Filament\Resources\ExpectedTransactionTemplateResource;
use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use App\Services\TallyRolloverDateCalculator;
use Filament\Pages\Actions;
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

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
