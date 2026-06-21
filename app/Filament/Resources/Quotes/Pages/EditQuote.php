<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // Recompute quote totals after the repeater saves its lines.
    protected function afterSave(): void
    {
        $this->record->load('lines');
        $this->record->recalculateTotals();
        $this->record->saveQuietly();
    }
}
