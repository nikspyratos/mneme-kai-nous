<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoadsheddingScheduleResource\Pages;
use App\Filament\Resources\LoadsheddingScheduleResource\RelationManagers;
use App\Models\LoadsheddingSchedule;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoadsheddingScheduleResource extends Resource
{
    protected static ?string $model = LoadsheddingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';
    protected static ?string $navigationGroup = 'General';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('zone')
                    ->required(),
                Forms\Components\TextInput::make('api_id')
                    ->required(),
                Forms\Components\TextInput::make('region')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('zone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('api_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('today_times')
                    ->formatStateUsing(fn (LoadsheddingSchedule $record): string => $record->today_times_formatted)
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListLoadsheddingSchedules::route('/'),
            'create' => Pages\CreateLoadsheddingSchedule::route('/create'),
            'edit' => Pages\EditLoadsheddingSchedule::route('/{record}/edit'),
        ];
    }    
}
