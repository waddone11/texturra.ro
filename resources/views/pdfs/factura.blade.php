@php
    use App\Helpers\Helpers;
@endphp
    <!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #333;
        }
        .header { width: 100%; background-color: #000000; color: #000; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .header-table, .details-table, .table { width: 100%; border-collapse: collapse; }
        .header-table td, .details-table td, .table td, .table th { padding: 8px; vertical-align: top; }
        .logo { max-width: 90px; margin-top: 35px; }
        .details-table .left { background-color: #e9ecef; padding: 15px; border-radius: 5px; line-height: 1.2; }
        .details-table .right { text-align: right; line-height: 1.2; }
        .table th { background-color: #000000; color: #fff; text-align: left; }
        .table td { border: 1px solid #ddd; }
        .table tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .table tfoot th { background-color: #f2f2f2; font-weight: bold; color: #000000; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="header">
    <table class="header-table">
        <tr>
            <td class="logo-cell" style="width: 30%;">
                <img class="logo" src="{{ public_path('storage/images/logo.svg') }}" alt="Logo">
                <img class="logo" src="{{ public_path('storage/images/logo2.svg') }}" alt="Logo 2">
            </td>
            <td class="details-cell" style="width: 70%; text-align: right;">
                <h1>Factura</h1>
                <p>Serie: {{ $order->order_number }}</p>
                <p>Data: {{ now()->format('d-m-Y') }}</p>
            </td>
        </tr>
    </table>
</div>

<table class="details-table">
    <tr>
        <td class="left">
            <h2>Furnizor:</h2>
            <p><strong>{{ Helpers::stripDiacritics(config('app.store_owner.name')) }}</strong></p>
            <p>Adresa juridica: {{ Helpers::stripDiacritics(config('app.store_owner.legal_address')) }}</p>
            <p>CIF: {{ Helpers::stripDiacritics(config('app.store_owner.unique_code')) }}</p>
            <p>Nr. Reg. Com.: {{ Helpers::stripDiacritics(config('app.store_owner.registration_number')) }}</p>
            <p>IBAN: {{ Helpers::stripDiacritics(config('app.store_owner.iban')) }}</p>
            <p>Banca: {{ Helpers::stripDiacritics(config('app.store_owner.bank')) }}</p>
            <p>Telefon: {{ Helpers::stripDiacritics(config('app.store_owner.phone')) }}</p>
        </td>
        <td class="right">
            <h2>Client:</h2>
            <p><strong>Nume:</strong> {{ $order->user->name }}</p>
            <p><strong>Tel:</strong> {{ $order->user->phone }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Adresa:</strong> {{ $order->billingAddress->street }}, {{ $order->billingAddress->postal_code }}</p>
            <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}</p>
        </td>
    </tr>
</table>

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Produs/Serviciu</th>
        <th>Cantitate</th>
        <th>Pret unitar (fara TVA)</th>
        <th>TVA (%)</th>
        <th>Valoare (fara TVA)</th>
        <th>Valoare TVA</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->products as $index => $product)
        @php
            $meta = $product->getPivotMeta();
            $isCustom = $meta['is_custom'] ?? false;

            $qty = $product->pivot->quantity;
            $unitPrice = $meta['unit_price_without_vat'] ?? $product->priceWithoutVat();
            $vatRate = $meta['vat_rate'] ?? ($product->vat->rate ?? 19);
            $unitVat = $meta['unit_vat'] ?? ($product->vatAmount() ?? 0);
            $lineTotalExVat = $meta['subtotal_ex_vat_for_product'] ?? $unitPrice * $qty;
            $lineVat = $meta['total_vat_for_product'] ?? $unitVat * $qty;
            $lineTotal = $meta['item_total'] ?? $lineTotalExVat + $lineVat;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                {{ $product->name }}
                @if($isCustom)
                    <br>
                    <small>
                        Draperie personalizată:
                        {{ $meta['length'] ?? '?' }}m × {{ $meta['pieces'] ?? '?' }} buc
                        — Manoperă: {{ $meta['manufactoring_type_name'] ?? '-' }} ({{ $meta['manufactoring_price'] ?? '?' }} lei/m)
                    </small>
                @endif
            </td>
            <td class="text-right">
                @if($isCustom)
                    {{ $meta['pieces'] ?? $qty }} buc ({{ $meta['length'] ?? '?' }}m)
                @else
                    {{ $qty }}
                @endif
            </td>
            <td class="text-right">{{ number_format($unitPrice, 2) }} RON</td>
            <td class="text-right">{{ $vatRate }}%</td>
            <td class="text-right">{{ number_format($lineTotalExVat, 2) }} RON</td>
            <td class="text-right">{{ number_format($lineVat, 2) }} RON</td>
            <td class="text-right">{{ number_format($lineTotal, 2) }} RON</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="5" class="text-right">Subtotal produse:</th>
        <td class="text-right">{{ number_format($order->subtotalExcludingVat(), 2) }} RON</td>
        <td class="text-right">{{ number_format($order->totalVat(), 2) }} RON</td>
        <td class="text-right">{{ number_format($order->subtotalIncludingVat(), 2) }} RON</td>
    </tr>
    <tr>
        <th colspan="7" class="text-right">Transport:</th>
        <td class="text-right">{{ number_format($order->shipping_cost, 2) }} RON</td>
    </tr>
    @if ($voucher)
        <tr>
            <td colspan="7" class="text-left"><strong>Voucher Discount ({{ $voucher->code }} {{ $voucher->display() }}):</strong></td>
            <td class="text-right">-{{ number_format($order->discount, 2) }} RON</td>
        </tr>
    @endif
    <tr>
        <th colspan="7" class="text-right">Total (de plata):</th>
        <td class="text-right">{{ number_format($order->total_amount, 2) }} RON</td>
    </tr>
    </tfoot>
</table>

<div class="footer">
    <p>Multumim pentru comanda dumneavoastra!</p>
</div>
</body>
</html>
