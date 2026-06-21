<?php

namespace App\Filament\Resources\ColorGroups\Pages;

use App\Filament\Resources\ColorGroups\ColorGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditColorGroup extends EditRecord
{
    protected static string $resource = ColorGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
