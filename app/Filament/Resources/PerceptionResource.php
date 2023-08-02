<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PerceptionResource\Pages\CreatePerception;
use App\Filament\Resources\PerceptionResource\Pages\EditPerception;
use App\Filament\Resources\PerceptionResource\Pages\ListPerceptions;
use App\Filament\Resources\PerceptionResource\RelationManagers\QuotesRelationManager;
use App\Models\Perception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => ListPerceptions::route('/'),
            'create' => CreatePerception::route('/create'),
            'edit' => EditPerception::route('/{record}/edit'),
        ];
    }
}
