@extends('layouts.base')

@section('content')
    {{-- Product page redesigned from texturra-product-page-redesign package.
         ⚠️ MONEY ZONE UNTOUCHED: the configurator (price calc + add-to-cart) is the
         @include('products.product-form-custom' / 'product-form-standard') partials — kept
         byte-identical. Only the SHELL around them was restyled. Old shell: detail-old.blade.php. --}}
    <div class="mx-auto w-full max-w-[1320px] px-4 font-dm text-[#171411] md:px-8">

        {{-- ===== PRODUCT ===== --}}
        <section class="grid grid-cols-1 items-start gap-8 py-6 md:py-10 lg:grid-cols-[48%_52%] lg:gap-12" aria-label="Detaliu produs">

            {{-- Media --}}
            <div class="lg:sticky lg:top-28">
                <div class="relative overflow-hidden rounded-[18px] bg-[#e9e4da] shadow-[0_20px_50px_rgba(33,25,18,0.08)]">
                    <button type="button" onclick="openZoomGallery()" aria-label="Mărește imaginea"
                            class="absolute left-4 top-4 z-10 grid h-11 w-11 place-items-center rounded-full bg-[#fffdf9] text-[#171411] shadow-[0_8px_21px_rgba(22,17,12,0.12)]">
                        <i class="fa fa-search-plus"></i>
                    </button>
                    <img id="mainImage" src="{{ $product->images[0] ?? asset('storage/images/placeholder-images.webp') }}"
                         alt="{{ strip_tags($product->name) }}" class="aspect-square w-full object-cover" />
                </div>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    @foreach ($product->images as $image)
                        <button type="button" onclick="updateMainImage(this)" data-src="{{ $image }}"
                                class="thumbnail h-[76px] w-[76px] overflow-hidden rounded-[10px] border border-[#e4ddd4] bg-[#fffdf9] p-[3px] transition hover:border-[#ad7c32]">
                            <img src="{{ $image }}" alt="" class="h-full w-full rounded-[7px] object-cover" />
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Info --}}
            <div>
                <div class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-4">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.09em] text-[#ad7c32]">{{ $product->category->name ?? 'TEXTURRA' }}</p>
                        <h1 class="mt-2 font-display text-3xl font-medium leading-[1.0] tracking-[-0.02em] text-[#171411] md:text-[42px]">
                            {{ $product->name }}
                        </h1>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="text-right font-display text-2xl md:text-[30px]">
                            {{ number_format($product->price(), 2) }} lei
                            <small class="mt-1 block text-[11px] font-normal tracking-[0.04em] text-[#736b63]">TVA inclus / metru</small>
                        </div>
                        <livewire:favorites-button :product-id="$product->id" wire:key="fav-detail-{{ $product->id }}" />
                    </div>
                </div>

                {{-- Meta --}}
                <div class="mt-4 flex flex-wrap gap-x-[18px] gap-y-1.5 border-b border-[#e4ddd4] pb-4 text-[13px] text-[#736b63]">
                    <span>Cod: <b class="font-bold text-[#171411]">TXT-{{ $product->id }}</b></span>
                    @if ($product->materials->isNotEmpty())
                        <span>Material: <b class="font-bold text-[#171411]">{{ $product->materials->pluck('name')->unique()->implode(', ') }}</b></span>
                    @endif
                    @if ($product->colors->isNotEmpty())
                        <span>Culoare: <b class="font-bold text-[#171411]">{{ $product->colors->pluck('name')->unique()->implode(', ') }}</b></span>
                    @endif
                </div>

                {{-- Color swatches (display of this product's colours) --}}
                @if ($product->colors->isNotEmpty())
                    <div class="mt-5">
                        <p class="mb-2 text-[12px] font-bold">Culoare</p>
                        <div class="flex flex-wrap gap-2.5" role="group" aria-label="Culoare produs">
                            @foreach ($product->colors as $color)
                                <span class="h-[23px] w-[23px] rounded-full shadow-[inset_0_0_0_1px_rgba(0,0,0,0.12)] {{ $loop->first ? 'outline outline-2 outline-offset-[3px] outline-[#ad7c32]' : '' }}"
                                      style="background-color: {{ $color->cod_css }}" title="{{ $color->name }}"></span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Discount badge (display only) --}}
                @php
                    $originalPrice = $product->price ?? 0;
                    $discountedPrice = $product->price();
                    $discountPercentage = $originalPrice > 0 ? round((($originalPrice - $discountedPrice) / $originalPrice) * 100) : 0;
                @endphp
                @if($originalPrice > $discountedPrice)
                    <div class="mt-4 inline-flex items-center gap-2 rounded-md bg-[#171411] px-3 py-1.5 text-sm font-extrabold text-white">
                        <span class="text-red-400">−{{ $discountPercentage }}%</span>
                        <span class="text-white/60 line-through">{{ number_format($originalPrice, 2) }} lei</span>
                        <span class="text-[#e6c478]">{{ number_format($discountedPrice, 2) }} lei</span>
                    </div>
                @endif

                {{-- ===== CONFIGURATOR — MONEY ZONE, INCLUDED VERBATIM (NOT MODIFIED) ===== --}}
                <div class="mt-6">
                    @if($product->type === 'custom')
                        @include('products.product-form-custom', ['product' => $product])
                    @else
                        @include('products.product-form-standard', ['product' => $product])
                    @endif
                </div>

                {{-- Siblings — same model, other dimensions --}}
                @php $siblings = $product->siblings()->get(); @endphp
                @if ($siblings->isNotEmpty())
                    <div class="mt-7 border-t border-[#e4ddd4] pt-5">
                        <h3 class="mb-3 text-base font-bold">Același model, alte dimensiuni</h3>
                        <div class="flex flex-col gap-2">
                            @foreach ($siblings as $sibling)
                                @php $sibImg = is_array($sibling->images) ? ($sibling->images[0] ?? null) : null; @endphp
                                <a href="{{ route('product.show', ['slug' => $sibling->slug]) }}"
                                   class="flex items-center gap-3 rounded-[10px] border border-[#e4ddd4] p-2 transition hover:border-[#ad7c32]">
                                    @if ($sibImg)
                                        <img src="{{ $sibImg }}" alt="{{ strip_tags($sibling->name) }}" class="h-12 w-12 flex-shrink-0 rounded object-cover">
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-medium">{{ strip_tags($sibling->name) }}</div>
                                        @if ($sibling->height)
                                            <div class="text-xs text-[#736b63]">Înălțime: {{ $sibling->height }} m</div>
                                        @endif
                                    </div>
                                    <div class="whitespace-nowrap text-sm font-semibold">{{ number_format($sibling->price(), 2) }} lei</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- ===== TRUST BAND ===== --}}
        <section class="mb-12 grid grid-cols-2 overflow-hidden rounded-[12px] border border-[#e4ddd4] bg-[#fffdf9] md:grid-cols-4" aria-label="Beneficii TEXTURRA">
            @foreach ([
                ['i' => 'fa-vials',        't' => 'Mostre gratuite',  's' => 'vezi și simte materialele'],
                ['i' => 'fa-truck',        't' => 'Transport gratuit', 's' => 'la comenzi peste 500 lei'],
                ['i' => 'fa-rotate-left',  't' => 'Retur 14 zile',     's' => 'garanția satisfacției'],
                ['i' => 'fa-lock',         't' => 'Plată securizată',  's' => 'card sau ramburs'],
            ] as $b)
                <div class="flex min-h-[70px] items-center gap-3 border-b border-[#e4ddd4] px-4 py-3 md:border-b-0 md:border-r [&:last-child]:border-r-0 max-md:[&:nth-child(odd)]:border-r">
                    <i class="fa-solid {{ $b['i'] }} text-[1.1rem] text-[#8c5e1b]"></i>
                    <div>
                        <strong class="block text-[11px] uppercase tracking-wide">{{ $b['t'] }}</strong>
                        <span class="text-[11px] text-[#736b63]">{{ $b['s'] }}</span>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- ===== DETAILS TABS ===== --}}
        <section class="mb-14" x-data="{ tab: 'spec' }" id="details">
            <div class="flex flex-wrap gap-1 border-b border-[#e4ddd4]">
                @foreach ([['spec','Specificații'],['desc','Descriere'],['transport','Transport'],['retur','Retur'],['ingrijire','Întreținere']] as [$k,$label])
                    <button type="button" @click="tab='{{ $k }}'"
                            :class="tab==='{{ $k }}' ? 'border-[#ad7c32] text-[#171411]' : 'border-transparent text-[#736b63]'"
                            class="border-b-2 px-4 py-3 text-[13px] font-semibold transition-colors hover:text-[#171411]">{{ $label }}</button>
                @endforeach
            </div>

            <div class="pt-6 text-sm leading-relaxed text-[#3a342e]">
                {{-- Specificații --}}
                <div x-show="tab==='spec'" x-cloak>
                    <h2 class="mb-4 font-display text-2xl font-medium">Specificații</h2>
                    <table class="w-full max-w-2xl text-sm">
                        <tbody>
                        @forelse ($characteristicsWithLabels as $characteristic)
                            <tr class="border-b border-[#e4ddd4]">
                                <th class="py-2.5 pr-4 text-left font-semibold text-[#736b63]">{{ $characteristic['label'] }}</th>
                                <td class="py-2.5 text-[#171411]">{{ $characteristic['value'] }}</td>
                            </tr>
                        @empty
                            @if ($product->materials->isNotEmpty())
                                <tr class="border-b border-[#e4ddd4]"><th class="py-2.5 pr-4 text-left font-semibold text-[#736b63]">Material</th><td class="py-2.5">{{ $product->materials->pluck('name')->unique()->implode(', ') }}</td></tr>
                            @endif
                            @if ($product->colors->isNotEmpty())
                                <tr class="border-b border-[#e4ddd4]"><th class="py-2.5 pr-4 text-left font-semibold text-[#736b63]">Culoare</th><td class="py-2.5">{{ $product->colors->pluck('name')->unique()->implode(', ') }}</td></tr>
                            @endif
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Descriere --}}
                <div x-show="tab==='desc'" x-cloak class="prose-sm max-w-3xl">
                    {!! html_entity_decode($product->description ?: 'Descrierea acestui produs va fi disponibilă în curând.') !!}
                </div>

                {{-- Transport --}}
                <div x-show="tab==='transport'" x-cloak class="max-w-3xl">
                    <p>Transport gratuit la comenzile de peste 500 lei. Livrare în 24–48h pentru produsele din stoc; pentru confecția la comandă, termenul se comunică la finalizarea comenzii. Detaliile complete sunt disponibile la checkout.</p>
                </div>

                {{-- Retur --}}
                <div x-show="tab==='retur'" x-cloak class="max-w-3xl">
                    <p>{{ $product->warranty ?: 'Returul este acceptat în termen de 14 zile de la livrare, în condițiile politicii de retur. Produsele confecționate la comandă (pe dimensiuni custom) pot fi excluse de la retur.' }}</p>
                </div>

                {{-- Întreținere --}}
                <div x-show="tab==='ingrijire'" x-cloak class="max-w-3xl">
                    <ul class="space-y-2.5">
                        <li>Se spală la 30°C.</li>
                        <li>Nu se folosesc înălbitori chimici; stoarcere la maxim 600 de turații.</li>
                        <li>Se calcă la temperatură normală, cu abur.</li>
                        <li>Înainte de prima utilizare, recomandăm prespălarea produsului.</li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- ===== RELATED ===== --}}
        <section class="mb-14">
            <div class="mb-6 flex items-end justify-between gap-4">
                <h2 class="font-display text-2xl font-medium md:text-3xl">S-ar putea să îți placă</h2>
                @if($product->category)
                    <a href="{{ route('products.category', ['slug' => $product->category->slug]) }}"
                       class="shrink-0 border-b border-[#171411]/30 pb-1 text-[12px] font-semibold uppercase tracking-[0.12em] transition-colors hover:border-[#ad7c32] hover:text-[#8c5e1b]">
                        Vezi toate →
                    </a>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($product->category->products->where('id', '!=', $product->id)->take(5) as $relatedProduct)
                    <article wire:key="related-{{ $relatedProduct->id }}" class="group relative overflow-hidden rounded-[12px] border border-[#ece5dc] bg-[#fffdf9] transition hover:-translate-y-1 hover:shadow-[0_16px_40px_rgba(33,25,14,0.07)]">
                        <div class="absolute right-2.5 top-2.5 z-[2]">
                            <livewire:favorites-button :product-id="$relatedProduct->id" wire:key="related-fav-{{ $relatedProduct->id }}" />
                        </div>
                        <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}" class="block aspect-[4/4.55] overflow-hidden bg-[#eee]">
                            <img src="{{ asset(($relatedProduct->images[0]) ?? 'storage/images/placeholder-images.webp') }}"
                                 alt="{{ strip_tags($relatedProduct->name) }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.045]" />
                        </a>
                        <div class="p-[13px]">
                            <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}"
                               class="line-clamp-2 font-display text-[15px] font-medium leading-[1.15] text-[#171411] hover:text-[#8c5e1b]">
                                {{ strip_tags($relatedProduct->name) }}
                            </a>
                            <p class="mt-2 text-[14px] font-bold text-[#2e2923]">{{ number_format($relatedProduct->price(), 2) }} <small class="text-[10px] font-medium text-[#7c7165]">lei / m</small></p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    {{-- ===== ZOOM GALLERY (preserved) ===== --}}
    <div id="zoomGalleryPanel" class="fixed inset-0 z-50 translate-x-full transform overflow-hidden bg-white transition-transform duration-300">
        <div class="flex h-full">
            <div class="w-16 overflow-y-auto border-r border-gray-200 bg-white p-2 md:w-20">
                <div class="flex flex-col gap-2">
                    @foreach($product->images as $img)
                        <img src="{{ asset($img) }}" data-large="{{ asset($img) }}" onclick="changeZoomGalleryImage(this)"
                             class="zoom-thumb h-16 w-full cursor-pointer rounded border object-cover hover:ring-2"/>
                    @endforeach
                </div>
            </div>
            <div class="relative flex flex-1 flex-col items-center justify-center">
                <button onclick="closeZoomGallery()" class="absolute right-4 top-4 z-50 h-10 w-10 rounded-full bg-white px-2 py-0.5 text-md text-black shadow-lg transition hover:bg-black hover:text-white">✕</button>
                <div id="zoomImageWrapper" class="max-h-screen w-auto overflow-hidden transition-all duration-300">
                    <img id="zoomGalleryImage" src="{{ asset($product->images[0] ?? '') }}" alt="Zoom {{ strip_tags($product->name) }}"
                         class="object-contain transition-transform duration-300 ease-in-out" style="transform: scale(1); cursor: zoom-in;"/>
                </div>
            </div>
        </div>
    </div>
    <div id="zoomGalleryBackdrop" class="fixed inset-0 z-40 hidden bg-black bg-opacity-50 transition-opacity" onclick="closeZoomGallery()"></div>

    <script>
        function updateMainImage(el) { document.getElementById("mainImage").src = el.getAttribute("data-src"); }
        let zoomedIn = false;
        function openZoomGallery() {
            document.getElementById('zoomGalleryPanel').classList.remove('translate-x-full');
            document.getElementById('zoomGalleryBackdrop').classList.remove('hidden');
            zoomedIn = false;
            const z = document.getElementById('zoomGalleryImage'); z.style.transform = 'scale(1)'; z.style.cursor = 'zoom-in';
        }
        function closeZoomGallery() {
            document.getElementById('zoomGalleryPanel').classList.add('translate-x-full');
            document.getElementById('zoomGalleryBackdrop').classList.add('hidden');
        }
        function changeZoomGalleryImage(thumb) {
            const image = document.getElementById('zoomGalleryImage');
            image.src = thumb.getAttribute('data-large'); image.style.transform = 'scale(1)'; zoomedIn = false; image.style.cursor = 'zoom-in';
        }
        document.addEventListener('DOMContentLoaded', function () {
            const zoomImage = document.getElementById('zoomGalleryImage');
            const wrapper = document.getElementById('zoomImageWrapper');
            if (!zoomImage) return;
            zoomImage.addEventListener('click', () => {
                zoomedIn = !zoomedIn;
                if (zoomedIn) {
                    wrapper.classList.remove('max-h-screen', 'w-auto', 'overflow-hidden');
                    wrapper.classList.add('w-full', 'h-full', 'overflow-scroll');
                    zoomImage.style.transform = 'scale(2)'; zoomImage.style.cursor = 'zoom-out';
                } else {
                    wrapper.classList.remove('w-full', 'h-full', 'overflow-scroll');
                    wrapper.classList.add('max-h-screen', 'w-auto', 'overflow-hidden');
                    zoomImage.style.transform = 'scale(1)'; zoomImage.style.cursor = 'zoom-in';
                }
            });
        });
    </script>
@endsection
