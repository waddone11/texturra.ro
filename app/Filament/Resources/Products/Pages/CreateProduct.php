<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Vat;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto product_code, like the old admin (ProductCreate::addProduct).
        if (empty($data['product_code'])) {
            $data['product_code'] = 'TEX-' . strtoupper(uniqid());
        }

        // Standard 21% VAT applied automatically (selector hidden in the form).
        $data['vat_id'] = Vat::firstOrCreate(['rate' => 21.00], ['name' => '21'])->id;

        return $data;
    }
}
