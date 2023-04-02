<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Enums\TransactionCategories;
use App\Enums\TransactionTypes;
use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use App\Models\Transaction;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());
        $categoriesSelect = EnumHelper::enumToFilamentOptionArray(TransactionCategories::cases());
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());

        return $form
            ->schema([
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Select::make('expected_transactions')
                    ->multiple()
                    ->relationship('expectedTransactions', 'name')
                    ->preload(),
                Select::make('tally_id')
                    ->relationship('tally', 'name', fn (Builder $query) => $query->forCurrentBudgetMonth()),
                DateTimePicker::make('date')
                    ->required(),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->default(TransactionTypes::DEBIT->value)
                    ->required(),
                Select::make('category')
                    ->options($categoriesSelect)
                    ->disablePlaceholderSelection(),
                Textarea::make('description')
                    ->required(),
                Textarea::make('detail'),
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
                TextInput::make('fee')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->numeric(),
                TextInput::make('listed_balance')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->numeric()
                    ->required(),
                Checkbox::make('is_tax_relevant'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $transactionCategoriesSelect = EnumHelper::enumToFilamentOptionArray(TransactionCategories::cases());
        $expectedTransactionSelect = ExpectedTransaction::all()->pluck('name', 'id')->toArray();
        $talliesSelect = Tally::all()->pluck('name', 'id')->toArray();

        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('account.name')
                    ->sortable()
                    ->limit(20),
                TextColumn::make('amount')->formatStateUsing(fn (Transaction $record): string => $record->formatted_amount),
                SelectColumn::make('tally_id')
                    ->label('Tally')
                    ->options($talliesSelect)
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('category')
                    ->options($transactionCategoriesSelect)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type'),
                TextColumn::make('description')->limit(20),
                ToggleColumn::make('is_tax_relevant'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('account_id')
                    ->relationship('account', 'name'),
                SelectFilter::make('expected_transaction_id')
                    ->relationship('expectedTransaction', 'name'),
                SelectFilter::make('tally_id')
                    ->relationship('tally', 'name', fn (Builder $query) => $query->forCurrentBudgetMonth()),
                SelectFilter::make('type')
                    ->options(EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases()))
                    ->attribute('type'),
                SelectFilter::make('category')
                    ->options(EnumHelper::enumToFilamentOptionArray(TransactionCategories::cases()))
                    ->attribute('category'),
                Filter::make('No Tally')->query(fn (Builder $query) => $query->whereNull('tally_id')),
                Filter::make('No Expected Transaction')->query(fn (Builder $query) => $query->whereNull('expected_transaction_id')),
                Filter::make('No Category')->query(fn (Builder $query) => $query->whereNull('category')->orWhereNotIn('category', TransactionCategories::values())),
                Filter::make('Tax Relevant')->query(fn (Builder $query) => $query->where('is_tax_relevant', true)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('Expected Transactions')
                    ->mountUsing((fn (ComponentContainer $form, Transaction $record) => $form->fill([
                        'expected_transactions' => $record->expectedTransactions->pluck('id')->toArray(),
                    ])))
                    ->form([
                        Select::make('expected_transactions')
                            ->label('Expected Transactions')
                            ->options($expectedTransactionSelect)
                            ->multiple()
                            ->searchable()
                            ->rules('required'),
                    ])
                    ->action('updateExpectedTransactions')
                    ->icon('heroicon-o-clipboard')
                    ->color('success'),
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
