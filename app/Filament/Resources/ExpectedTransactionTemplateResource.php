<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\Currencies;
use App\Enumerations\DuePeriods;
use App\Enumerations\TransactionTypes;
use App\Filament\Resources\ExpectedTransactionTemplateResource\Pages\CreateExpectedTransactionTemplate;
use App\Filament\Resources\ExpectedTransactionTemplateResource\Pages\EditExpectedTransactionTemplate;
use App\Filament\Resources\ExpectedTransactionTemplateResource\Pages\ListExpectedTransactionTemplates;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransaction;
use App\Models\ExpectedTransactionTemplate;
use App\Services\TallyRolloverDateCalculator;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ExpectedTransactionTemplateResource extends Resource
{
    protected static ?string $model = ExpectedTransactionTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());
        $duePeriodSelect = EnumHelper::enumToFilamentOptionArray(DuePeriods::cases());

        return $form
            ->schema([
                TextInput::make('budget_id'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->selectablePlaceholder()
                    ->required(),
                TextInput::make('amount')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->numeric()
                    ->required(),
                Select::make('due_period')
                    ->options($duePeriodSelect),
                TextInput::make('due_day')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31),
                TextInput::make('group'),
                TextInput::make('identifier'),
                TextInput::make('identifier_transaction_type'),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->selectablePlaceholder(),
                Repeater::make('identifier')
                    ->schema([
                        TextInput::make('identifier'),
                    ])
                    ->columns(1),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->selectablePlaceholder(),
                Checkbox::make('enabled')
                    ->default(true),
                Checkbox::make('is_tax_relevant')
                    ->default(false),
                Checkbox::make('is_paid')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->toggleable(true, true),
                TextColumn::make('group'),
                TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransactionTemplate $record): string => $record->formatted_amount),
                TextColumn::make('due_day_formatted')->label('Due'),
                ToggleColumn::make('enabled'),
                ToggleColumn::make('is_tax_relevant'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('create_instance')
                    ->label('Create instance')
                    ->form([
                        DatePicker::make('due_date')->label('Due Date')->required(),
                    ])
                    ->action(function (ExpectedTransactionTemplate $record, array $data) {
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
                    })
                    ->icon('heroicon-o-clipboard')
                    ->color('success'),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpectedTransactionTemplates::route('/'),
            'create' => CreateExpectedTransactionTemplate::route('/create'),
            'edit' => EditExpectedTransactionTemplate::route('/{record}/edit'),
        ];
    }
}
