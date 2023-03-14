<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Enums\TransactionTypes;
use App\Filament\Resources\BudgetResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\Budget;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());

        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                TextInput::make('amount')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->required(),
                TextInput::make('identifier'),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
                Checkbox::make('enabled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')->formatStateUsing(fn (Budget $record): string => $record->formatted_amount),
                TextColumn::make('identifier'),
                TextColumn::make('identifier_transaction_type'),
                TextColumn::make('enabled')->formatStateUsing(fn (Budget $record): string => $record->enabled ? 'true' : 'false'),
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
