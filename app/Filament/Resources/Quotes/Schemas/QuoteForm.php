<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Product;
use App\Models\Quote;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

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
                    ->schema([
                        Repeater::make('lines')
                            ->label('')
                            ->relationship()
                            ->orderColumn('position')
                            ->defaultItems(1)
                            ->addActionLabel('Adaugă linie')
                            ->columns(12)
                            ->schema([
                                // Optional: pick a product to prefill description + net price.
                                Select::make('product_id')
                                    ->label('Produs (opțional)')
                                    ->options(fn (): array => Product::orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        $product = $state ? Product::find($state) : null;
                                        if ($product) {
                                            $set('description', $product->name);
                                            // product.price is VAT-inclusive → strip to net
                                            $set('unit_price', round(((float) $product->price) / (1 + Quote::VAT_RATE), 2));
                                        }
                                    })
                                    ->columnSpan(3),

                                TextInput::make('description')->label('Denumire')->required()->columnSpan(4),
                                TextInput::make('unit')->label('UM')->default('buc')->required()->columnSpan(1),
                                TextInput::make('quantity')->label('Cant.')->numeric()->required()->default(1)->columnSpan(2),
                                TextInput::make('unit_price')->label('Preț unitar (fără TVA)')->numeric()->required()->default(0)->suffix('RON')->columnSpan(2),
                            ]),
                    ]),

                Section::make('Observații')
                    ->schema([
                        Textarea::make('notes')->label('Note / observații')->rows(3),
                    ]),
            ]);
    }
}
