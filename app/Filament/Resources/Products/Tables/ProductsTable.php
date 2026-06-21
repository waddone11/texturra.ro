<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable()
                    ->limit(45),

                TextColumn::make('category.name')
                    ->label('Categorie')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->label('Mod')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'custom' => 'Custom',
                        'standard' => 'Standard',
                        default => $state,
                    })
                    ->color(fn (string $state): string => $state === 'custom' ? 'warning' : 'gray'),

                TextColumn::make('price')
                    ->label('Preț')
                    ->money('RON')
                    ->sortable(),

                TextColumn::make('general_stock')
                    ->label('Stoc')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Activ')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Mod')
                    ->options([
                        'custom' => 'Custom',
                        'standard' => 'Standard',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
