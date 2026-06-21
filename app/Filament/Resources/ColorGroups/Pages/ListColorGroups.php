<?php

namespace App\Filament\Resources\ColorGroups\Pages;

use App\Filament\Resources\ColorGroups\ColorGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListColorGroups extends ListRecords
{
    protected static string $resource = ColorGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
