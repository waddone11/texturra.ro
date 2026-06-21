<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * "Alte dimensiuni (frați)" — products sharing this product's model_group_id.
 * Attach links an existing product (sets its model_group_id to this group, creating
 * the group on this product if needed); detach clears a sibling's model_group_id.
 * No create/edit/delete here — use "Duplică în altă dimensiune" to spawn a sibling.
 */
class SiblingsRelationManager extends RelationManager
{
    protected static string $relationship = 'modelGroupMembers';

    protected static ?string $title = 'Alte dimensiuni (frați)';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            // hasMany over the shared model_group_id includes self — exclude it.
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereKeyNot($this->getOwnerRecord()->getKey()))
            ->columns([
                TextColumn::make('name')->label('Produs')->wrap(),
                TextColumn::make('height')->label('Înălțime (m)')->placeholder('—'),
                TextColumn::make('price')->label('Preț')->money('RON'),
                TextColumn::make('general_stock')->label('Stoc')->numeric(),
                IconColumn::make('status')->label('Activ')->boolean(),
            ])
            ->emptyStateHeading('Niciun frate')
            ->emptyStateDescription('Folosește „Duplică în altă dimensiune" din lista de produse ca să creezi o altă dimensiune a acestui model, sau atașează un produs existent.')
            ->headerActions([
                Action::make('attachExisting')
                    ->label('Atașează produs existent')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produs')
                            ->required()
                            ->searchable()
                            ->options(fn (): array => Product::query()
                                ->whereKeyNot($this->getOwnerRecord()->getKey())
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all()),
                    ])
                    ->action(function (array $data): void {
                        $owner = $this->getOwnerRecord();
                        $sibling = Product::with('category')->find($data['product_id']);

                        // Siblings are meant to be the same model in another dimension, so
                        // a cross-category link is usually a mistake — WARN (don't block).
                        // The single admin decides; the attach still goes through.
                        if ($sibling && $owner->category_id !== $sibling->category_id) {
                            $ownerCat = $owner->category?->name ?? '—';
                            $siblingCat = $sibling->category?->name ?? '—';
                            Notification::make()
                                ->warning()
                                ->title('Atenție: categorii diferite')
                                ->body("Produsele sunt din categorii diferite ({$ownerCat} vs {$siblingCat}). Le-am legat ca frați — verifică dacă e intenționat.")
                                ->persistent()
                                ->send();
                        }

                        if (empty($owner->model_group_id)) {
                            $owner->model_group_id = (string) Str::uuid();
                            $owner->save();
                        }
                        Product::whereKey($data['product_id'])->update(['model_group_id' => $owner->model_group_id]);
                    }),
            ])
            ->recordActions([
                Action::make('detach')
                    ->label('Detașează')
                    ->icon('heroicon-o-link-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Product $record) => $record->update(['model_group_id' => null])),
            ]);
    }

    // No create form — siblings are linked, not created here.
    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
