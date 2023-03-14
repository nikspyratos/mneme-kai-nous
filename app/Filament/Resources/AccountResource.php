<?php

namespace App\Filament\Resources;

use App\Enums\AccountTypes;
use App\Enums\Banks;
use App\Enums\Currencies;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Helpers\EnumHelper;
use App\Models\Account;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-library';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        $banksSelect = EnumHelper::enumToArray(Banks::cases());
        $currenciesSelect = EnumHelper::enumToArray(Currencies::cases());
        $typesSelect = EnumHelper::enumToArray(AccountTypes::cases());

        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('bank_name')
                    ->options($banksSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                TextInput::make('account_number')
                    ->required(),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                TextInput::make('balance')
                    ->numeric()
                    ->required(),
                TextInput::make('debt')
                    ->numeric(),
                Select::make('type')
                    ->options($typesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                Checkbox::make('has_overdraft'),
                TextInput::make('bank_identifier')
                   ->required(),
                Checkbox::make('is_primary'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('bank_name'),
                TextColumn::make('account_number'),
                TextColumn::make('balance')->formatStateUsing(fn (Account $record): string => $record->formatted_balance),
                TextColumn::make('debt')->formatStateUsing(fn (Account $record): string => $record->formatted_debt),
                TextColumn::make('type'),
                TextColumn::make('has_overdraft')->formatStateUsing(fn (Account $record): string => $record->has_overdraft ? 'true' : 'false'),
                TextColumn::make('bank_identifier'),
                TextColumn::make('type'),
                TextColumn::make('is_primary')->formatStateUsing(fn (Account $record): string => $record->is_primary ? 'true' : 'false'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}
