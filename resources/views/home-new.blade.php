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
                    px-5 pb-16 pt-36 sm:px-8 md:px-[76px] md:pb-24 md:pt-48">
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
        <div class="mx-auto max-w-[1180px] px-5 sm:px-8">
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
        <div class="mx-auto max-w-[1180px] px-5 py-14 sm:px-8 md:py-20">
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

    @php
        // Map each parent category name to its corresponding icon URL.
        $categoryIcons = [
            'Perdele' => asset('storage/images/icons/perdele.png'),
            'Draperii' => asset('storage/images/icons/draperii.png'),
            'Covoare' => asset('storage/images/icons/covoare.png'),
            'Lenjerii de pat' => asset('storage/images/icons/lenjerii.png'),
            'Accesorii' => asset('storage/images/icons/accesorii.png'),
            'Galerii & Sine' => asset('storage/images/icons/sine.png'),


        ];

        $defaultIcon = asset('storage/images/icons/default.png');
    @endphp

    <div class="max-w-7xl mx-auto mt-8 md:mt-16 mb-8 px-4 md:px-0 relative overflow-hidden">
        <h2 class="text-3xl font-bold text-left textColor">
            Cele mai rafinate textile și perdele pentru casa ta
        </h2>
        <p class="mt-4 mb-8 md:mb-16 text-left text-gray-600">
            La <strong>TEXTURRA HOME SRL</strong> punem accent pe calitate, design și funcționalitate. Oferim o selecție atent aleasă de <strong>perdele, draperii și textile premium</strong> pentru casă, perfecte pentru a-ți transforma locuința într-un spațiu cald și elegant. Cu experiență în domeniu din 2017, venim în întâmpinarea clienților noștri cu produse moderne, la prețuri competitive și livrare rapidă.
        </p>

        <div class="max-w-7xl mx-auto px:0 md:px-4 py-2 pb-0 md:py-8 space-y-8">
            <div class="w-full overflow-hidden">
                <!-- Scrollable Container -->
                <div id="category-scroll" class="flex flex-nowrap gap-4 overflow-x-auto scroll-smooth pb-4 custom-scrollbar px-0">
                    @foreach ($topCategories as $category)
                        <div class="shrink-0 w-24 md:w-36 flex flex-col items-center text-center gap-4 transition duration-200 group">
                            <a href="{{ route('products.category', ['slug' => $category->slug]) }}">
                                <img
                                    src="{{ $categoryIcons[$category->name] ?? $defaultIcon }}"
                                    alt="{{ $category->name }}"
                                    class="w-24 md:w-36 p-6 object-cover mx-auto transition-transform duration-300 group-hover:scale-110 bg-gray-50 rounded-full shadow-md border border-gray-200"
                                />
                            </a>
                            <h2 class="text-sm font-bold uppercase text-gray-800">
                                <a href="{{ route('products.category', ['slug' => $category->slug]) }}" class="hover:underline">
                                    {{ $category->name }}
                                </a>
                            </h2>
                        </div>
                    @endforeach
                </div>

                <!-- Scroll Hint for Mobile -->
                <div class="flex justify-end mt-2 pr-4 md:hidden">
                    <img src="{{ asset('storage/images/swipe.svg') }}" alt="Swipe right" class="w-8 h-8 opacity-80 animate-pulse" id="category-scroll-hint">
                </div>
            </div>

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                .custom-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const scrollContainer = document.getElementById("category-scroll");
                    const scrollHint = document.getElementById("category-scroll-hint");

                    if (scrollContainer && scrollHint) {
                        scrollContainer.addEventListener("scroll", function () {
                            if (this.scrollLeft > 10) {
                                scrollHint.style.opacity = "0";
                            } else {
                                scrollHint.style.opacity = "1";
                            }
                        });
                    }
                });
            </script>

        </div>
    </div>


    <!-- Swiper JS Initialization -->
    <script>
        var swiper = new Swiper('.swiper-container2', {
            // Enable grid layout:
            grid: {
                rows: 1,             // 2 rows
                fill: 'row',         // Fill items row by row
            },
            slidesPerView: 2,      // On mobile: 2 columns
            spaceBetween: 10,
            slidesPerGroup: 2,     // Move 2 slides at a time on mobile
            breakpoints: {
                768: {               // e.g., from tablet size upward
                    slidesPerView: 48,  // 4 columns on desktop
                    slidesPerGroup: 4, // Move 4 slides at a time
                    spaceBetween: 20,
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>


    <!-- Parallax Section -->
    <div class="relative bg-fixed bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/images/bg.jpg') }}');min-height: 500px;">
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Content Container -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center text-white px-4 py-12 md:py-24">
            <h2 class="text-3xl md:text-5xl font-bold mb-2">
                Intră în universul TEXTURRA
            </h2>
            <p class="text-white text-lg md:text-2xl mb-10">
                Textile pentru casă create cu stil și pasiune.
            </p>

            <!-- Metrics Row -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 max-w-6xl mx-auto">
                <!-- Ani de experiență -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">11+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Ani de experiență</span>
                </div>

                <!-- Produse vândute -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">500.000+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Produse vândute</span>
                </div>

                <!-- Consultanți -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">10+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Consultanți specializați</span>
                </div>

                <!-- Parteneri comerciali -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">120+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Parteneri comerciali</span>
                </div>

                <!-- Magazine fizice -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">3</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Magazine fizice</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-0 py-8 md:mt-8">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Block Example -->
            @php
                $infoBlocks = [
                    [
                        'icon' => 'transport.svg',
                        'title' => 'Transport gratuit',
                        'text' => 'De la 500 lei',
                        'alt' => 'Transport Gratuit'
                    ],
                    [
                        'icon' => 'livrare.svg',
                        'title' => 'Livrare rapidă',
                        'text' => '24h, 48h la distanțe considerabile',
                        'alt' => 'Livrare Rapidă'
                    ],
                    [
                        'icon' => 'consultanta.svg',
                        'title' => 'Consultanță pe produs',
                        'text' => 'Vrei un detaliu? Sună-ne!',
                        'alt' => 'Consultanță pe produs'
                    ],
                    [
                        'icon' => 'sliders.svg',
                        'title' => 'Personalizare produse',
                        'text' => 'Online și gratuit',
                        'alt' => 'Personalizare produse'
                    ]
                ];
            @endphp

            @foreach ($infoBlocks as $block)
                <div class="flex flex-col md:flex-row items-center bg-gray-100 rounded-lg p-4 shadow-md text-center md:text-left">
                    <img
                        src="{{ asset('storage/images/icons/' . $block['icon']) }}"
                        alt="{{ $block['alt'] }}"
                        class="w-10 h-10 md:w-12 md:h-12 mb-2 md:mb-0 md:mr-4"
                    />
                    <div>
                        <h3 class="text-sm md:text-base font-semibold text-gray-800">{{ $block['title'] }}</h3>
                        <p class="text-xs md:text-sm text-gray-600">{{ $block['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Noutăți (section 4): newest REAL products. Replaces the GPT-invented "collections"
         (Colecția Diafan / sons-of-zeus / Pure Serenity) with real, latest catalogue items. --}}
    <section aria-label="Cele mai noi produse" class="w-full bg-[#FCFAF7] font-dm">
        <div class="mx-auto max-w-[1180px] px-5 py-16 sm:px-8 md:py-24">
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

    <!-- Parallax Section -->
    <div class="relative bg-fixed bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/images/bg_map.png') }}');min-height: 500px;">
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Content Container -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center text-white py-12 md:py-24">
            <div class="max-w-7xl mx-auto w-full px-6 md:px-0">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-white mb-10">
                    Găsește-ne în magazinele fizice
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <!-- Location 1 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c1.656 0 3-1.344 3-3s-1.344-3-3-3-3 1.344-3 3 1.344 3 3 3zm0 0c2.761 0 5 2.239 5 5 0 1.657-2.239 4-5 4s-5-2.343-5-4c0-2.761 2.239-5 5-5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">George Coșbuc nr.13</h3>
                        <p class="text-sm text-gray-600 mb-4">Zona Halelor Centrale, Ploiești</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=George+Cosbuc+13+Ploiesti" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>

                    <!-- Location 2 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9 6 9-6-9-6-9 6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15l9 6 9-6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Omnia Winmark, Etaj 2</h3>
                        <p class="text-sm text-gray-600 mb-4">Ploiești – Magazin în centrul comercial</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Omnia+Winmark+Ploiesti" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>

                    <!-- Location 3 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Magazin Lems, Etaj 1</h3>
                        <p class="text-sm text-gray-600 mb-4">Blejoi – în incinta showroom-ului Lems</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Magazin+Lems+Blejoi" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="max-w-7xl mx-auto px-6 md:px-0 py-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
            Caută produse după culoare
        </h2>
        <div class="grid grid-cols-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-6">

            @foreach($colorGroups as $group)
                <a href=""
                   class="group block text-center transition transform hover:scale-105">
                    <div class="w-full aspect-square bg-gray-100 rounded-xl overflow-hidden shadow">
                        <img src="{{ asset($group->image_path) }}"
                             alt="{{ $group->name }}"
                             class="object-cover w-full h-full transition duration-300 group-hover:opacity-90" />
                    </div>
                    <span class="mt-2 block text-sm font-semibold text-gray-700 group-hover:underline">
                    {{ $group->name }}
                </span>
                </a>
            @endforeach

        </div>
    </div>

    <section class="w-full bg-white border-t border-black pt-12 pb-4 mb-12">
        <div class="max-w-7xl mx-auto px-6 md:px-0 md:py-16">

            <!-- CURTAIN BLOCK -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start mb-24">
                <!-- TEXT BLOCK -->
                <div class="col-span-1 flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">Perdele, draperii <br/> la comandă</h1>
                    <p class="text-base md:text-lg mb-6">
                        Alege materialul preferat, dimensiunile exacte, tipul de rejansă și creează soluția perfectă pentru spațiul tău.
                        Produsele noastre sunt realizate manual, special pentru tine.
                    </p>
                    <a href="{{ route('products.category', ['slug' => 'perdele']) }}"
                       class="bg-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition w-fit">
                        Configurează perdeaua ta
                    </a>
                </div>

                <!-- BEFORE/AFTER SLIDER -->
                <div class="col-span-2 relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <div class="relative w-full h-full before-after-slider">
                        <img src="{{ asset('storage/images/tex_3.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-10 after-img" />
                        <img src="{{ asset('storage/images/tex_2.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-20 before-img" />
                        <div class="handle z-30"></div>
                    </div>
                </div>
            </div>

            <!-- RUG BLOCK -->
            <div class="flex flex-col-reverse md:grid md:grid-cols-3 gap-12 items-start mt-0 md:mt-8">



            <!-- BEFORE/AFTER SLIDER -->
                <div class="col-span-2 relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <div class="relative w-full h-full before-after-slider">
                        <img src="{{ asset('storage/images/rug_after.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-10 after-img" />
                        <img src="{{ asset('storage/images/rug_before.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-20 before-img" />
                        <div class="handle z-30"></div>
                    </div>
                </div>

                <!-- TEXT BLOCK -->
                <div class="col-span-1 flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">Covoare elegante <br/> pentru orice spațiu</h1>
                    <p class="text-base md:text-lg mb-6">
                        Completează atmosfera cu un covor care se potrivește perfect. Alege textura, dimensiunea și modelul potrivit pentru dormitor, living sau birou.
                    </p>
                    <a href="{{ route('products.category', ['slug' => 'covoare']) }}"
                       class="bg-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition w-fit">
                        Vezi colecția de covoare
                    </a>
                </div>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.before-after-slider').forEach(slider => {
                const beforeImg = slider.querySelector('.before-img');
                const handle = slider.querySelector('.handle');
                let isDragging = false;

                const updateSlider = (x) => {
                    const rect = slider.getBoundingClientRect();
                    const offsetX = Math.min(Math.max(0, x - rect.left), rect.width);
                    const percent = (offsetX / rect.width) * 100;
                    beforeImg.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
                    handle.style.left = `${percent}%`;
                };

                handle.addEventListener('mousedown', () => isDragging = true);
                document.addEventListener('mouseup', () => isDragging = false);
                document.addEventListener('mousemove', e => isDragging && updateSlider(e.clientX));

                handle.addEventListener('touchstart', () => isDragging = true);
                document.addEventListener('touchend', () => isDragging = false);
                document.addEventListener('touchmove', e => isDragging && updateSlider(e.touches[0].clientX));
            });
        });
    </script>




    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 100000, // 10s
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            // optional: on slide change, reset & animate progress bar
        });

        function resetProgress() {
            const progressBar = document.getElementById('progressBar');
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';
        }

        function animateProgress() {
            const progressBar = document.getElementById('progressBar');
            setTimeout(() => {
                progressBar.style.transition = 'width 10s linear';
                progressBar.style.width = '100%';
            }, 50);
        }

        swiper.on('slideChangeTransitionStart', () => {
            resetProgress();
        });
        swiper.on('slideChangeTransitionEnd', () => {
            animateProgress();
        });

        // Initialize progress on first load
        document.addEventListener("DOMContentLoaded", () => {
            animateProgress();
        });

    </script>


@endsection
