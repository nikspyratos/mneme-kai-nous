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
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
                    ->selectablePlaceholder(),
                Textarea::make('description')
                    ->required(),
                Textarea::make('detail'),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->selectablePlaceholder()
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
                    ->toggleable(true, true)
                    ->limit(20),
                TextColumn::make('formatted_amount_with_signs')
                    ->label('Amount'),
                IconColumn::make('categorised')
                    ->boolean()
                    ->state(fn (Transaction $record) => $record->expectedTransactions()->exists() || ($record->category && $record->tally_id)),
                TextColumn::make('description')->limit(20)->searchable(),
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
                Filter::make('Has Category')->query(fn (Builder $query) => $query->whereIn('category', TransactionCategories::values())),
                Filter::make('No Category')->query(fn (Builder $query) => $query->whereNull('category')->orWhereNotIn('category', TransactionCategories::values())),
                /** @phpstan-ignore-next-line */ //https://github.com/nunomaduro/larastan/issues/1110
                Filter::make('Current Budget Month')->query(fn (Builder $query) => $query->inCurrentBudgetMonth()),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Categorise')
                        ->form([
                            Select::make('tally_id')
                                ->label('Tally')
                                ->options($talliesSelect)
                                ->required(),
                            Select::make('category')
                                ->options($transactionCategoriesSelect),
                        ])
                        ->icon('heroicon-o-inbox-stack')
                        ->color('success')
                        ->action(fn (Transaction $record, array $data) => $record->update($data)),
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
                        ->action(function (Transaction $record, array $data) {
                            $amount = $data['amount'] * 100;
                            Transaction::create([
                                'parent_id' => $record->id,
                                'account_id' => $record->account_id,
                                'tally_id' => $record->tally_id,
                                'date' => $record->date,
                                'type' => $record->type,
                                'category' => $record->category,
                                'description' => $record->description,
                                'detail' => $data['detail'],
                                'currency' => $record->currency,
                                'amount' => $amount,
                                'fee' => $record->fee,
                                'listed_balance' => $record->listed_balance,
                                'data' => $record->data,
                                'is_tax_relevant' => $record->is_tax_relevant,
                            ]);
                            $record->update([
                                'amount' => $record->amount - $amount,
                            ]);
                        })
                        ->icon('heroicon-o-squares-plus')
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
                        ->action(function (Transaction $record, array $data) {
                            $amount = $data['amount'] * 100;
                            $expectedTransaction = ExpectedTransaction::create([
                                'name' => 'Owed for transaction ' . $record->id,
                                'description' => $record->detail,
                                'group' => 'Owed',
                                'currency' => $record->currency,
                                'amount' => $amount,
                                'is_paid' => $data['is_paid'],
                                'date' => $record->date,
                                'type' => TransactionTypes::getOppositeType(TransactionTypes::from($record->type)),
                                'is_tax_relevant' => $record->is_tax_relevant,
                            ]);
                            $record->expectedTransactions()->attach($expectedTransaction->id);
                            $record->tally->update([
                                'balance' => $record->tally->balance - $amount,
                            ]);
                        })
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
