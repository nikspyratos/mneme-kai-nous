<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TallyResource\Pages;
use App\Models\Tally;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class TallyResource extends Resource
{
    protected static ?string $model = Tally::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
