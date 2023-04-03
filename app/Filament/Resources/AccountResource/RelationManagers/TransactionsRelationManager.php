<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\Enums\TransactionCategories;
use App\Helpers\EnumHelper;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
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
                TextColumn::make('description'),
                SelectColumn::make('tally_id')
                    ->label('Tally')
                    ->options($talliesSelect)
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('category')
                    ->options($transactionCategoriesSelect)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
