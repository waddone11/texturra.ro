<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nume')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Categorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Enum DB = standard|custom (2 valori). „sized" (covoare)
                        // cere extindere enum — raportat, NU migrat aici.
                        Select::make('type')
                            ->label('Mod produs')
                            ->required()
                            ->live()
                            ->default('standard')
                            ->options([
                                'custom' => 'Custom — perdele/draperii (configurator)',
                                'standard' => 'Standard — așternuturi / simplu',
                            ])
                            ->helperText('„sized" (covoare) nu există în enum-ul DB încă — vezi raport.'),

                        TextInput::make('price')
                            ->label('Preț')
                            ->required()
                            ->numeric()
                            ->prefix('RON'),

                        TextInput::make('sale_price')
                            ->label('Preț redus')
                            ->numeric()
                            ->prefix('RON'),

                        Select::make('vat_id')
                            ->label('TVA')
                            ->relationship('vat', 'rate')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->rate . '%'),

                        Toggle::make('status')
                            ->label('Activ')
                            ->default(true),

                        TextInput::make('product_code')
                            ->label('Cod produs')
                            ->maxLength(255),

                        TextInput::make('ean')
                            ->label('EAN')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descriere')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                // Vizibil doar pentru custom (perdele/draperii). height = plafonul
                // de înălțime folosit de configuratorul frontend (neatins).
                Section::make('Custom — configurator')
                    ->description('Doar pentru produse custom. „Înălțime max" = plafonul din configuratorul frontend.')
                    ->visible(fn (Get $get): bool => $get('type') === 'custom')
                    ->schema([
                        TextInput::make('height')
                            ->label('Înălțime max (m)')
                            ->numeric()
                            ->step(0.01)
                            ->helperText('Decimal, ex. 3.00. Plafon pentru configuratorul de pe site.'),
                    ]),

                Section::make('Stoc & paletar')
                    ->schema([
                        // general_stock: derivat (read-only) când produsul are culori
                        // atașate (suma din product_color, recalculată de relation
                        // manager); editabil direct când NU are paletar.
                        TextInput::make('general_stock')
                            ->label('Stoc general')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn (?Product $record): bool => (bool) $record?->colors()->exists())
                            ->dehydrated(fn (?Product $record): bool => ! (bool) $record?->colors()->exists())
                            ->helperText(fn (?Product $record): string => $record?->colors()->exists()
                                ? 'Derivat = suma stocurilor pe culori (gestionat în „Culori", read-only).'
                                : 'Editabil direct (fără paletar). Când atașezi culori, devine derivat.'),

                        Placeholder::make('colors_hint')
                            ->label('')
                            ->content('Culorile + stocul per culoare se gestionează în secțiunea „Culori" de mai jos (la editare).'),
                    ]),

                // Read-only: nu rescriem stocarea imaginilor (JSON /storage/...) și
                // nici materialul (prin variații legacy) în această fază — vezi raport.
                Section::make('Imagini & material (read-only)')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('images_preview')
                            ->label('Imagini curente')
                            ->content(function (?Product $record): HtmlString {
                                $imgs = $record?->images ?: [];
                                if (! $imgs) {
                                    return new HtmlString('<span>— fără imagini —</span>');
                                }

                                return new HtmlString(collect($imgs)
                                    ->map(fn ($p) => '<img src="' . e($p) . '" style="height:56px;display:inline-block;margin:2px;border-radius:6px;border:1px solid #e5e7eb">')
                                    ->implode(''));
                            }),

                        Placeholder::make('material_current')
                            ->label('Material (prin variații legacy)')
                            ->content(function (?Product $record): string {
                                if (! $record) {
                                    return '—';
                                }

                                $val = DB::table('product_variation_attribute_values as piv')
                                    ->join('attribute_values as av', 'piv.attribute_value_id', '=', 'av.id')
                                    ->join('attributes as a', 'av.attribute_id', '=', 'a.id')
                                    ->join('product_variations as pv', 'piv.product_variation_id', '=', 'pv.id')
                                    ->where('a.name', 'Material')
                                    ->where('pv.product_id', $record->id)
                                    ->value('av.value');

                                return $val ?: '— nesetat —';
                            }),
                    ]),
            ]);
    }
}
