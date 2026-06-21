<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nume')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descriere')
                    ->rows(3)
                    ->columnSpanFull(),

                // Self-referential parent. On edit we exclude the record itself
                // and all of its descendants so the hierarchy can't form a cycle.
                Select::make('parent_id')
                    ->label('Categorie părinte')
                    ->placeholder('— fără părinte (rădăcină) —')
                    ->searchable()
                    ->options(function (?Category $record): array {
                        $query = Category::query()->orderBy('name');

                        if ($record) {
                            $query->whereNotIn('id', static::excludedParentIds($record));
                        }

                        return $query->pluck('name', 'id')->all();
                    }),

                Toggle::make('status')
                    ->label('Activă')
                    ->default(true),

                Toggle::make('is_allowed')
                    ->label('Permisă')
                    ->default(true),

                Toggle::make('is_ean_mandatory')
                    ->label('EAN obligatoriu')
                    ->default(false),

                Toggle::make('is_warranty_mandatory')
                    ->label('Garanție obligatorie')
                    ->default(false),
            ]);
    }

    /**
     * The record itself plus every descendant id — the set that may NOT be
     * chosen as parent (mirrors the iterative walk in Category model).
     *
     * @return array<int, int>
     */
    protected static function excludedParentIds(Category $record): array
    {
        $ids = [$record->id];
        $stack = [$record->id];

        while (! empty($stack)) {
            $parentId = array_pop($stack);
            $children = Category::where('parent_id', $parentId)->pluck('id')->all();

            foreach ($children as $childId) {
                $ids[] = $childId;
                $stack[] = $childId;
            }
        }

        return $ids;
    }
}
