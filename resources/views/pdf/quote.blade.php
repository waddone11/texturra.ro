<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <style>
        /* DejaVu Sans is bundled with dompdf and supports Romanian diacritics (ă â î ș ț). */
        * { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 28px 34px; }
        body { color: #2e2a22; font-size: 11px; }

        .brand-bar { background-color: #b8860b; height: 6px; }
        .header-table { width: 100%; margin-top: 14px; }
        .monogram {
            font-size: 26px; font-weight: bold; color: #8a6d1b; letter-spacing: 1px;
        }
        .monogram small { display: block; font-size: 9px; letter-spacing: 3px; color: #b8860b; font-weight: normal; }
        .company { font-size: 9.5px; color: #5a5345; line-height: 1.5; text-align: right; }
        .company strong { color: #2e2a22; }

        .title-row { margin-top: 18px; }
        .doc-title { font-size: 18px; font-weight: bold; color: #8a6d1b; }
        .doc-meta { text-align: right; font-size: 10px; color: #5a5345; }
        .doc-meta .num { font-size: 13px; font-weight: bold; color: #2e2a22; }

        .client-box {
            margin-top: 16px; background-color: #faf6ee; border: 1px solid #ead9b5;
            border-radius: 6px; padding: 10px 14px; line-height: 1.5;
        }
        .client-box .label { color: #b8860b; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
        .client-box .name { font-size: 13px; font-weight: bold; }

        table.lines { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.lines th {
            background-color: #b8860b; color: #fff; font-size: 9.5px; text-align: left;
            padding: 7px 8px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        table.lines td { padding: 7px 8px; border-bottom: 1px solid #eee3cc; font-size: 10.5px; vertical-align: top; }
        table.lines tr:nth-child(even) td { background-color: #fdfaf3; }
        .r { text-align: right; }
        .c { text-align: center; }
        .muted { color: #8a8472; font-size: 9px; }

        .totals { width: 46%; margin-top: 14px; margin-left: 54%; border-collapse: collapse; }
        .totals td { padding: 6px 10px; font-size: 11px; }
        .totals .lbl { color: #5a5345; }
        .totals .grand td {
            background-color: #b8860b; color: #fff; font-size: 13px; font-weight: bold; border-radius: 4px;
        }

        .notes { margin-top: 18px; background-color: #faf6ee; border-left: 3px solid #b8860b; padding: 8px 12px; font-size: 10px; color: #5a5345; }

        .footer { margin-top: 26px; border-top: 1px solid #ead9b5; padding-top: 12px; }
        .footer .thanks { color: #8a6d1b; font-size: 13px; font-weight: bold; }
        .footer .contact { color: #5a5345; font-size: 9.5px; line-height: 1.6; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="brand-bar"></div>

    <table class="header-table">
        <tr>
            <td style="width:50%;" class="monogram">
                TEXTURRA <span style="color:#b8860b;">HOME</span>
                <small>HOME &amp; TEXTIL</small>
            </td>
            <td class="company">
                <strong>{{ $company['name'] ?? 'TEXTURRA HOME SRL' }}</strong><br>
                {{ $company['legal_address'] ?? '' }}<br>
                CIF: {{ $company['unique_code'] ?? '' }} &nbsp;·&nbsp; Reg. Com.: {{ $company['registration_number'] ?? '' }}<br>
                Tel: {{ $company['phone'] ?? '' }}
                @if(!empty($company['iban']))<br>IBAN: {{ $company['iban'] }} ({{ $company['bank'] ?? '' }})@endif
            </td>
        </tr>
    </table>

    <table class="title-row" style="width:100%;">
        <tr>
            <td style="width:50%;"><span class="doc-title">Ofertă de preț</span></td>
            <td class="doc-meta">
                <span class="num">{{ $quote->quote_number }}</span><br>
                Data: {{ $quote->created_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}
            </td>
        </tr>
    </table>

    <div class="client-box">
        <span class="label">Client</span>
        <div class="name">{{ $quote->client_name }}</div>
        @if($quote->client_cif)CIF/CUI: {{ $quote->client_cif }}<br>@endif
        @if($quote->client_address){{ $quote->client_address }}<br>@endif
        @if($quote->client_email){{ $quote->client_email }}@endif
        @if($quote->client_phone) &nbsp;·&nbsp; {{ $quote->client_phone }}@endif
    </div>

    <table class="lines">
        <thead>
            <tr>
                <th style="width:4%;" class="c">#</th>
                <th style="width:30%;">Denumire</th>
                <th style="width:7%;" class="c">UM</th>
                <th style="width:8%;" class="r">Cant.</th>
                <th style="width:12%;" class="r">Preț unitar</th>
                <th style="width:12%;" class="r">Valoare net</th>
                <th style="width:12%;" class="r">TVA 21%</th>
                <th style="width:15%;" class="r">Total</th>
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
                    <td class="r"><strong>{{ number_format($line->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="lbl">Total fără TVA</td>
            <td class="r">{{ number_format($quote->total_net, 2) }} RON</td>
        </tr>
        <tr>
            <td class="lbl">TVA (21%)</td>
            <td class="r">{{ number_format($quote->total_vat, 2) }} RON</td>
        </tr>
        <tr class="grand">
            <td>Total de plată</td>
            <td class="r">{{ number_format($quote->total_gross, 2) }} RON</td>
        </tr>
    </table>

    @if($quote->notes)
        <div class="notes"><strong>Observații:</strong> {{ $quote->notes }}</div>
    @endif

    <div class="footer">
        <div class="thanks">Vă mulțumim pentru încredere!</div>
        <div class="contact">
            Ofertă valabilă 30 de zile de la data emiterii. Prețurile sunt exprimate în RON.<br>
            {{ $company['name'] ?? 'TEXTURRA HOME SRL' }} &nbsp;·&nbsp; Tel: {{ $company['phone'] ?? '' }}
            &nbsp;·&nbsp; www.texturra.ro &nbsp;·&nbsp; [facebook] &nbsp;·&nbsp; [instagram]
        </div>
    </div>
</body>
</html>
