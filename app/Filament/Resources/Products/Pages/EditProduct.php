<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Vat;
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

    // Migrate any product touched in the admin to the current 21% VAT rate.
    // (Untouched products keep their existing rate; historical order totals are
    // stored columns and are never recalculated.)
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['vat_id'] = Vat::firstOrCreate(['rate' => 21.00], ['name' => '21'])->id;

        return $data;
    }
}
