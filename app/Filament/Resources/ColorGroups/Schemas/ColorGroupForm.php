<?php

namespace App\Filament\Resources\ColorGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ColorGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nume grup')
                    ->required()
                    ->maxLength(255),

                // image_path is a reference to an existing file (e.g.
                // storage/images/colors/rosu_burgundia.avif). Kept as a plain
                // text field on purpose — no upload, no storage writes (Faza 3
                // scope: palette CRUD only).
                TextInput::make('image_path')
                    ->label('Cale imagine')
                    ->helperText('Cale către fișierul imaginii grupului (ex. storage/images/colors/...). Doar referință text — fără upload în această fază.')
                    ->maxLength(255),
            ]);
    }
}
