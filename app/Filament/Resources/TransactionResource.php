<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Enums\TransactionCategories;
use App\Enums\TransactionTypes;
use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Helpers\EnumHelper;
use App\Models\Transaction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        $transactionTypesSelect = EnumHelper::enumToArray(TransactionTypes::cases());
        $categoriesSelect = EnumHelper::enumToArray(TransactionCategories::cases());
        $currenciesSelect = EnumHelper::enumToArray(Currencies::cases());

        return $form
            ->schema([
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Select::make('expected_transaction_id')
                    ->relationship('expectedTransaction', 'name'),
                Select::make('budget_id')
                    ->relationship('budget', 'name'),
                Select::make('tally_id')
                    ->relationship('tally', 'name'),
                DateTimePicker::make('date')
                    ->required(),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                Select::make('category')
                    ->options($categoriesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                Textarea::make('description')
                    ->required(),
                Textarea::make('detail'),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->disablePlaceholderSelection()
                    ->required(),
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                TextInput::make('fee')
                    ->numeric(),
                TextInput::make('listed_balance')
                    ->numeric()
                    ->required(),
                Checkbox::make('is_tax_relevant'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name'),
                TextColumn::make('expected_transaction.name'),
                TextColumn::make('budget.name'),
                TextColumn::make('tally.name'),
                TextColumn::make('date')->dateTime(),
                TextColumn::make('type'),
                TextColumn::make('category'),
                TextColumn::make('description'),
                TextColumn::make('amount')->formatStateUsing(fn (Transaction $record): string => $record->formatted_amount),
                TextColumn::make('fee')->formatStateUsing(fn (Transaction $record): string => $record->formatted_fee),
                TextColumn::make('listed_balance')->formatStateUsing(fn (Transaction $record): string => $record->formatted_listed_balance),
                TextColumn::make('is_tax_relevant')->formatStateUsing(fn (Transaction $record): string => $record->is_tax_relevant ? 'true' : 'false'),
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
