<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\Currencies;
use App\Enumerations\TransactionTypes;
use App\Filament\Resources\ExpectedTransactionResource\Pages\EditExpectedTransaction;
use App\Filament\Resources\ExpectedTransactionResource\Pages\ListExpectedTransactions;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpectedTransactionResource extends Resource
{
    protected static ?string $model = ExpectedTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());
        $transactionTypesSelect = EnumHelper::enumToFilamentOptionArray(TransactionTypes::cases());

        return $form
            ->schema([
                Select::make('tally_id')
                    ->relationship('tally', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
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
                DatePicker::make('next_due_date'),
                TextInput::make('group'),
                Select::make('type')
                    ->options($transactionTypesSelect)
                    ->selectablePlaceholder(),
                Repeater::make('identifier')
                    ->schema([
                        TextInput::make('identifier'),
                    ])
                    ->columns(1),
                Select::make('identifier_transaction_type')
                    ->options($transactionTypesSelect)
                    ->selectablePlaceholder(),
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
                TextColumn::make('template')
                    ->label('Template ID')
                    ->formatStateUsing(fn (ExpectedTransaction $record): string => (string) $record->template?->id),
                SelectColumn::make('tally_id')
                    ->label('Tally')
                    ->options($talliesSelect)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name'),
                TextColumn::make('description')->limit(20),
                TextColumn::make('group'),
                TextColumn::make('amount')->formatStateUsing(fn (ExpectedTransaction $record): string => $record->formatted_amount),
                TextColumn::make('next_due_date')->date(),
                ToggleColumn::make('enabled'),
                TextColumn::make('type'),
                ToggleColumn::make('is_tax_relevant'),
                ToggleColumn::make('is_paid'),
            ])
            ->defaultSort('next_due_date', 'desc')
            ->filters([
                Filter::make('is_paid')
                    ->label('Is Paid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', true)),
                Filter::make('unpaid')
                    ->label('Unpaid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', false)),
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
            'index' => ListExpectedTransactions::route('/'),
            'edit' => EditExpectedTransaction::route('/{record}/edit'),
        ];
    }
}
