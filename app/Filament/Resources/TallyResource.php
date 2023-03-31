<?php

namespace App\Filament\Resources;

use App\Enums\Currencies;
use App\Filament\Resources\TallyResource\Pages;
use App\Helpers\EnumHelper;
use App\Models\Tally;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class TallyResource extends Resource
{
    protected static ?string $model = Tally::class;

    protected static ?string $navigationIcon = 'heroicon-o-sort-ascending';

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
                    ->disablePlaceholderSelection()
                    ->required(),
                TextInput::make('balance')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state / 100);
                    })
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
                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListTallies::route('/'),
            'create' => Pages\CreateTally::route('/create'),
            'edit' => Pages\EditTally::route('/{record}/edit'),
        ];
    }
}
