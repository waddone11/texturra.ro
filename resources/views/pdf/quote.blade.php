<!DOCTYPE html>
<html lang="ro">
@php
    // dompdf: enable_remote=false → use local file paths (chroot=base_path), not base64;
    // inline <svg> doesn't render → icons are local SVG files via public_path. Manrope/Google
    // Fonts can't load either → DejaVu Sans (the only bundled font with RO diacritics).
    $logoPath = public_path('storage/images/logo_black.png');
    $logo = is_file($logoPath) ? $logoPath : null;
    $icon = fn (string $name, int $px): string => is_file(public_path("images/pdf-icons/{$name}.svg"))
        ? '<img src="' . public_path("images/pdf-icons/{$name}.svg") . '" style="height:' . $px . 'px; width:' . $px . 'px; vertical-align:middle;">'
        : '';
    $lei = fn ($n): string => number_format((float) $n, 2, ',', '.') . ' lei';

    $issued = $quote->created_at ?? now();

    // Supplier — FIXED (hardcoded, authoritative). The mock had wrong data.
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
    $tagline = 'Intră în universul TEXTURRA — textile pentru casă create cu stil și pasiune.';
@endphp
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 0; }
        body { margin: 0; color: #1a1a1a; font-size: 10px; }

        .content { padding: 15mm 16mm 0 16mm; }

        /* header */
        .doc-title { font-size: 19px; font-weight: bold; letter-spacing: 2px; color: #1a1a1a; }
        .accent { background: #A85C32; height: 2px; }
        .meta { font-size: 10px; color: #4a4641; line-height: 1.8; }
        .meta .k { color: #8b857d; }
        .meta .v { font-weight: bold; color: #1a1a1a; }
        .hr { height: 1px; background: #e2ded6; }

        /* parties */
        .ptitle { font-size: 9px; font-weight: bold; letter-spacing: 2px; }
        .pname { font-size: 12.5px; font-weight: bold; color: #1a1a1a; }
        .pbody { font-size: 9.5px; color: #4a4641; line-height: 1.85; }
        .pbody .k { color: #8b857d; }

        /* lines table */
        table.lines { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.lines th {
            background: #1d1d1d; color: #f2efe9; font-size: 8px; font-weight: bold;
            letter-spacing: 0.5px; text-transform: uppercase; padding: 8px 7px;
        }
        table.lines td { padding: 9px 7px; font-size: 9.5px; border-bottom: 1px solid #ece8e1; vertical-align: top; word-wrap: break-word; }
        table.lines .num { color: #8b857d; }
        table.lines .desc { color: #1a1a1a; font-weight: bold; }
        table.lines .mut { color: #4a4641; }
        table.lines .r { text-align: right; }
        table.lines .c { text-align: center; }
        table.lines .tot { color: #1a1a1a; font-weight: bold; text-align: right; }

        /* summary */
        .note { font-size: 9px; line-height: 1.7; color: #6b6660; font-style: italic; }
        .term-k { font-size: 8px; font-weight: bold; letter-spacing: 1px; color: #A85C32; }
        .term-v { font-size: 10px; color: #1a1a1a; font-weight: bold; }
        .sum-k { font-size: 10px; color: #6b6660; }
        .sum-v { font-size: 10.5px; color: #1a1a1a; font-weight: bold; text-align: right; }
        .grand-l { font-size: 10.5px; font-weight: bold; letter-spacing: 0.5px; color: #1a1a1a; text-transform: uppercase; }
        .grand-v { font-size: 24px; font-weight: bold; color: #1a1a1a; text-align: right; }

        /* fixed footer (dark band, repeats on every page) */
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
                    {!! $icon('social-facebook', 15) !!} &nbsp;{!! $icon('social-instagram', 15) !!} &nbsp;{!! $icon('social-linkedin', 15) !!} &nbsp;{!! $icon('social-youtube', 15) !!} &nbsp;{!! $icon('social-web', 15) !!}
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
                <td style="text-align:right; vertical-align:top; width:230px;">
                    <div class="doc-title">OFERTĂ COMERCIALĂ</div>
                    <table style="margin:9px 0 9px auto;"><tr><td><div class="accent" style="width:54px;"></div></td></tr></table>
                    <div class="meta">
                        <div><span class="k">Nr. ofertă:</span> <span class="v">{{ $quote->quote_number }}</span></div>
                        <div><span class="k">Data emiterii:</span> <span class="v">{{ $issued->format('d.m.Y') }}</span></div>
                        <div><span class="k">Valabilitate:</span> <span class="v">{{ $issued->copy()->addDays(30)->format('d.m.Y') }}</span></div>
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
                        <div><span class="k">Telefon:</span> {{ $supplier['phone'] }}</div>
                        <div><span class="k">E-mail:</span> {{ $supplier['email'] }}</div>
                        <div><span class="k">Website:</span> {{ $supplier['website'] }}</div>
                    </div>
                </td>
                <td style="width:50%; vertical-align:top; padding-left:17px;">
                    <div class="ptitle" style="color:#1a1a1a;">CLIENT</div>
                    <div class="pname" style="margin:6px 0 5px 0;">{{ $quote->client_name }}</div>
                    <div class="pbody">
                        @if($quote->client_cif)<div><span class="k">CUI:</span> {{ $quote->client_cif }}</div>@endif
                        @if($quote->client_address)<div><span class="k">Adresă:</span> {{ $quote->client_address }}</div>@endif
                        @if($quote->client_phone)<div><span class="k">Telefon:</span> {{ $quote->client_phone }}</div>@endif
                        @if($quote->client_email)<div><span class="k">E-mail:</span> {{ $quote->client_email }}</div>@endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- lines --}}
        <table class="lines" style="margin-top:20px;">
            <thead>
                <tr>
                    <th style="width:5%; text-align:left;">Nr.</th>
                    <th style="width:30%; text-align:left;">Produs / Descriere</th>
                    <th style="width:13%; text-align:left;">Cod produs</th>
                    <th style="width:7%; text-align:center;">UM</th>
                    <th style="width:9%; text-align:right;">Cant.</th>
                    <th style="width:14%; text-align:right;">Preț unitar</th>
                    <th style="width:8%; text-align:right;">TVA</th>
                    <th style="width:14%; text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->lines as $i => $line)
                    <tr>
                        <td class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="desc">{{ $line->description }}</td>
                        <td class="mut">{{ $line->product?->product_code ?? '—' }}</td>
                        <td class="mut c">{{ $line->unit }}</td>
                        <td class="mut r">{{ rtrim(rtrim(number_format($line->quantity, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="mut r">{{ $lei($line->unit_price) }}</td>
                        <td class="mut r">21%</td>
                        <td class="tot">{{ $lei($line->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- summary --}}
        <table style="width:100%; margin-top:22px;">
            <tr>
                <td style="vertical-align:top; padding-right:30px;">
                    <p class="note" style="margin:0 0 16px 0;">„{{ $quote->notes ?: 'Oferta este valabilă în limita stocului disponibil și pentru perioada menționată mai sus. Termenul de livrare și disponibilitatea produselor se confirmă la plasarea comenzii.' }}”</p>
                    <div style="margin-bottom:8px;"><div class="term-k">TERMEN DE LIVRARE</div><div class="term-v">3–7 zile lucrătoare</div></div>
                    <div style="margin-bottom:8px;"><div class="term-k">MODALITATE DE PLATĂ</div><div class="term-v">OP / ordin de plată</div></div>
                    <div><div class="term-k">GARANȚIE</div><div class="term-v">Conform specificațiilor producătorului</div></div>
                </td>
                <td style="width:250px; vertical-align:top;">
                    <table style="width:100%;">
                        <tr><td class="sum-k" style="padding:5px 0;">Subtotal</td><td class="sum-v" style="padding:5px 0;">{{ $lei($quote->total_net) }}</td></tr>
                        <tr><td class="sum-k" style="padding:5px 0; border-bottom:1px solid #e2ded6;">TVA 21%</td><td class="sum-v" style="padding:5px 0; border-bottom:1px solid #e2ded6;">{{ $lei($quote->total_vat) }}</td></tr>
                    </table>
                    <table style="margin:14px 0 7px auto;"><tr><td><div class="accent" style="width:46px;"></div></td></tr></table>
                    <div class="grand-l">Total de plată</div>
                    <div class="grand-v" style="margin-top:3px;">{{ $lei($quote->total_gross) }}</div>
                    <div style="text-align:right; font-size:8px; color:#8b857d; margin-top:4px;">TVA inclus</div>
                </td>
            </tr>
        </table>

        {{-- spacer so content clears the fixed footer --}}
        <div style="height:95px;"></div>
    </div>

</body>
</html>
