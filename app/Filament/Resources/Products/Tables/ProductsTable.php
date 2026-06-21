<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

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

                // Create a sibling product (same model, another dimension). Shares
                // model_group_id; new product_code/slug; inherits the colour palette.
                Action::make('duplicateToSize')
                    ->label('Duplică în altă dimensiune')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nume produs nou')
                            ->required()
                            ->maxLength(255)
                            ->default(fn (Product $record): string => $record->name),
                        TextInput::make('height')
                            ->label('Înălțime / dimensiune (metri)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0.5)
                            ->maxValue(10)
                            ->helperText('Dimensiunea noii variante a acestui model (opțional aici, o poți seta și la editare).'),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $copy = self::duplicateToSize($record, $data);
                        Notification::make()
                            ->success()
                            ->title('Produs duplicat')
                            ->body("„{$copy->name}” creat ca frate (același model, altă dimensiune).")
                            ->send();
                    }),

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

    /**
     * Duplicate a product into a sibling (same model, another dimension).
     *
     * - Source joins/creates a model_group_id; the copy inherits it (they become siblings).
     * - Regenerates the UNIQUE fields (product_code TEX-…, slug via boot) and clears
     *   ones that must not collide / carry over (ean, emag_id, is_synced).
     * - Inherits the colour palette (same model → same colours) with stock 0; the new
     *   dimension's real stock is entered afterwards. general_stock starts at 0.
     * - Material (legacy variations) is NOT copied — that system is being retired; see report.
     */
    public static function duplicateToSize(Product $source, array $data): Product
    {
        if (empty($source->model_group_id)) {
            $source->model_group_id = (string) Str::uuid();
            $source->save();
        }

        $copy = $source->replicate();
        $copy->name = $data['name'] ?? $source->name;
        $copy->slug = null;                                  // regenerated by Product::boot
        $copy->product_code = 'TEX-' . strtoupper(uniqid()); // unique
        $copy->ean = null;                                   // unique — copy gets its own / none
        $copy->emag_id = null;                               // not the same eMAG listing
        $copy->is_synced = 0;
        $copy->general_stock = 0;
        $copy->model_group_id = $source->model_group_id;
        if (! empty($data['height'])) {
            $copy->height = $data['height'];
        }
        $copy->save();

        // Inherit available colours (same model) with stock 0.
        foreach ($source->colors as $color) {
            $copy->colors()->attach($color->id, ['stock' => 0]);
        }

        return $copy;
    }
}
