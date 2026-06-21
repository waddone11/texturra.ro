<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
                // Default to active products only — mirrors the old admin list
                // (archived = status 0 hidden from the default admin view).
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([1 => 'Active', 0 => 'Arhivate'])
                    ->default(1),

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

                // Archive (status->0) / Restore (status->1) — reversible, like the
                // old admin. NO hard delete (products may be referenced in orders).
                Action::make('archive')
                    ->label('Arhivează')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => (bool) $record->status)
                    ->action(fn ($record) => $record->update(['status' => 0])),

                Action::make('restore')
                    ->label('Restaurează')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn ($record): bool => ! $record->status)
                    ->action(fn ($record) => $record->update(['status' => 1])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archive')
                        ->label('Arhivează selectate')
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 0]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
