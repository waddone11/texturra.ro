<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderProduct;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Read-only display of order lines, incl. custom dimensions from order_product.meta.
 * No create/edit/delete/attach — orders are built at checkout, never touched here.
 */
class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    protected static ?string $title = 'Produse comandate';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produs')
                    ->placeholder('— produs șters —')
                    ->wrap(),

                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->alignCenter(),

                TextColumn::make('price')
                    ->label('Preț')
                    ->money('RON'),

                // Custom dimensions parsed from the pivot `meta` JSON.
                TextColumn::make('meta')
                    ->label('Dimensiuni / Manoperă')
                    ->state(function (OrderProduct $record): string {
                        $m = json_decode($record->meta ?? '{}', true) ?: [];

                        if (empty($m['is_custom'])) {
                            return 'Standard';
                        }

                        $parts = [];
                        if (! empty($m['length']) && ! empty($m['height'])) {
                            $parts[] = $m['length'] . ' × ' . $m['height'] . ' m';
                        }
                        if (! empty($m['manufactoring_type_name'])) {
                            $man = $m['manufactoring_type_name'];
                            if (isset($m['manufactoring_price'])) {
                                $man .= ' (' . number_format((float) $m['manufactoring_price'], 2) . ' RON)';
                            }
                            $parts[] = $man;
                        }

                        return $parts ? implode(' · ', $parts) : 'Custom';
                    })
                    ->badge()
                    ->color(fn (OrderProduct $record): string => (json_decode($record->meta ?? '{}', true)['is_custom'] ?? false) ? 'warning' : 'gray')
                    ->wrap(),
            ])
            // fully read-only
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
