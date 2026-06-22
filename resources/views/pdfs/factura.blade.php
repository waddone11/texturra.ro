{{-- Order invoice (factura fiscală) — rebranded to quote-level quality. Shared layout in
     pdfs/_invoice.blade.php (DejaVu Sans + diacritics, logo, branded footer). Data unchanged. --}}
@include('pdfs._invoice', ['docTitle' => 'FACTURĂ'])
