<!DOCTYPE html>
<html lang="ro">
@php
    // dompdf: enable_remote=false → local file paths (not base64); inline <svg> doesn't render →
    // icons are local SVG files via public_path; Google Fonts can't load → DejaVu Sans (bundled,
    // has RO diacritics). Mirrors the quote PDF. NO stripDiacritics — diacriticele apar corect.
    $logoPath = public_path('storage/images/logo_black.png');
    $logo = is_file($logoPath) ? $logoPath : null;
    $icon = fn (string $name, int $px): string => is_file(public_path("images/pdf-icons/{$name}.svg"))
        ? '<img src="' . public_path("images/pdf-icons/{$name}.svg") . '" style="height:' . $px . 'px; width:' . $px . 'px; vertical-align:middle;">'
        : '';
    $lei = fn ($n): string => number_format((float) $n, 2, ',', '.') . ' lei';

    $issued = $order->created_at ?? now();

    $supplier = [
        'name' => 'TEXTURRA HOME SRL',
        'cui' => '37242038',
        'reg' => 'J29/548/2017',
        'address' => 'Str. Diligenței nr. 10, Mun. Ploiești, Prahova, cod 100575',
        'vat' => 'Plătitor de TVA',
        'phone' => '+40 748 538 323',
        'email' => 'contact@texturra.ro',
        'website' => 'www.texturra.ro',
    ];
    $iban = config('app.store_owner.iban');
    $bank = config('app.store_owner.bank');
    $tagline = 'Intră în universul TEXTURRA — textile pentru casă create cu stil și pasiune.';

    $payLabel = match ($order->payment_method) {
        'courier' => 'Ramburs (plata la livrare)',
        'bank_transfer' => 'Transfer bancar',
        'online' => 'Card online',
        default => $order->payment_method,
    };
