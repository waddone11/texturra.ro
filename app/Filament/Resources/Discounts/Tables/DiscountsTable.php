<?php

namespace App\Filament\Resources\Discounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target')
                    ->label('Țintă')
                    ->state(fn ($record): string => $record->product?->name
                        ?? $record->category?->name
                        ?? '— global —')
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->state(fn ($record): string => $record->percentage !== null ? 'Procent' : 'Sumă fixă')
                    ->color(fn ($record): string => $record->percentage !== null ? 'info' : 'warning'),

                TextColumn::make('value')
                    ->label('Valoare')
                    ->state(fn ($record): string => $record->percentage !== null
                        ? rtrim(rtrim((string) $record->percentage, '0'), '.') . '%'
                        : number_format((float) $record->fixed_amount, 2) . ' RON'),

                TextColumn::make('start_date')
                    ->label('De la')
                    ->date('d.m.Y')
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label('Până la')
                    ->date('d.m.Y')
                    ->placeholder('—'),

                TextColumn::make('active_now')
                    ->label('Activă acum')
                    ->badge()
                    ->state(function ($record): string {
                        // Discount has no date casts → attributes are raw strings; parse safely.
                        $now = now()->toDateString();
                        $from = $record->start_date ? \Illuminate\Support\Carbon::parse($record->start_date)->toDateString() : null;
                        $to = $record->end_date ? \Illuminate\Support\Carbon::parse($record->end_date)->toDateString() : null;

                        return (! $from || $from <= $now) && (! $to || $to >= $now) ? 'Da' : 'Nu';
                    })
                    ->color(fn (string $state): string => $state === 'Da' ? 'success' : 'gray'),
            ])
            ->defaultSort('end_date', 'desc')
            ->filters([
                Filter::make('active_now')
                    ->label('Active acum')
                    ->query(fn (Builder $query): Builder => $query
                        ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
                        ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))),
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
