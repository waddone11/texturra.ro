<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Țintă')
                    ->description('O reducere se aplică pe un produs SAU pe o categorie (alege unul).')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->label('Produs')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('— niciunul —'),

                        Select::make('category_id')
                            ->label('Categorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('— niciuna —'),
                    ]),

                Section::make('Valoare & perioadă')
                    ->description('Completează procent SAU sumă fixă. Activă în intervalul de date.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('percentage')
                            ->label('Procent')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),

                        TextInput::make('fixed_amount')
                            ->label('Sumă fixă')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('RON'),

                        DatePicker::make('start_date')
                            ->label('De la')
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label('Până la')
                            ->native(false)
                            ->afterOrEqual('start_date'),
                    ]),
            ]);
    }
}
