<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Color;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ColorsRelationManager extends RelationManager
{
    protected static string $relationship = 'colors';

    protected static ?string $title = 'Culori (paletar + stoc)';

    // Edit form for an attached color = its pivot stock.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('stock')
                    ->label('Stoc')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ColorColumn::make('cod_css')
                    ->label('Swatch'),

                TextColumn::make('name')
                    ->label('Culoare')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('group.name')
                    ->label('Grup')
                    ->sortable()
                    ->placeholder('—'),

                // distinct key from the swatch (same-name columns collide — 4b lesson)
                TextColumn::make('hex')
                    ->label('Cod CSS')
                    ->state(fn (Color $record): string => $record->cod_css)
                    ->badge(),

                // pivot stock — resolved explicitly via the pivot accessor
                TextColumn::make('stock')
                    ->label('Stoc')
                    ->state(fn (Color $record): int => (int) ($record->pivot->stock ?? 0))
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name')
            ->headerActions([
                // attach a color from the shared 93-color palette, with its stock
                AttachAction::make()
                    ->label('Atașează culoare')
                    ->recordSelectSearchColumns(['name'])
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()->label('Culoare din paletar'),
                        TextInput::make('stock')
                            ->label('Stoc')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->after(fn () => $this->recomputeOwnerStock()),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn () => $this->recomputeOwnerStock()),
                DetachAction::make()
                    ->after(fn () => $this->recomputeOwnerStock()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->after(fn () => $this->recomputeOwnerStock()),
                ]),
            ]);
    }

    /**
     * Keep the stored Product.general_stock = SUM(product_color.stock) so the
     * frontend (which still reads the column) stays correct. Admin-only change.
     */
    protected function recomputeOwnerStock(): void
    {
        $product = $this->getOwnerRecord();
        $sum = (int) DB::table('product_color')->where('product_id', $product->getKey())->sum('stock');
        $product->update(['general_stock' => $sum]);
    }
}
