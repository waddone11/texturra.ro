<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Product;
use App\Models\Quote;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Date ofertă')
                    ->columns(2)
                    ->schema([
                        TextInput::make('quote_number')
                            ->label('Număr ofertă')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto la salvare (OF-…)'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Ciornă',
                                'sent' => 'Trimisă',
                                'accepted' => 'Acceptată',
                                'rejected' => 'Respinsă',
                            ])
                            ->default('draft'),
                    ]),

                Section::make('Client')
                    ->columns(2)
                    ->schema([
                        TextInput::make('client_name')->label('Nume / firmă')->required()->maxLength(255),
                        TextInput::make('client_cif')->label('CIF / CUI')->maxLength(255),
                        TextInput::make('client_email')->label('Email')->email()->maxLength(255),
                        TextInput::make('client_phone')->label('Telefon')->maxLength(255),
                        TextInput::make('client_address')->label('Adresă')->maxLength(255)->columnSpanFull(),
                    ]),

                Section::make('Linii ofertă')
                    ->columnSpanFull()
                    ->schema([
                        // Native Filament v4 table repeater → one line = one clean horizontal row.
                        Repeater::make('lines')
                            ->relationship()
                            ->orderColumn('position')
                            ->defaultItems(1)
                            ->addActionLabel('Adaugă linie')
                            ->table([
                                TableColumn::make('Produs (opțional)')->width('19%'),
                                TableColumn::make('Denumire')->width('21%')->markAsRequired(),
                                TableColumn::make('UM')->width('14%'),
                                TableColumn::make('Cant.')->width('11%'),
                                TableColumn::make('Preț unitar (fără TVA)')->width('18%'),
                                TableColumn::make('Total (cu TVA)')->width('17%'),
                            ])
                            ->schema([
                                // Pick a product to prefill description + net price (long names truncated).
                                Select::make('product_id')
                                    ->label('Produs')
                                    ->options(fn (): array => Product::orderBy('name')->pluck('name', 'id')
                                        ->map(fn (string $n): string => Str::limit($n, 45))->all())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        $product = $state ? Product::find($state) : null;
                                        if ($product) {
                                            $set('description', $product->name);
                                            // product.price is VAT-inclusive → strip to net
                                            $set('unit_price', round(((float) $product->price) / (1 + Quote::VAT_RATE), 2));
                                        }
                                    }),

                                TextInput::make('description')->label('Denumire')->required(),
                                TextInput::make('unit')->label('UM')->default('buc')->required(),
                                TextInput::make('quantity')->label('Cant.')->numeric()->required()->default(1)->live(onBlur: true),
                                TextInput::make('unit_price')->label('Preț unitar')->numeric()->required()->default(0)->suffix('RON')->live(onBlur: true),

                                // Live per-line total (net + 21% VAT).
                                Placeholder::make('line_total')
                                    ->label('Total')
                                    ->content(function (Get $get): string {
                                        $c = \App\Models\QuoteLine::compute((float) $get('quantity'), (float) $get('unit_price'));

                                        return number_format($c['total'], 2) . ' RON';
                                    }),
                            ]),
                    ]),

                Section::make('Observații')
                    ->schema([
                        Textarea::make('notes')->label('Note / observații')->rows(3),
                    ]),
            ]);
    }
}
