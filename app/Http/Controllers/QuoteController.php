<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    /**
     * Render a quote as a branded PDF (dompdf + Blade). Gated by the admin route group.
     */
    public function pdf(Quote $quote)
    {
        $quote->load(['lines' => fn ($q) => $q->orderBy('position'), 'lines.product']);

        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'company' => config('app.store_owner'),
        ])->setPaper('a4');

        return $pdf->download($quote->quote_number . '.pdf');
    }
}
