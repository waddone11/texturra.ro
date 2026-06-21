<?php

namespace App\Filament\Resources\Colors\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ColorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('color_group_id')
                    ->label('Grup')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nume culoare')
                    ->required()
                    ->maxLength(255),

                ColorPicker::make('cod_css')
                    ->label('Cod CSS')
                    ->required(),
            ]);
    }
}
