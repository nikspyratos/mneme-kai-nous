<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PerceptionResource\Pages;
use App\Filament\Resources\PerceptionResource\RelationManagers\QuotesRelationManager;
use App\Models\Perception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerceptionResource extends Resource
{
    protected static ?string $model = Perception::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Perceptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('description')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('description'),
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
            QuotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerceptions::route('/'),
            'create' => Pages\CreatePerception::route('/create'),
            'edit' => Pages\EditPerception::route('/{record}/edit'),
        ];
    }
}
