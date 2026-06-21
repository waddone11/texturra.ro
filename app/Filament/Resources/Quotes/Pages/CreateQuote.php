<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['quote_number'] = Quote::generateNumber();

        return $data;
    }

    // Lines are saved by the repeater relationship; recompute the quote totals after.
    protected function afterCreate(): void
    {
        $this->record->load('lines');
        $this->record->recalculateTotals();
        $this->record->saveQuietly();
    }
}
