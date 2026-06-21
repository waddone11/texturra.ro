<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Cod')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('value')
                    ->label('Reducere')
                    ->state(fn ($record): string => $record->discount_amount
                        ? number_format((float) $record->discount_amount, 2) . ' RON'
                        : (($record->discount_percentage ?? 0) . '%')),

                TextColumn::make('usages_count')
                    ->label('Utilizări')
                    ->counts('usages')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record): string => $state . ' / ' . $record->usage_limit),

                TextColumn::make('valid_to')
                    ->label('Expiră')
                    ->date('d.m.Y')
                    ->placeholder('—')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('Activ')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('active')
                    ->label('Activ'),
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
