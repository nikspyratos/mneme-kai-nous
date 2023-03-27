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
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-library';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        $banksSelect = EnumHelper::enumToFilamentOptionArray(Banks::cases());
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $typesSelect = EnumHelper::enumToFilamentOptionArray(AccountTypes::cases());

        return $form
            ->schema([
                Group::make([
                    TextInput::make('name')
                        ->required(),
                    Select::make('bank_name')
                        ->options($banksSelect)
                        ->disablePlaceholderSelection(),
                    TextInput::make('account_number'),
                    Select::make('currency')
                        ->options($currenciesSelect)
                        ->default(Currencies::RANDS->value)
                        ->disablePlaceholderSelection()
                        ->required(),
                    TextInput::make('balance')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state($state / 100)
                                ->mask(fn (TextInput\Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(2) // Set the number of digits after the decimal point.
                                    ->decimalSeparator('.') // Add a separator for decimal numbers.
                                    ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
                                    ->normalizeZeros(false) // Append or remove zeros at the end of the number.
                                    ->padFractionalZeros(false) // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
                                    ->thousandsSeparator(',') // Add a separator for thousands.
                                );
                        }),
                    Select::make('type')
                        ->options($typesSelect)
                        ->disablePlaceholderSelection()
                        ->reactive()
                        ->required(),
                    TextInput::make('debt')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state($state / 100)
                                ->mask(fn (TextInput\Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(2)
                                    ->decimalSeparator('.')
                                    ->normalizeZeros(false)
                                    ->padFractionalZeros(false)
                                    ->thousandsSeparator(',')
                                );
                        })
                        ->visible(fn (Closure $get): bool => $get('type') == AccountTypes::DEBT->value),
                    TextInput::make('bank_identifier'),
                    Checkbox::make('has_overdraft'),
                    Checkbox::make('is_primary'),
                ])
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
                IconColumn::make('has_overdraft')->boolean(),
                TextColumn::make('bank_identifier'),
                TextColumn::make('type'),
                IconColumn::make('is_primary')->boolean(),
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
