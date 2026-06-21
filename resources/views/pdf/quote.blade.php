<!DOCTYPE html>
<html lang="ro">
@php
    // Real brand logo (PNG). dompdf has enable_remote=false, so base64 data URIs are blocked —
    // a local file path under base_path (chroot) is loaded fine, like the legacy invoices.
    $logoPath = public_path('storage/images/logo_black.png');
    $logo = is_file($logoPath) ? $logoPath : null;

    // Contact + social shown in the footer (editable placeholders until real links provided).
    $brand = [
        'website' => 'www.texturra.ro',
        'email' => $company['support'] ?: 'contact@texturra.ro',
        'facebook' => 'facebook.com/texturra',
        'instagram' => 'instagram.com/texturra',
    ];

    // Icons as local SVG files (dompdf renders SVG via php-svg-lib; inline <svg> is unreliable).
    $iconDir = public_path('images/pdf-icons');
    $icon = fn (string $name): string => is_file("{$iconDir}/{$name}.svg")
        ? '<img src="' . $iconDir . '/' . $name . '.svg" style="height:9px; width:9px; vertical-align:middle;">'
        : '';
@endphp
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 28px 34px; }
        body { color: #2b2b2b; font-size: 10.5px; }

        /* ---- header ---- */
        .top { width: 100%; border-collapse: collapse; }
        .wordmark { font-size: 22px; font-weight: bold; color: #1f2a44; }
        .wordmark .accent { color: #b8860b; }
        .wordmark small { display: block; font-size: 8px; letter-spacing: 3px; color: #9a8550; font-weight: normal; margin-top: 2px; }
        .co { font-size: 9px; color: #555; line-height: 1.6; text-align: right; }
        .co .co-name { font-size: 11px; font-weight: bold; color: #1f2a44; }
        .rule { height: 2px; background-color: #1f2a44; margin-top: 12px; }
        .rule-gold { height: 2px; background-color: #b8860b; width: 150px; }

        /* ---- title + meta ---- */
        .band { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .doc-title { font-size: 18px; font-weight: bold; color: #1f2a44; letter-spacing: 0.5px; }
        .doc-title .sub { display: block; font-size: 8.5px; color: #9a8550; letter-spacing: 2px; font-weight: normal; margin-top: 2px; }
        .meta { border: 1px solid #d8d2c4; border-collapse: collapse; }
        .meta td { padding: 5px 11px; font-size: 9.5px; border-bottom: 1px solid #eee6d6; }
        .meta td.k { color: #777; background-color: #faf7f0; }
        .meta td.v { font-weight: bold; color: #1f2a44; }

        /* ---- client ---- */
        .client { margin-top: 16px; border: 1px solid #d8d2c4; border-left: 3px solid #b8860b; border-radius: 4px; padding: 10px 14px; line-height: 1.55; }
        .client .label { color: #b8860b; font-size: 8px; text-transform: uppercase; letter-spacing: 1.5px; }
        .client .name { font-size: 12.5px; font-weight: bold; color: #1f2a44; }
        .client .row { font-size: 9.5px; color: #555; }

        /* ---- lines table (fixed layout → no overlap) ---- */
        table.lines { width: 100%; border-collapse: collapse; margin-top: 18px; table-layout: fixed; }
        table.lines th {
            background-color: #1f2a44; color: #fff; font-size: 8.5px; text-align: left;
            padding: 8px 8px; text-transform: uppercase; letter-spacing: 0.4px; border-right: 1px solid #33405f;
        }
        table.lines td {
            padding: 8px 8px; border-bottom: 1px solid #e7e2d6; border-right: 1px solid #efeadf;
            font-size: 10px; vertical-align: top; word-wrap: break-word; overflow: hidden;
        }
        table.lines tr:nth-child(even) td { background-color: #faf8f3; }
        table.lines .r { text-align: right; }
        table.lines .c { text-align: center; }
        table.lines td.tot { font-weight: bold; color: #1f2a44; }

        /* ---- totals ---- */
        .totals { width: 44%; margin-left: 56%; margin-top: 14px; border-collapse: collapse; }
        .totals td { padding: 7px 12px; font-size: 10.5px; border-bottom: 1px solid #eee6d6; }
        .totals .k { color: #555; }
        .totals .v { text-align: right; font-weight: bold; color: #1f2a44; }
        .totals .grand td { background-color: #b8860b; color: #fff; font-size: 13px; font-weight: bold; border: none; }

        .notes { margin-top: 18px; background-color: #faf7f0; border: 1px solid #e7e2d6; border-radius: 4px; padding: 9px 13px; font-size: 9.5px; color: #555; }

        /* ---- footer ---- */
        .footer { margin-top: 26px; }
        .footer .bar { height: 2px; background-color: #b8860b; }
        .ftab { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .fcontact { font-size: 9px; color: #555; line-height: 1.9; text-align: right; }
        .fcontact .sep { color: #c9b98a; }
        .thanks { margin-top: 10px; text-align: center; color: #9a8550; font-size: 11.5px; font-weight: bold; }
        .terms { text-align: center; color: #999; font-size: 8px; margin-top: 3px; }
    </style>
</head>
<body>
    {{-- ===== HEADER ===== --}}
    <table class="top">
        <tr>
            <td style="width:48%; vertical-align:middle;">
                @if($logo)
                    <img src="{{ $logo }}" alt="TEXTURRA HOME" style="height:74px;">
                @else
                    <div class="wordmark">TEXTURRA <span class="accent">HOME</span><small>HOME &amp; TEXTIL</small></div>
                @endif
            </td>
            <td style="width:52%;" class="co">
                <span class="co-name">{{ $company['name'] ?? 'TEXTURRA HOME SRL' }}</span><br>
                {{ $company['legal_address'] ?? '' }}<br>
                CIF: {{ $company['unique_code'] ?? '' }} &nbsp;·&nbsp; Reg. Com.: {{ $company['registration_number'] ?? '' }}<br>
                Tel: {{ $company['phone'] ?? '' }}@if(!empty($company['iban'])) &nbsp;·&nbsp; IBAN: {{ $company['iban'] }}@endif
            </td>
        </tr>
    </table>
    <div class="rule"></div>
    <div class="rule-gold"></div>

    {{-- ===== TITLE + META ===== --}}
    <table class="band">
        <tr>
            <td style="vertical-align:bottom;">
                <span class="doc-title">OFERTĂ DE PREȚ<span class="sub">PROFORMĂ / DEVIZ ESTIMATIV</span></span>
            </td>
            <td style="width:240px;">
                <table class="meta" style="width:100%;">
                    <tr><td class="k">Număr</td><td class="v">{{ $quote->quote_number }}</td></tr>
                    <tr><td class="k">Data</td><td class="v">{{ $quote->created_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}</td></tr>
                    <tr><td class="k">Valabilitate</td><td class="v">30 zile</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== CLIENT ===== --}}
    <div class="client">
        <span class="label">Către / Client</span>
        <div class="name">{{ $quote->client_name }}</div>
        <div class="row">
            @if($quote->client_cif)CIF/CUI: {{ $quote->client_cif }}@endif
            @if($quote->client_address) &nbsp;·&nbsp; {{ $quote->client_address }}@endif
        </div>
        @if($quote->client_email || $quote->client_phone)
            <div class="row">
                @if($quote->client_email){{ $quote->client_email }}@endif
                @if($quote->client_phone) &nbsp;·&nbsp; {{ $quote->client_phone }}@endif
            </div>
        @endif
    </div>

    {{-- ===== LINES ===== --}}
    <table class="lines">
        <thead>
            <tr>
                <th style="width:4%;" class="c">#</th>
                <th style="width:34%;">Denumire</th>
                <th style="width:7%;" class="c">UM</th>
                <th style="width:9%;" class="r">Cant.</th>
                <th style="width:14%;" class="r">Preț unitar</th>
                <th style="width:13%;" class="r">Valoare net</th>
                <th style="width:9%;" class="r">TVA 21%</th>
                <th style="width:10%; border-right:none;" class="r">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->lines as $i => $line)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $line->description }}</td>
                    <td class="c">{{ $line->unit }}</td>
                    <td class="r">{{ rtrim(rtrim(number_format($line->quantity, 2), '0'), '.') }}</td>
                    <td class="r">{{ number_format($line->unit_price, 2) }}</td>
                    <td class="r">{{ number_format($line->line_net, 2) }}</td>
                    <td class="r">{{ number_format($line->line_vat, 2) }}</td>
                    <td class="r tot" style="border-right:none;">{{ number_format($line->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== TOTALS ===== --}}
    <table class="totals">
        <tr><td class="k">Total fără TVA</td><td class="v">{{ number_format($quote->total_net, 2) }} RON</td></tr>
        <tr><td class="k">TVA (21%)</td><td class="v">{{ number_format($quote->total_vat, 2) }} RON</td></tr>
        <tr class="grand"><td>TOTAL DE PLATĂ</td><td style="text-align:right;">{{ number_format($quote->total_gross, 2) }} RON</td></tr>
    </table>

    @if($quote->notes)
        <div class="notes"><strong>Observații:</strong> {{ $quote->notes }}</div>
    @endif

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <div class="bar"></div>
        <table class="ftab">
            <tr>
                <td style="width:34%; vertical-align:middle;">
                    @if($logo)<img src="{{ $logo }}" alt="TEXTURRA HOME" style="height:40px;">@endif
                </td>
                <td class="fcontact">
                    {!! $icon('phone') !!} {{ $company['phone'] ?? '' }}
                    <span class="sep">&nbsp;·&nbsp;</span> {!! $icon('mail') !!} {{ $brand['email'] }}
                    <span class="sep">&nbsp;·&nbsp;</span> {!! $icon('web') !!} {{ $brand['website'] }}<br>
                    {!! $icon('facebook') !!} {{ $brand['facebook'] }}
                    <span class="sep">&nbsp;·&nbsp;</span> {!! $icon('instagram') !!} {{ $brand['instagram'] }}
                </td>
            </tr>
        </table>
        <div class="thanks">Vă mulțumim pentru încredere!</div>
        <div class="terms">Ofertă valabilă 30 de zile de la data emiterii. Prețurile sunt exprimate în RON și includ TVA 21% acolo unde este indicat.</div>
    </div>
</body>
</html>
