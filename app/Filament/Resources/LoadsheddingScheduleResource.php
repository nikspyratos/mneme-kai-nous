<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LoadsheddingScheduleResource\Pages;
use App\Models\LoadsheddingSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoadsheddingScheduleResource extends Resource
{
    protected static ?string $model = LoadsheddingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
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
                Forms\Components\Checkbox::make('is_home')
                    ->required(),
                Forms\Components\Checkbox::make('enabled')
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
                Tables\Columns\ToggleColumn::make('is_home'),
                Tables\Columns\ToggleColumn::make('enabled'),
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
