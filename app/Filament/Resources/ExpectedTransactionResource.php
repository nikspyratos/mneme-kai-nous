<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Enums\DuePeriods;
use App\Enums\TransactionTypes;
use App\Filament\Resources\ExpectedTransactionResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransaction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ExpectedTransactionResource extends Resource
{
    protected static ?string $model = ExpectedTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToArray(TransactionTypes::cases());
        $duePeriodSelect = EnumHelper::enumToArray(DuePeriods::cases());

        return $form
            ->schema([
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
                    ->required(),
                Select::make('due_period')
                    ->options($duePeriodSelect)
                    ->disablePlaceholderSelection(),
                TextInput::make('due_day')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31),
                TextInput::make('group'),
                TextInput::make('identifier'),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
                Checkbox::make('enabled')
                    ->default(true),
                Checkbox::make('is_tax_relevant')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->formatted_amount),
                TextColumn::make('due_period'),
                TextColumn::make('due_day'),
                TextColumn::make('identifier'),
                TextColumn::make('identifier_transaction_type'),
                TextColumn::make('enabled')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->enabled ? 'true' : 'false'),
                TextColumn::make('type'),
                TextColumn::make('is_tax_relevant')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->is_tax_relevant ? 'true' : 'false'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListExpectedTransactions::route('/'),
            'create' => Pages\CreateExpectedTransaction::route('/create'),
            'edit' => Pages\EditExpectedTransaction::route('/{record}/edit'),
        ];
    }
}
