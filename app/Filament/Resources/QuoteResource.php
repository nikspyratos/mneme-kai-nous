<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages\ManageQuotes;
use App\Models\Quote;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Perceptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('perception_id')
                    ->relationship('perception', 'name')
                    ->required()
                    ->preload(),
                Textarea::make('content')
                    ->required(),
                TextInput::make('author')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('perception.name'),
                TextColumn::make('content')->words(25),
                TextColumn::make('author'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuotes::route('/'),
        ];
    }
}
