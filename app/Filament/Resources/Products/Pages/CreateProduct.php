<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // Auto-generate product_code exactly like the old admin (ProductCreate::addProduct).
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['product_code'])) {
            $data['product_code'] = 'TEX-' . strtoupper(uniqid());
        }

        return $data;
    }
}
