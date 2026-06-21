<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Părinte')
                    ->placeholder('—')
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Activă')
                    ->boolean(),

                IconColumn::make('is_allowed')
                    ->label('Permisă')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Părinte')
                    ->relationship('parent', 'name')
                    ->searchable(),
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
