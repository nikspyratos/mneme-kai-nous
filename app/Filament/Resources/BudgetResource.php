<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\BudgetPeriodTypes;
use App\Enumerations\Currencies;
use App\Enumerations\TransactionTypes;
use App\Filament\Resources\BudgetResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\Budget;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());
        $periodTypesSelect = EnumHelper::enumToFilamentOptionArray(BudgetPeriodTypes::cases());

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
                Select::make('period_type')
                    ->options($periodTypesSelect)
                    ->required(),
                Repeater::make('identifier')
                    ->schema([
                        TextInput::make('identifier'),
                    ])
                    ->columns(1),
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
                TextColumn::make('period_type'),
                TextColumn::make('identifier')
                    ->formatStateUsing(fn (Budget $record): string => $record->identifier_string),
                TextColumn::make('identifier_transaction_type'),
                ToggleColumn::make('enabled'),
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
