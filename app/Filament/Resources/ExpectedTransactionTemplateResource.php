<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\Currencies;
use App\Enumerations\DuePeriods;
use App\Enumerations\TransactionTypes;
use App\Filament\Resources\ExpectedTransactionTemplateResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransactionTemplate;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                Forms\Components\TextInput::make('budget_id'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->disablePlaceholderSelection()
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
                Forms\Components\TextInput::make('identifier'),
                Forms\Components\TextInput::make('identifier_transaction_type'),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
                Repeater::make('identifier')
                    ->schema([
                        TextInput::make('identifier'),
                    ])
                    ->columns(1),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
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
                Tables\Columns\TextColumn::make('budget_id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('group'),
                TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransactionTemplate $record): string => $record->formatted_amount),
                Tables\Columns\TextColumn::make('due_period'),
                Tables\Columns\TextColumn::make('due_day'),
                Tables\Columns\TextColumn::make('identifier'),
                Tables\Columns\TextColumn::make('identifier_transaction_type'),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\IconColumn::make('is_tax_relevant')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('create_instance')
                    ->label('Create instance')
                    ->form([
                        DateTimePicker::make('due_date')->label('Due Date')->required(),
                    ])
                    ->action('createExpectedTransactionInstance')
                    ->icon('heroicon-o-clipboard')
                    ->color('success'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListExpectedTransactionTemplates::route('/'),
            'create' => Pages\CreateExpectedTransactionTemplate::route('/create'),
            'edit' => Pages\EditExpectedTransactionTemplate::route('/{record}/edit'),
        ];
    }
}
