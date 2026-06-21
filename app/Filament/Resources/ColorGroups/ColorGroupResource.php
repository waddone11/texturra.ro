<?php

namespace App\Filament\Resources\ColorGroups;

use App\Filament\Resources\ColorGroups\Pages\CreateColorGroup;
use App\Filament\Resources\ColorGroups\Pages\EditColorGroup;
use App\Filament\Resources\ColorGroups\Pages\ListColorGroups;
use App\Filament\Resources\ColorGroups\RelationManagers\ColorsRelationManager;
use App\Filament\Resources\ColorGroups\Schemas\ColorGroupForm;
use App\Filament\Resources\ColorGroups\Tables\ColorGroupsTable;
use App\Models\ColorGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ColorGroupResource extends Resource
{
    protected static ?string $model = ColorGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|\UnitEnum|null $navigationGroup = 'Paletar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'grup de culori';

    protected static ?string $pluralModelLabel = 'grupuri de culori';

    public static function form(Schema $schema): Schema
    {
        return ColorGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ColorGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ColorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListColorGroups::route('/'),
            'create' => CreateColorGroup::route('/create'),
            'edit' => EditColorGroup::route('/{record}/edit'),
        ];
    }
}
