<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\Currencies;
use App\Enumerations\TransactionCategories;
use App\Enumerations\TransactionTypes;
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
use Filament\Tables\Actions\ActionGroup;
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
        $expectedTransactionSelect = ExpectedTransaction::all()->pluck('name', 'id')->toArray();
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());
        $categoriesSelect = EnumHelper::enumToFilamentOptionArray(TransactionCategories::cases());
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());

        return $form
            ->schema([
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Select::make('expected_transactions')
                    ->label('Expected Transactions')
                    ->options($expectedTransactionSelect)
                    ->multiple()
                    ->searchable(),
                Select::make('tally_id')
                    /** @phpstan-ignore-next-line */ //https://github.com/nunomaduro/larastan/issues/1110
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
        $talliesSelect = Tally::forRecentBudgetMonths()->select(['name', 'id', 'start_date', 'end_date'])->get();
        $talliesSelect = $talliesSelect->map(function ($tally) {
            return [
                'name' => $tally->name . '(' . $tally->start_date->format('M') . ' - ' . $tally->end_date->format('M') . ')',
                'id' => $tally->id,
            ];
        })->pluck('name', 'id')
        ->toArray();

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
                SelectFilter::make('tally_id')
                    /** @phpstan-ignore-next-line */ //https://github.com/nunomaduro/larastan/issues/1110
                    ->relationship('tally', 'name', fn (Builder $query) => $query->forCurrentBudgetMonth()),
                SelectFilter::make('type')
                    ->options(EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases()))
                    ->attribute('type'),
                SelectFilter::make('category')
                    ->options(EnumHelper::enumToFilamentOptionArray(TransactionCategories::cases()))
                    ->attribute('category'),
                Filter::make('No Tally')->query(fn (Builder $query) => $query->whereNull('tally_id')),
                //                Filter::make('Has Expected Transaction(s)')->query(fn (Builder $query) => $query->whereHas('expectedTransactions')),
                //                Filter::make('No Expected Transaction(s)')->query(fn (Builder $query) => $query->whereDoesntHave('expectedTransactions')),
                Filter::make('Has Category')->query(fn (Builder $query) => $query->whereIn('category', TransactionCategories::values())),
                Filter::make('No Category')->query(fn (Builder $query) => $query->whereNull('category')->orWhereNotIn('category', TransactionCategories::values())),
                Filter::make('Tax Relevant')->query(fn (Builder $query) => $query->where('is_tax_relevant', true)),
                Filter::make('Tax Irrelevant')->query(fn (Builder $query) => $query->where('is_tax_relevant', false)),
                /** @phpstan-ignore-next-line */ //https://github.com/nunomaduro/larastan/issues/1110
                Filter::make('Current Budget Month')->query(fn (Builder $query) => $query->inCurrentBudgetMonth()),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Expected')
                    ->mountUsing((fn (ComponentContainer $form, Transaction $record) => $form->fill([
                        'expected_transactions' => $record->expectedTransactions->pluck('id')->toArray(),
                    ])))
                    ->form([
                        Select::make('expected_transactions')
                            ->label('Expected Transactions')
                            ->relationship('expectedTransactions', 'name')
                            ->multiple()
                            ->searchable()
                            ->required(),
                    ])
                    ->action('updateExpectedTransactions')
                    ->icon('heroicon-o-clipboard')
                    ->color('success'),
                    Action::make('Split')
                        ->form([
                            TextInput::make('detail')
                                ->label('Detail')
                                ->rules('required'),
                            TextInput::make('amount')
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $component->state($state / 100);
                            })
                            ->numeric()
                            ->required(),
                        ])
                        ->action('splitTransaction')
                        ->icon('heroicon-o-view-grid-add')
                        ->color('success'),
                    Action::make('Owed')
                        ->form([
                            TextInput::make('amount')
                                ->afterStateHydrated(function (TextInput $component, $state) {
                                    $component->state($state / 100);
                                })
                            ->numeric()
                            ->required(),
                            Checkbox::make('is_paid'),
                        ])
                        ->action('owedTransaction')
                        ->visible(fn (Transaction $record) => ! is_null($record->tally_id))
                        ->icon('heroicon-o-scale')
                        ->color('success'),
                ]),
                EditAction::make(),
                DeleteAction::make(),
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
