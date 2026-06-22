@extends('layouts.base')

@section('content')

    {{-- ============================================================
         HERO — redesign 2026 (editorial-luxury, full-bleed photo).
         Replaces the old autoplay-video hero. CTAs use real routes:
         primary → first top-level category, secondary → consultanță.
    ============================================================ --}}
    @php
        $heroCategory = $topCategories->first();
    @endphp
    <section class="hp-hero relative isolate w-full overflow-hidden bg-[#2d271f] font-dm"
             aria-labelledby="hero-title">

        {{-- Full-bleed background photo (WebP + JPG fallback, optimized) --}}
        <picture>
            <source srcset="{{ asset('storage/images/homepage/hero-living-room.webp') }}" type="image/webp">
            <img src="{{ asset('storage/images/homepage/hero-living-room.jpg') }}"
                 alt="Living luminos cu perdele și draperii în tonuri calde"
                 class="absolute inset-0 h-full w-full object-cover object-center"
                 fetchpriority="high" decoding="async">
        </picture>

        {{-- Readability gradients: dense on the left for storytelling, photo stays clean on the right --}}
        <div class="hp-hero__shade absolute inset-0 z-[1]"></div>
        <div class="hp-hero__shade-b absolute inset-x-0 bottom-0 z-[1] h-[30%]"></div>

        {{-- Content, anchored to the lower-left half --}}
        <div class="relative z-[2] mx-auto flex min-h-[var(--hp-hero-h)] max-w-[1440px] items-end
                    px-5 pb-16 pt-36 md:px-[76px] md:pb-24 md:pt-48">
            <div class="w-full max-w-[548px] text-white">
                <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#ead8b8]">
                    Textile care transformă
                </p>
                <h1 id="hero-title"
                    class="font-display text-4xl font-semibold leading-[1.05] sm:text-5xl md:text-[56px]">
                    Eleganță pentru fiecare spațiu
                </h1>
                <p class="mt-6 mb-8 max-w-[500px] text-[15px] leading-[1.62] text-white/[0.86] md:text-[17px]">
                    Descoperă perdele, draperii și textile premium, create pentru confort și
                    rafinament în fiecare cameră.
                </p>
                <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:gap-[18px]">
                    <a href="{{ $heroCategory ? route('products.category', ['slug' => $heroCategory->slug]) : '#' }}"
                       class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-white px-7 py-3.5
                              text-sm font-semibold text-[#171411] transition hover:bg-[#f1ece4]">
                        Descoperă produsele <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('about') }}"
                       class="inline-flex items-center justify-center gap-2 self-start text-sm font-medium
                              text-white underline decoration-white/40 underline-offset-[6px]
                              transition hover:decoration-white sm:self-auto">
                        Programează o consultație <span aria-hidden="true">↗</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Scroll hint (desktop only) --}}
        <div class="absolute bottom-7 right-5 z-[2] hidden items-center gap-3 text-[11px] text-white/[0.78]
                    md:right-[76px] md:flex">
            <span class="block h-px w-10 bg-white/50"></span>
            <span>Explorează mai jos</span>
        </div>
    </section>
    {{-- Hero-specific CSS (gradients, height var) lives in resources/css/app.css
         under "Homepage redesign 2026" — keeps @media out of Blade parsing. --}}

    {{-- ============================================================
         CATEGORY DOCK — redesign 2026 (section 2).
         Floating unified bar overlapping the hero (-49px). Real
         top-level categories from the DB, cohesive line-art icons.
         Desktop: single row; mobile: 3-col grid (2 rows for 6 items).
    ============================================================ --}}
    @php
        // Cohesive inline line-art icon set (uniform 24x24, currentColor) keyed by
        // the real top-level category names. Falls back to a generic grid glyph.
        $dockIcons = [
            'Perdele'         => '<path d="M3 3h18"/><path d="M5 3v13a3 3 0 0 0 3 3"/><path d="M9.5 3v18"/><path d="M14.5 3v18"/><path d="M19 3v13a3 3 0 0 1-3 3"/>',
            'Draperii'        => '<path d="M3 3h18"/><path d="M5.2 3C4.6 9 4.4 15 5.6 21"/><path d="M9.1 3C8.8 9 8.8 15 9.3 21"/><path d="M12 3v18"/><path d="M14.9 3c.3 6 .3 12-.2 18"/><path d="M18.8 3c.6 6 .8 12-.4 18"/>',
            'Lenjerii de pat' => '<path d="M3 18v-4.5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2V18"/><path d="M3 18v2M21 18v2"/><path d="M3 15h18"/><path d="M6.5 11.5V9.2A1.7 1.7 0 0 1 8.2 7.5h3.1a1.7 1.7 0 0 1 1.7 1.7v2.3"/>',
            'Covoare'         => '<path d="M5 7h14v10H5z"/><path d="M8 10h8v4H8z"/><path d="M7 5v2M11 5v2M15 5v2"/><path d="M7 17v2M11 17v2M15 17v2"/>',
            'Accesorii'       => '<circle cx="12" cy="4.5" r="1.8"/><path d="M12 6.3v3.2"/><path d="M8 9.5h8l-1.1 3.3a3 3 0 0 1-5.8 0z"/><path d="M9.5 13v5.5M12 13v6.5M14.5 13v5.5"/>',
            'Galerii & Sine'  => '<path d="M2.5 8h19"/><circle cx="2.5" cy="8" r="1.4"/><circle cx="21.5" cy="8" r="1.4"/><circle cx="8" cy="11" r="2.2"/><circle cx="12" cy="11" r="2.2"/><circle cx="16" cy="11" r="2.2"/>',
        ];
        $dockIconDefault = '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9h16M9 4v16"/>';
    @endphp

    <section aria-label="Categorii de produse" class="relative z-[3] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)]">
            <nav class="hp-dock -mt-7 grid grid-cols-3 overflow-hidden rounded-[18px] border border-[#dad0c4]/90
                        bg-white/[0.96] shadow-[0_15px_45px_rgba(43,32,19,0.12)] backdrop-blur-md
                        md:-mt-[49px] md:grid-cols-6 md:rounded-[24px]">
                @foreach ($topCategories as $cat)
                    <a href="{{ route('products.category', ['slug' => $cat->slug]) }}"
                       class="hp-dock__item group grid min-h-[96px] place-items-center gap-2.5 px-2 py-5
                              text-center transition-colors duration-200 hover:bg-[#f8f2ea] md:min-h-[118px]">
                        <span class="grid h-[38px] w-[38px] place-items-center rounded-full border border-[#ded4c7]
                                     text-[#463f36] transition-colors duration-200
                                     group-hover:border-[#B28D4E] group-hover:text-[#B28D4E]">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                                 stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                {!! $dockIcons[$cat->name] ?? $dockIconDefault !!}
                            </svg>
                        </span>
                        <span class="text-[11px] font-bold uppercase leading-[1.22] tracking-wide text-[#171411]">
                            {{ $cat->name }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    {{-- Proof band (section 3): real, user-confirmed trust statistics. Static (no animated counters), editorial. --}}
    @php
        // Line-art icons (Lucide-style, 24×24, stroke currentColor) matching the dock's visual language.
        $proofStats = [
            ['num' => '11+',      'label' => 'Ani de experiență',       'icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>'],
            ['num' => '500.000+', 'label' => 'Produse vândute',         'icon' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>'],
            ['num' => '10+',      'label' => 'Consultanți specializați', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
            ['num' => '120+',     'label' => 'Parteneri comerciali',     'icon' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
            ['num' => '2',        'label' => 'Magazine fizice',          'icon' => '<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/>'],
        ];
    @endphp

    <section aria-label="De ce TEXTURRA" class="w-full bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)] py-14 md:py-20">
            <div class="grid grid-cols-2 gap-y-12 md:grid-cols-5 md:gap-y-0">
                @foreach ($proofStats as $i => $stat)
                    <div class="flex flex-col items-center px-2 text-center
                                {{ $loop->last ? 'col-span-2 md:col-span-1' : '' }}
                                {{ $i > 0 ? 'md:border-l md:border-[#171411]/10' : '' }}">
                        <span class="mb-3 text-[#B28D4E]">
                            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor"
                                 stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                {!! $stat['icon'] !!}
                            </svg>
                        </span>
                        <span class="font-display text-[32px] font-semibold leading-none text-[#171411] md:text-[38px]">
                            {{ $stat['num'] }}
                        </span>
                        <span class="mt-2.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#171411]/55">
                            {{ $stat['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Noutăți (section 4): newest REAL products. Replaces the GPT-invented "collections"
         (Colecția Diafan / sons-of-zeus / Pure Serenity) with real, latest catalogue items. --}}
    <section aria-label="Cele mai noi produse" class="w-full bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)] py-16 md:py-24">
            <div class="mb-10 flex items-end justify-between gap-4">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B28D4E]">Noutăți</p>
                    <h2 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[40px]">
                        Cele mai noi produse
                    </h2>
                </div>
                @php
                    $viewAllSlug = optional(optional($newestProducts->first())->category)->slug
                        ?? optional($topCategories->first())->slug;
                @endphp
                @if ($viewAllSlug)
                    <a href="{{ route('products.category', ['slug' => $viewAllSlug]) }}"
                       class="shrink-0 border-b border-[#171411]/30 pb-1 text-[12px] font-semibold uppercase tracking-[0.14em] text-[#171411] transition-colors hover:border-[#B28D4E] hover:text-[#B28D4E]">
                        Vezi toate &rarr;
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-x-5 gap-y-10 md:grid-cols-4 md:gap-x-7">
                @foreach ($newestProducts as $product)
                    <article class="group" wire:key="newest-{{ $product->id }}">
                        <div class="relative overflow-hidden rounded-[14px] bg-white">
                            <livewire:favorites-button :product-id="$product->id" wire:key="newest-fav-{{ $product->id }}" />
                            <span class="absolute left-3 top-3 z-[2] rounded-full bg-[#171411] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.1em] text-[#FCFAF7]">Nou</span>
                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="block aspect-[3/4] overflow-hidden">
                                <img src="{{ asset(($product->images[0]) ?? 'storage/images/placeholder-images.webp') }}"
                                     alt="{{ strip_tags($product->name) }}" loading="lazy"
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            </a>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}"
                               class="line-clamp-2 text-[13px] font-semibold uppercase leading-snug tracking-wide text-[#171411] hover:text-[#B28D4E]">
                                {{ strip_tags($product->name) }}
                            </a>
                            @if (!empty($product->colors_with_css) && count($product->colors_with_css))
                                <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                                    @foreach ($product->colors_with_css->take(6) as $color)
                                        <span class="h-4 w-4 rounded-full border border-[#171411]/15"
                                              style="background-color: {{ $color['css'] }}" title="{{ $color['name'] }}"></span>
                                    @endforeach
                                </div>
                            @endif
                            <p class="mt-3 font-display text-lg font-semibold text-[#171411]">
                                {{ number_format($product->price(), 2) }} <span class="text-sm font-normal text-[#171411]/60">lei / m</span>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Showrooms (section 7): the 2 REAL Ploiești locations. Replaces the previous 3 cards
         (which included a Blejoi/Lems entry). Maps links reuse the existing Google Maps SEARCH
         URLs — user can swap for exact pin URLs. No opening hours were provided, so omitted.
         Count (2) is consistent with the section-3 proof band. --}}
    <section aria-label="Magazine fizice" class="w-full bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)] py-16 md:py-24">
            <div class="mb-12 text-center">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B28D4E]">Showroom-uri</p>
                <h2 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[40px]">
                    Vino în showroom
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-[#171411]/60">
                    Te așteptăm în cele două showroom-uri din Ploiești — vezi, atinge și alege textilele potrivite, alături de consultanții noștri.
                </p>
            </div>
            <div class="mx-auto grid max-w-3xl grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach ([
                    ['name' => 'George Coșbuc nr. 13', 'addr' => 'Zona Halelor Centrale, Ploiești', 'maps' => 'https://www.google.com/maps/search/?api=1&query=George+Cosbuc+13+Ploiesti'],
                    ['name' => 'Omnia Winmark, Etaj 2', 'addr' => 'Ploiești — magazin în centrul comercial', 'maps' => 'https://www.google.com/maps/search/?api=1&query=Omnia+Winmark+Ploiesti'],
                ] as $shop)
                    <div class="flex flex-col rounded-[16px] border border-[#171411]/10 bg-white p-7 shadow-sm transition-shadow hover:shadow-md">
                        <span class="mb-5 grid h-12 w-12 place-items-center rounded-full bg-[#171411] text-[#FCFAF7]">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <h3 class="font-display text-xl font-semibold text-[#171411]">{{ $shop['name'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#171411]/60">{{ $shop['addr'] }}</p>
                        <a href="{{ $shop['maps'] }}" target="_blank" rel="noopener"
                           class="mt-6 inline-flex w-fit items-center gap-2 border-b border-[#171411]/30 pb-1 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#171411] transition-colors hover:border-[#B28D4E] hover:text-[#B28D4E]">
                            Vezi pe Google Maps
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>




    {{-- Color palette (section 5, REDO): MINIMALIST full-width flat swatches — NO images, NO circles.
         Flat color = first color's cod_css per ColorGroup. Layout mirrors the design package
         ("Descoperă culorile"): title left, swatches fill the middle, CTA right. No public per-group
         filter route exists → swatches + CTA link to the main category listing (general fallback). --}}
    @php
        $paletteListingSlug = optional($topCategories->first())->slug;
    @endphp
    <section aria-label="Paletar de culori" class="w-full border-y border-[#171411]/[0.08] bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)] py-12 md:py-16">
            <div class="flex flex-col gap-7 md:flex-row md:items-center md:gap-9">
                <div class="md:w-[180px] md:shrink-0">
                    <h2 class="font-display text-2xl font-semibold leading-none text-[#171411]">Descoperă culorile</h2>
                    <p class="mt-2.5 text-xs leading-snug text-[#766d64]">Alege nuanța potrivită spațiului tău.</p>
                </div>

                <div class="flex flex-1 flex-wrap items-center gap-2.5 md:flex-nowrap md:justify-between md:gap-3">
                    @foreach ($colorGroups as $group)
                        <a @if ($paletteListingSlug) href="{{ route('products.category', ['slug' => $paletteListingSlug]) }}" @else href="#" @endif
                           title="{{ $group->name }}" aria-label="{{ $group->name }}"
                           class="aspect-square w-[clamp(30px,3vw,44px)] shrink-0 rounded-[4px] border border-[#3c2b1a]/20 shadow-[inset_0_0_0_3px_rgba(255,255,255,0.14)] transition-transform duration-200 hover:scale-110"
                           style="background-color: {{ optional($group->colors->first())->cod_css ?? '#cccccc' }}"></a>
                    @endforeach
                </div>

                @if ($paletteListingSlug)
                    <a href="{{ route('products.category', ['slug' => $paletteListingSlug]) }}"
                       class="inline-flex shrink-0 items-center gap-1.5 border-b border-[#171411]/30 pb-1 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#171411] transition-colors hover:border-[#B28D4E] hover:text-[#B28D4E]">
                        Vezi culorile <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Confecție la comandă (section 6, REDO): implemented EXACTLY from the design package
         (texturra-homepage-redesign/index.html #la-comanda): 38/62 banner card, eyebrow,
         ✦ service points in circular badges, circular quality-seal over the image, dark button.
         3 points = package text (Materiale premium / Confecție în atelier propriu / Montaj
         profesional) — user CONFIRMED own atelier + montaj. CTA → real store phone. --}}
    <section aria-label="Confecție la comandă" class="w-full bg-[#FCFAF7] font-dm">
        <div class="px-[clamp(20px,4.2vw,76px)] py-12 md:py-20">
            <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 items-stretch overflow-hidden rounded-[20px] border border-[#e7ded3] bg-[#f6f0e7] md:min-h-[398px] md:grid-cols-[38%_62%] md:shadow-[0_20px_60px_rgba(43,32,19,0.10)]">
                {{-- Text panel (38%) --}}
                <div class="flex items-center p-8 sm:p-10 md:p-[clamp(35px,4vw,65px)]">
                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.16em] text-[#936f35]">Realizate pentru tine</p>
                        <h2 class="font-display text-[clamp(29px,2.55vw,45px)] font-semibold leading-[1.04] tracking-[-0.035em] text-[#171411]">
                            Perdele și draperii la comandă
                        </h2>
                        <p class="my-[18px] max-w-[610px] text-[14px] leading-[1.65] text-[#70675e]">
                            Consultanță personalizată, măsurători exacte și confecții premium pentru un rezultat impecabil în fiecare cameră.
                        </p>
                        <div class="mb-[30px] flex flex-wrap gap-[14px]">
                            @foreach (['Materiale premium', 'Confecție în atelier propriu', 'Montaj profesional'] as $pt)
                                <span class="flex items-center gap-[7px] text-[11px] text-[#62584d]">
                                    <span class="grid h-6 w-6 place-items-center rounded-full border border-[#dbcdbb] text-[13px] leading-none text-[#936f35]">✦</span>
                                    {{ $pt }}
                                </span>
                            @endforeach
                        </div>
                        <a href="tel:{{ config('app.store_owner.phone') }}"
                           class="inline-flex min-h-[48px] items-center justify-center gap-[11px] rounded-[4px] border border-[#171411] bg-[#171411] px-5 text-[12px] font-bold uppercase tracking-[0.035em] text-white transition-colors hover:border-[#936f35] hover:bg-[#936f35]">
                            Programează consultație <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
                {{-- Image panel (62%) with circular quality seal --}}
                <div class="relative min-h-[270px] overflow-hidden md:min-h-[300px]">
                    <img src="{{ asset('storage/images/homepage/custom-curtains.webp') }}"
                         alt="Consultant care ajustează o perdea din material natural"
                         class="h-full w-full object-cover object-center" loading="lazy" />
                    <span class="absolute right-[30px] top-7 grid h-[103px] w-[103px] place-items-center rounded-full border border-[#9d7231]/60 bg-[#fef9f1]/90 text-center text-[10px] font-extrabold uppercase leading-[1.3] tracking-[0.075em] text-[#936f35]">
                        Confecție<br>premium<br>la comandă
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Inspirație (section 8): links to REAL categories. The mockup's 'Perne decorative' /
         'Plaiduri & Pături' / 'Decorațiuni' are NOT real DB categories (Accesorii = inele/ciucuri/
         rejansă), so those cards link to the closest real category (Lenjerii de pat); the two rug
         cards map exactly to covoare-moderne / covoare-clasice. Images optimized to WebP. --}}
    @php
        $inspiration = [
            ['label' => 'Covoare moderne',   'sub' => 'Texturi contemporane',   'slug' => 'covoare-moderne', 'img' => 'inspiration-modern-rug'],
            ['label' => 'Covoare clasice',   'sub' => 'Eleganță atemporală',    'slug' => 'covoare-clasice', 'img' => 'inspiration-classic-rug'],
            ['label' => 'Perne decorative',  'sub' => 'Accente pentru living',   'slug' => 'lenjerii-de-pat', 'img' => 'inspiration-pillows'],
            ['label' => 'Pături & plaiduri', 'sub' => 'Confort pentru dormitor', 'slug' => 'lenjerii-de-pat', 'img' => 'inspiration-throws'],
        ];
    @endphp
    <section aria-label="Inspirație pentru casă" class="w-full bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1440px] px-[clamp(20px,4.2vw,76px)] py-16 md:py-24">
            <div class="mb-12 text-center">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B28D4E]">Inspirație</p>
                <h2 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[40px]">
                    Inspirație pentru casa ta
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-[#171411]/60">
                    Completează atmosfera cu textile și covoare care dau caracter fiecărei camere.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
                @foreach ($inspiration as $item)
                    <a href="{{ route('products.category', ['slug' => $item['slug']]) }}" class="group block">
                        <div class="relative aspect-[3/4] overflow-hidden rounded-[14px]">
                            <img src="{{ asset('storage/images/homepage/'.$item['img'].'.webp') }}"
                                 alt="{{ $item['label'] }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-4 text-left">
                                <span class="block font-display text-lg font-semibold leading-tight text-white">{{ $item['label'] }}</span>
                                <span class="mt-0.5 flex items-center gap-1 text-[11px] font-medium uppercase tracking-[0.1em] text-white/80">
                                    {{ $item['sub'] }}
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
