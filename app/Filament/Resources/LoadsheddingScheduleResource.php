<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LoadsheddingScheduleResource\Pages\CreateLoadsheddingSchedule;
use App\Filament\Resources\LoadsheddingScheduleResource\Pages\EditLoadsheddingSchedule;
use App\Filament\Resources\LoadsheddingScheduleResource\Pages\ListLoadsheddingSchedules;
use App\Models\LoadsheddingSchedule;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                TextInput::make('name')
                    ->required(),
                TextInput::make('zone')
                    ->required(),
                TextInput::make('api_id')
                    ->required(),
                TextInput::make('region')
                    ->required(),
                Checkbox::make('is_home')
                    ->required(),
                Checkbox::make('enabled')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('zone')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('api_id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('region')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('today_times')
                    ->formatStateUsing(fn (LoadsheddingSchedule $record): string => $record->today_times_formatted)
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('is_home'),
                ToggleColumn::make('enabled'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoadsheddingSchedules::route('/'),
            'create' => CreateLoadsheddingSchedule::route('/create'),
            'edit' => EditLoadsheddingSchedule::route('/{record}/edit'),
        ];
    }
}
