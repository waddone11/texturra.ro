<?php

namespace App\Filament\Resources\ColorGroups\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorsRelationManager extends RelationManager
{
    protected static string $relationship = 'colors';

    protected static ?string $title = 'Culori';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nume culoare')
                    ->required()
                    ->maxLength(255),

                ColorPicker::make('cod_css')
                    ->label('Cod CSS')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ColorColumn::make('cod_css')
                    ->label('Swatch'),

                TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cod_css')
                    ->label('Cod CSS')
                    ->badge(),
            ])
            ->defaultSort('name')
            // hasMany with a required FK — no Associate/Dissociate (would null
            // color_group_id). Colors are created/edited/deleted within a group.
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
