<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\AccountTypes;
use App\Enumerations\Banks;
use App\Enumerations\Currencies;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Filament\Resources\AccountResource\RelationManagers\TransactionsRelationManager;
use App\Helpers\EnumHelper;
use App\Models\Account;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Finance';

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
                        ->selectablePlaceholder(),
                    TextInput::make('account_number'),
                    Select::make('currency')
                        ->options($currenciesSelect)
                        ->default(Currencies::RANDS->value)
                        ->selectablePlaceholder()
                        ->required(),
                    TextInput::make('balance')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state($state / 100)
                                ->mask(RawJs::make(<<<'JS'
                                        $money($input, '.', ',', 2)
                                  JS));
                        }),
                    Select::make('type')
                        ->options($typesSelect)
                        ->selectablePlaceholder()
                        ->reactive()
                        ->required(),
                    TextInput::make('debt')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state($state / 100)
                                ->mask(RawJs::make(<<<'JS'
                                        $money($input, '.', ',', 2)
                                  JS));
                        })
                        ->visible(fn (Get $get): bool => in_array($get('type'), [AccountTypes::DEBT->value, AccountTypes::CREDIT->value])),
                    TextInput::make('bank_identifier'),
                    Checkbox::make('has_overdraft')->reactive(),
                    TextInput::make('overdraft_amount')

                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state($state / 100)
                                ->mask(RawJs::make(<<<'JS'
                                    $money($input, '.', ',', 2)
                                JS));
                        })->hidden(fn (Get $get) => $get('has_overdraft') == false),
                    Checkbox::make('is_primary'),
                    Checkbox::make('is_main')->rules([function (Account $record) {
                        return function (string $attribute, $value, Closure $fail) use ($record) {
                            if ($value) {
                                Account::where('id', '!=', $record->id)->whereIsMain(true)->doesntExist() ?: $fail(
                                    'There is already a main account.'
                                );
                            }
                        };
                    }]),
                ]),
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
                ToggleColumn::make('has_overdraft'),
                TextColumn::make('overdraft_amount')->formatStateUsing(fn (Account $record): string => $record->formatted_overdraft_amount),
                TextColumn::make('bank_identifier'),
                TextColumn::make('type'),
                ToggleColumn::make('is_primary'),
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
            TransactionsRelationManager::class,
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
