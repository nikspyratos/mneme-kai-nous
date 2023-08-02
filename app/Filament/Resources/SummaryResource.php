<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SummaryResource\Pages\CreateSummary;
use App\Filament\Resources\SummaryResource\Pages\EditSummary;
use App\Filament\Resources\SummaryResource\Pages\ListSummaries;
use App\Models\Summary;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SummaryResource extends Resource
{
    protected static ?string $model = Summary::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                KeyValue::make('data')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('data')
                    ->label('Data')
                    ->formatStateUsing(fn (Summary $record): string => json_encode($record->data)),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
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
            'index' => ListSummaries::route('/'),
            'create' => CreateSummary::route('/create'),
            'edit' => EditSummary::route('/{record}/edit'),
        ];
    }
}
