<?php

namespace App\Filament\Resources\Colors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ColorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('cod_css')
                    ->label('Swatch'),

                TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('group.name')
                    ->label('Grup')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('cod_css')
                    ->label('Cod CSS')
                    ->badge()
                    ->toggleable(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('color_group_id')
                    ->label('Grup')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
            ])
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
