<?php

namespace App\Filament\Resources\Vouchers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Read-only redemption history. Usages are written at checkout — never created,
 * edited, or deleted from the admin (they are an audit trail).
 */
class UsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'usages';

    protected static ?string $title = 'Utilizări (istoric)';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Client')
                    ->placeholder('—'),

                TextColumn::make('order.order_number')
                    ->label('Comandă')
                    ->placeholder('—'),

                TextColumn::make('discount')
                    ->label('Reducere aplicată')
                    ->money('RON'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
