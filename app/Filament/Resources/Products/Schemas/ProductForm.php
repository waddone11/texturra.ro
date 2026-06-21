<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

                        // TVA: nu se alege din form — toate produsele textile sunt 21%
                        // (cota standard RO din 2025-08-01), aplicat automat la create/save.

                        Toggle::make('status')
                            ->label('Activ')
                            ->default(true),

                        // product_code: nu apare în formular — se auto-generează
                        // (TEX-…) la create (CreateProduct::mutateFormDataBeforeCreate).

                        // EAN: opțional (produse non-eMAG nu se blochează). Dacă e
                        // completat → unic; gol → NULL (mai multe NULL permise).
                        TextInput::make('ean')
                            ->label('EAN')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null)
                            ->helperText('Opțional. Dacă îl completezi, trebuie să fie unic.'),

                        // description column is NOT NULL without a default → coerce
                        // empty to '' so a product can be created without a description.
                        Textarea::make('description')
                            ->label('Descriere')
                            ->rows(4)
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? '')
                            ->columnSpanFull(),
                    ]),

                // Vizibil doar pentru custom (perdele/draperii). height = plafonul
                // de înălțime folosit de configuratorul frontend (neatins).
                Section::make('Custom — configurator')
                    ->description('Doar pentru produse custom. „Înălțime max" = plafonul din configuratorul frontend.')
                    ->visible(fn (Get $get): bool => $get('type') === 'custom')
                    ->schema([
                        // Coloana e decimal(4,2) → max 99.99. Înălțimea e în METRI
                        // (ex. 2.80), nu cm — validăm 0.5–10 ca să nu depășească precizia.
                        TextInput::make('height')
                            ->label('Înălțime maximă (metri)')
                            ->numeric()
                            ->suffix('m')
                            ->step(0.01)
                            ->minValue(0.5)
                            ->maxValue(10)
                            ->helperText('În METRI (ex. 2.80, nu 280). Plafonul de înălțime din configuratorul de pe site.'),
                    ]),

                Section::make('Stoc & paletar')
                    ->schema([
                        // general_stock: derivat (read-only) când produsul are culori
                        // atașate (suma din product_color, recalculată de relation
                        // manager); editabil direct când NU are paletar.
                        TextInput::make('general_stock')
                            ->label('Stoc')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn (?Product $record): bool => (bool) $record?->colors()->exists())
                            ->dehydrated(fn (?Product $record): bool => ! (bool) $record?->colors()->exists())
                            ->helperText(fn (?Product $record): string => $record?->colors()->exists()
                                ? 'Acest produs are culori, deci stocul se pune per culoare (în secțiunea „Culori"). Aici se vede totalul, calculat automat.'
                                : 'Stocul total al produsului. Dacă adaugi culori în secțiunea „Culori", stocul se va pune pe fiecare culoare în parte.'),

                        Placeholder::make('colors_hint')
                            ->label('')
                            ->content('Pentru un produs cu mai multe culori, adaugă culorile în secțiunea „Culori" (mai jos, la editare) și pune stocul pe fiecare. Totalul se adună singur aici.'),
                    ]),

                Section::make('Imagini')
                    ->schema([
                        // Stored exactly like the old admin: files on the public disk
                        // under images/uploads/products, JSON array of "/storage/..."
                        // paths (frontend reads these directly). The format closures
                        // bridge FileUpload's disk-relative paths to that convention.
                        FileUpload::make('images')
                            ->label('Imagini produs')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('images/uploads/products')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->getUploadedFileNameForStorageUsing(fn ($file): string => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                . '-' . substr(md5(uniqid()), 0, 6) . '.' . $file->getClientOriginalExtension())
                            ->formatStateUsing(fn (?array $state): array => collect($state ?? [])
                                ->map(fn ($p) => Str::startsWith((string) $p, '/storage/') ? Str::after($p, '/storage/') : $p)
                                ->values()->all())
                            ->dehydrateStateUsing(fn (?array $state): array => collect($state ?? [])
                                ->map(fn ($p) => Str::startsWith((string) $p, '/storage/') ? $p : '/storage/' . ltrim((string) $p, '/'))
                                ->values()->all())
                            ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public')
                                ->delete(Str::startsWith($file, '/storage/') ? Str::after($file, '/storage/') : $file))
                            ->columnSpanFull(),
                    ]),

                Section::make('Material (read-only)')
                    ->collapsed()
                    ->schema([
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