@endphp
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 0; }
        body { margin: 0; color: #1a1a1a; font-size: 10px; }
        .content { padding: 15mm 16mm 0 16mm; }

        .doc-title { font-size: 19px; font-weight: bold; letter-spacing: 2px; color: #1a1a1a; }
        .accent { background: #A85C32; height: 2px; }
        .meta { font-size: 10px; color: #4a4641; line-height: 1.8; }
        .meta .k { color: #8b857d; }
        .meta .v { font-weight: bold; color: #1a1a1a; }
        .hr { height: 1px; background: #e2ded6; }

        .ptitle { font-size: 9px; font-weight: bold; letter-spacing: 2px; }
        .pname { font-size: 12.5px; font-weight: bold; color: #1a1a1a; }
        .pbody { font-size: 9.5px; color: #4a4641; line-height: 1.85; }
        .pbody .k { color: #8b857d; }

        table.lines { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.lines th { background: #1d1d1d; color: #f2efe9; font-size: 8px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; padding: 8px 7px; }
        table.lines td { padding: 9px 7px; font-size: 9.5px; border-bottom: 1px solid #ece8e1; vertical-align: top; word-wrap: break-word; }
        table.lines .num { color: #8b857d; }
        table.lines .desc { color: #1a1a1a; font-weight: bold; }
        table.lines .sub { color: #6b6660; font-size: 8.5px; font-weight: normal; }
        table.lines .mut { color: #4a4641; }
        table.lines .r { text-align: right; }
        table.lines .c { text-align: center; }
        table.lines .tot { color: #1a1a1a; font-weight: bold; text-align: right; }

        .sum-k { font-size: 10px; color: #6b6660; }
        .sum-v { font-size: 10.5px; color: #1a1a1a; font-weight: bold; text-align: right; }
        .term-k { font-size: 8px; font-weight: bold; letter-spacing: 1px; color: #A85C32; }
        .term-v { font-size: 10px; color: #1a1a1a; font-weight: bold; }
        .grand-l { font-size: 10.5px; font-weight: bold; letter-spacing: 0.5px; color: #1a1a1a; text-transform: uppercase; }
        .grand-v { font-size: 24px; font-weight: bold; color: #1a1a1a; text-align: right; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #1a1a1a; color: #f2efe9; padding: 12px 16mm 11px 16mm; }
        .stat .snum { font-size: 14px; font-weight: bold; color: #ffffff; }
        .stat .slbl { font-size: 7px; letter-spacing: 0.5px; color: #a8a29a; text-transform: uppercase; }
        .fdiv { height: 1px; background: #333030; margin: 11px 0 9px 0; }
        .fbottom { font-size: 8.5px; color: #bdb8b0; }
        .fbottom .nm { font-weight: bold; color: #f2efe9; }
        .fbottom .sep { color: #555049; }
    </style>
</head>
<body>

    {{-- ===== FIXED DARK FOOTER ===== --}}
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td style="vertical-align:middle;">
                    <table>
                        <tr>
                            <td class="stat" style="padding-right:20px;">{!! $icon('stat-calendar', 15) !!} <span class="snum">11+</span><div class="slbl">Ani experiență</div></td>
                            <td class="stat" style="padding-right:20px;">{!! $icon('stat-box', 15) !!} <span class="snum">500.000+</span><div class="slbl">Produse vândute</div></td>
                            <td class="stat" style="padding-right:20px;">{!! $icon('stat-headset', 15) !!} <span class="snum">10+</span><div class="slbl">Consultanți specializați</div></td>
                            <td class="stat" style="padding-right:20px;">{!! $icon('stat-handshake', 15) !!} <span class="snum">120+</span><div class="slbl">Parteneri comerciali</div></td>
                            <td class="stat">{!! $icon('stat-store', 15) !!} <span class="snum">2</span><div class="slbl">Magazine fizice</div></td>
                        </tr>
                    </table>
                </td>
                <td style="text-align:right; vertical-align:middle; width:130px;">
                    {!! $icon('social-facebook', 15) !!} &nbsp;{!! $icon('social-instagram', 15) !!} &nbsp;{!! $icon('social-web', 15) !!}
                </td>
            </tr>
        </table>
        <div class="fdiv"></div>
        <div style="font-size:8px; color:#8b857d; font-style:italic; margin-bottom:4px;">{{ $tagline }}</div>
        <div class="fbottom">
            <span class="nm">{{ $supplier['name'] }}</span>
            <span class="sep">&nbsp;|&nbsp;</span> {{ $supplier['phone'] }}
            <span class="sep">&nbsp;|&nbsp;</span> {{ $supplier['email'] }}
            <span class="sep">&nbsp;|&nbsp;</span> {{ $supplier['website'] }}
        </div>
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="content">

        {{-- header --}}
        <table style="width:100%;">
            <tr>
                <td style="vertical-align:top;">
                    @if($logo)<img src="{{ $logo }}" alt="TEXTURRA" style="height:70px;">@endif
                </td>
                <td style="text-align:right; vertical-align:top; width:240px;">
                    <div class="doc-title">{{ $docTitle }}</div>
                    <table style="margin:9px 0 9px auto;"><tr><td><div class="accent" style="width:54px;"></div></td></tr></table>
                    <div class="meta">
                        <div><span class="k">Nr. comandă:</span> <span class="v">{{ $order->order_number }}</span></div>
                        <div><span class="k">Data emiterii:</span> <span class="v">{{ $issued->format('d.m.Y') }}</span></div>
                        <div><span class="k">Modalitate plată:</span> <span class="v">{{ $payLabel }}</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="hr" style="margin:14px 0 16px 0;"></div>

        {{-- supplier / client --}}
        <table style="width:100%;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:17px;">
                    <div class="ptitle" style="color:#A85C32;">FURNIZOR</div>
                    <div class="pname" style="margin:6px 0 5px 0;">{{ $supplier['name'] }}</div>
                    <div class="pbody">
                        <div><span class="k">CUI:</span> {{ $supplier['cui'] }} &nbsp;·&nbsp; {{ $supplier['vat'] }}</div>
                        <div><span class="k">Nr. Reg. Com.:</span> {{ $supplier['reg'] }}</div>
                        <div><span class="k">Sediu:</span> {{ $supplier['address'] }}</div>
                        @if($iban)<div><span class="k">IBAN:</span> {{ $iban }} @if($bank)({{ $bank }})@endif</div>@endif
                        <div><span class="k">Telefon:</span> {{ $supplier['phone'] }}</div>
                        <div><span class="k">E-mail:</span> {{ $supplier['email'] }}</div>
                    </div>
                </td>
                <td style="width:50%; vertical-align:top; padding-left:17px;">
                    <div class="ptitle" style="color:#1a1a1a;">CLIENT</div>
                    <div class="pname" style="margin:6px 0 5px 0;">{{ $order->user->name ?? '—' }}</div>
                    <div class="pbody">
                        @if($order->user->phone ?? null)<div><span class="k">Telefon:</span> {{ $order->user->phone }}</div>@endif
                        @if($order->user->email ?? null)<div><span class="k">E-mail:</span> {{ $order->user->email }}</div>@endif
                        @if($order->billingAddress)
                            <div><span class="k">Adresă:</span> {{ $order->billingAddress->street }}@if($order->billingAddress->postal_code), {{ $order->billingAddress->postal_code }}@endif</div>
                            <div>{{ $order->billingAddress->city }}@if($order->billingAddress->state), {{ $order->billingAddress->state }}@endif</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- lines --}}
        <table class="lines" style="margin-top:20px;">
            <thead>
                <tr>
                    <th style="width:5%; text-align:left;">Nr.</th>
                    <th style="width:37%; text-align:left;">Produs / Descriere</th>
                    <th style="width:12%; text-align:right;">Cant.</th>
                    <th style="width:16%; text-align:right;">Preț unitar (fără TVA)</th>
                    <th style="width:8%; text-align:right;">TVA</th>
                    <th style="width:14%; text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $index => $product)
                    @php
                        $meta = $product->getPivotMeta();
                        $isCustom = $meta['is_custom'] ?? false;
                        $qty = $product->pivot->quantity;
                        $unitPrice = $meta['unit_price_without_vat'] ?? $product->priceWithoutVat();
                        $vatRate = $meta['vat_rate'] ?? ($product->vat->rate ?? 21);
                        $lineTotalExVat = $meta['subtotal_ex_vat_for_product'] ?? $unitPrice * $qty;
                        $lineVat = $meta['total_vat_for_product'] ?? 0;
                        $lineTotal = $meta['item_total'] ?? $lineTotalExVat + $lineVat;
                    @endphp
                    <tr>
                        <td class="num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <span class="desc">{{ $product->name }}</span>
                            @if($isCustom)
                                <div class="sub">
                                    Confecție la comandă: {{ $meta['length'] ?? '?' }} m
                                    @if(($meta['height'] ?? null)) × {{ $meta['height'] }} m @endif
                                    · {{ $meta['pieces'] ?? '?' }} buc
                                    @if(($meta['manufactoring_type_name'] ?? null)) · Manoperă: {{ $meta['manufactoring_type_name'] }}@if(($meta['manufactoring_price'] ?? null)) ({{ $meta['manufactoring_price'] }} lei/m)@endif @endif
                                </div>
                            @endif
                        </td>
                        <td class="mut r">@if($isCustom){{ $meta['pieces'] ?? $qty }} buc @else{{ $qty }}@endif</td>
                        <td class="mut r">{{ $lei($unitPrice) }}</td>
                        <td class="mut r">{{ $vatRate }}%</td>
                        <td class="tot">{{ $lei($lineTotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- summary --}}
        <table style="width:100%; margin-top:22px;">
            <tr>
                <td style="vertical-align:top; padding-right:30px;">
                    <div style="margin-bottom:8px;"><div class="term-k">TERMEN DE LIVRARE</div><div class="term-v">1–3 zile lucrătoare</div></div>
                    <div style="margin-bottom:8px;"><div class="term-k">MODALITATE DE PLATĂ</div><div class="term-v">{{ $payLabel }}</div></div>
                    <div><div class="term-k">GARANȚIE</div><div class="term-v">Conform specificațiilor producătorului</div></div>
                </td>
                <td style="width:250px; vertical-align:top;">
                    <table style="width:100%;">
                        <tr><td class="sum-k" style="padding:5px 0;">Subtotal (fără TVA)</td><td class="sum-v" style="padding:5px 0;">{{ $lei($order->subtotalExcludingVat()) }}</td></tr>
                        <tr><td class="sum-k" style="padding:5px 0;">TVA</td><td class="sum-v" style="padding:5px 0;">{{ $lei($order->totalVat()) }}</td></tr>
                        <tr><td class="sum-k" style="padding:5px 0;">Transport</td><td class="sum-v" style="padding:5px 0;">{{ $lei($order->shipping_cost) }}</td></tr>
                        @if($voucher)
                            <tr><td class="sum-k" style="padding:5px 0; color:#A85C32;">Voucher {{ $voucher->code }}</td><td class="sum-v" style="padding:5px 0; color:#A85C32;">−{{ $lei($order->discount) }}</td></tr>
                        @endif
                    </table>
                    <table style="margin:14px 0 7px auto;"><tr><td><div class="accent" style="width:46px;"></div></td></tr></table>
                    <div class="grand-l">Total de plată</div>
                    <div class="grand-v" style="margin-top:3px;">{{ $lei($order->total_amount) }}</div>
                    <div style="text-align:right; font-size:8px; color:#8b857d; margin-top:4px;">TVA inclus</div>
                </td>
            </tr>
        </table>

        <div style="height:95px;"></div>
    </div>

</body>
</html>
