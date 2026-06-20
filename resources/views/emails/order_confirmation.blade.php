<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmare comandă</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: #30B2AF;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .flex-container .left, .flex-container .right {
            max-width: 48%;
        }
        .flex-container .right {
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #30B2AF;
            color: #fff;
            padding: 8px;
            text-align: left;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .lineHeight {
            line-height: 1.2;
        }
    </style>
</head>
<body>
<!-- Header -->
<div class="header">
    <h1 style="margin: 0;">Confirmare comandă</h1>
    <p style="margin: 5px 0;">Număr comandă: {{ $order->order_number }}</p>
    <p style="margin: 5px 0;">Data: {{ now()->format('d-m-Y') }}</p>
</div>

<!-- Flex Container -->
<div class="flex-container">
    <!-- Furnizor -->
    <div class="left">
        <h2>Furnizor:</h2>
        <p class="lineHeight"><strong>{{ config('app.store_owner.name') }}</strong></p>
        <p class="lineHeight">Adresa juridică: {{ config('app.store_owner.legal_address') }}</p>
        <p class="lineHeight">CIF: {{ config('app.store_owner.unique_code') }}</p>
        <p class="lineHeight">Nr. Reg. Com.: {{ config('app.store_owner.registration_number') }}</p>
        <p class="lineHeight">IBAN: {{ config('app.store_owner.iban') }}</p>
        <p class="lineHeight">Banca: {{ config('app.store_owner.bank') }}</p>
        <p class="lineHeight">Telefon: {{ config('app.store_owner.phone') }}</p>
    </div>

    <!-- Client -->
    <div class="right">
        <h2>Client:</h2>
        <p class="lineHeight"><strong>Nume:</strong> {{ $order->user->name }}</p>
        <p class="lineHeight"><strong>Tel:</strong> {{ $order->user->phone }}</p>
        <p class="lineHeight"><strong>Email:</strong> {{ $order->user->email }}</p>
        <p class="lineHeight"><strong>Adresa:</strong> {{ $order->billingAddress->street }}, {{ $order->billingAddress->postal_code }}</p>
        <p class="lineHeight">{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}</p>
    </div>
</div>

<!-- Produse Comandate -->
<h3>Produse comandate:</h3>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Produs</th>
        <th>Cantitate</th>
        <th>Preț unitar (fără TVA)</th>
        <th>TVA</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td style="text-align: right;">{{ $product->pivot->quantity }}</td>
            <td style="text-align: right;">{{ number_format($product->priceWithoutVat(), 2) }} RON</td>
            <td style="text-align: right;">{{ number_format($product->vatAmount(), 2) }} RON</td>
            <td style="text-align: right;">{{ number_format($product->pivot->quantity * $product->priceWithoutVat(), 2) }} RON</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Rezumat -->
<h3>Rezumat:</h3>
<p class="lineHeight"><strong>Subtotal fără TVA:</strong> {{ number_format($order->subtotal_excluding_vat, 2) }} RON</p>
<p class="lineHeight"><strong>TVA Total:</strong> {{ number_format($order->total_vat, 2) }} RON</p>
<p class="lineHeight"><strong>Transport:</strong> {{ number_format($order->shipping_cost, 2) }} RON</p>
@if ($order->voucherUsage && $order->voucherUsage->voucher)
    <p class="lineHeight"><strong>Voucher ({{ $order->voucherUsage->voucher->code }}):</strong> -{{ number_format($order->discount, 2) }} RON</p>
@endif
<p class="lineHeight"><strong>Total:</strong> {{ number_format($order->total_amount, 2) }} RON</p>

<!-- Footer -->
<div style="text-align: center; margin-top: 20px;">
    <p>Mulțumim pentru comanda dumneavoastră!</p>
</div>
</body>
</html>
