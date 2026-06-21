<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    // No delete: orders are financial records. Cancellation = "canceled" status.
    protected function getHeaderActions(): array
    {
        return [];
    }
}
