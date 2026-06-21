<?php

namespace App\Filament\Resources\ColorGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nume grup')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('colors_count')
                    ->label('Nr. culori')
                    ->counts('colors')
                    ->badge()
                    ->sortable(),

                TextColumn::make('image_path')
                    ->label('Cale imagine')
                    ->placeholder('—')
                    ->toggleable()
                    ->limit(40),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
