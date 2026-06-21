<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Comandă')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->description(fn ($record): ?string => $record->user?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('RON')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => OrderForm::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'canceled' => 'danger',
                        'processing' => 'info',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(OrderForm::STATUSES),
            ])
            ->recordActions([
                EditAction::make()->label('Vezi / editează'),
            ]);
        // No delete: orders are financial records (no hard delete, no soft-delete this phase).
    }
}
