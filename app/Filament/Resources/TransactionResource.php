<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Models\Transaction;
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
        return $form
            ->schema([
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                DateTimePicker::make('date')
                    ->required(),
                Textarea::make('description')
                    ->required(),
                Textarea::make('detail'),
                TextInput::make('currency')
                    ->required(),
                TextInput::make('amount')
                    ->required(),
                TextInput::make('listed_balance')
                    ->required(),
                Select::make('expense_id')
                    ->relationship('expense', 'name'),
                Select::make('budget_id')
                    ->relationship('budget', 'name'),
                Select::make('tally_id')
                    ->relationship('tally', 'name'),
                TextInput::make('type'),
                TextInput::make('fee'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name'),
                TextColumn::make('date')
                    ->dateTime(),
                TextColumn::make('description'),
                TextColumn::make('detail'),
                TextColumn::make('currency'),
                TextColumn::make('amount'),
                TextColumn::make('listed_balance'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
                TextColumn::make('expense.name'),
                TextColumn::make('budget.name'),
                TextColumn::make('tally.name'),
                TextColumn::make('type'),
                TextColumn::make('fee'),
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
