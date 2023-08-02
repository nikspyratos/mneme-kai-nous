<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enumerations\Currencies;
use App\Filament\Resources\TallyResource\Pages\CreateTally;
use App\Filament\Resources\TallyResource\Pages\EditTally;
use App\Filament\Resources\TallyResource\Pages\ListTallies;
use App\Filament\Resources\TallyResource\RelationManagers\TransactionsRelationManager;
use App\Helpers\EnumHelper;
use App\Models\Tally;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TallyResource extends Resource
{
    protected static ?string $model = Tally::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        $currenciesSelect = EnumHelper::enumToFilamentOptionArray(Currencies::cases());

        return $form
            ->schema([
                Select::make('budgetId')
                    ->relationship('budget', 'name')
                    ->required()
                    ->preload(),
                TextInput::make('name')
                    ->required(),
                Select::make('currency')
                    ->options($currenciesSelect)
                    ->selectablePlaceholder()
                    ->required(),
                TextInput::make('balance')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->numeric()
                    ->required(),
                TextInput::make('limit')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
                    ->numeric()
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget.name'),
                TextColumn::make('name'),
                TextColumn::make('balance')->formatStateUsing(fn (Tally $record): string => $record->formatted_balance),
                TextColumn::make('limit')->formatStateUsing(fn (Tally $record): string => $record->formatted_limit),
                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date(),
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
            'index' => ListTallies::route('/'),
            'create' => CreateTally::route('/create'),
            'edit' => EditTally::route('/{record}/edit'),
        ];
    }
}
