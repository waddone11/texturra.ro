<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // No hard delete: products may be referenced in orders. Use archive/restore
    // (status) from the list instead.
    protected function getHeaderActions(): array
    {
        return [];
    }
}
