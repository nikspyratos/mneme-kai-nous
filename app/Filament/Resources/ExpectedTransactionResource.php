<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Enums\DuePeriods;
use App\Enums\TransactionTypes;
use App\Filament\Resources\ExpectedTransactionResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\Budget;
use App\Models\ExpectedTransaction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ExpectedTransactionResource extends Resource
{
    protected static ?string $model = ExpectedTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());
        $duePeriodSelect = EnumHelper::enumToFilamentOptionArray(DuePeriods::cases());

        return $form
            ->schema([
                Select::make('budget_id')
                    ->relationship('budget', 'name'),
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
                    ->numeric()
                    ->required(),
                Select::make('due_period')
                    ->options($duePeriodSelect),
                TextInput::make('due_day')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31),
                DatePicker::make('next_due_date'),
                TextInput::make('group'),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
                Repeater::make('identifier')
                    ->schema([
                        TextInput::make('identifier'),
                    ])
                    ->columns(1),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->disablePlaceholderSelection(),
                Checkbox::make('enabled')
                    ->default(true),
                Checkbox::make('is_tax_relevant')
                    ->default(false),
                Checkbox::make('is_paid')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        $budgetSelect = Budget::all()->pluck('name', 'id')->toArray();

        return $table
            ->columns([
                SelectColumn::make('budget_id')
                    ->label('Budget')
                    ->options($budgetSelect)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name'),
                TextColumn::make('description')->limit(20),
                TextColumn::make('group'),
                TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->formatted_amount),
                TextColumn::make('due_period'),
                TextColumn::make('due_day'),
                TextColumn::make('next_due_date')->date(),
                ToggleColumn::make('enabled'),
                TextColumn::make('type'),
                ToggleColumn::make('is_tax_relevant'),
                ToggleColumn::make('is_paid'),
            ])
            ->filters([
                Filter::make('is_recurring')
                    ->label('Is Recurring')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('due_period')),
                Filter::make('is_paid')
                    ->label('Is Paid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', true)),
                Filter::make('unpaid')
                    ->label('Unpaid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', false)),
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
