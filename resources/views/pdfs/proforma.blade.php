{{-- Order proforma — rebranded to quote-level quality. Shared layout in
     pdfs/_invoice.blade.php (DejaVu Sans + diacritics, logo, branded footer). Data unchanged.
     Ramburs/transfer orders generate this proforma (vs factura for paid online). --}}
@include('pdfs._invoice', ['docTitle' => 'FACTURĂ PROFORMĂ'])
