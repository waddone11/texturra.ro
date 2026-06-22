{{-- Product listing — redesigned from texturra-draperii-listing-redesign package.
     FUNCTION PRESERVED: Oferte toggle, single-select attribute filters (selectedFilters.X),
     resetFilters/resetOferte, applied-filter chips, pagination ($products->links()), favorites,
     color swatches (product_color), price(). Sort ($sortBy) added (additive). Old view kept in
     product-listing-old.blade.php. Generic hero image (per-category images can replace later). --}}
<div class="font-dm text-[#1a1714]">

    {{-- Hero --}}
    <section class="pt-6">
        <div class="relative isolate min-h-[260px] overflow-hidden rounded-[18px] bg-[#47382e] md:min-h-[350px]">
            <img src="{{ asset('storage/images/homepage/listing-hero.webp') }}" alt="{{ $categoryName }}"
                 class="absolute inset-0 -z-10 h-full w-full object-cover object-[center_56%]" />
            <div class="absolute inset-0 -z-[5] bg-gradient-to-r from-[#110e0c]/95 via-[#110e0c]/55 to-transparent"></div>
            <div class="w-[min(530px,80%)] px-8 py-12 text-white md:px-14 md:py-16">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#d5b26d]">Colecția TEXTURRA</p>
                <h1 class="mt-2 font-display text-3xl font-semibold leading-tight md:text-[40px]">{{ $categoryName }}</h1>
                <div class="my-4 h-px w-12 bg-[#d5b26d]"></div>
                <p class="max-w-[390px] text-sm leading-relaxed text-white/80">
                    Țesături premium și confecție la comandă — {{ $products->total() }} produse disponibile în această categorie.
                </p>
            </div>
        </div>
    </section>

    {{-- Subcategory pills --}}
    @if($childCategories->isNotEmpty())
        <nav class="flex flex-wrap gap-2.5 py-5" aria-label="Subcategorii">
            @foreach($childCategories as $child)
                <a href="{{ route('products.category', ['slug' => $child->slug]) }}"
                   class="inline-flex min-h-[41px] items-center rounded-full border border-[#e5ddd2] bg-[#fffdfb] px-5 font-display text-sm text-[#5e554a] transition hover:border-[#b58a43] hover:bg-[#b58a43] hover:text-white">
                    {{ $child->name }}
                </a>
            @endforeach
        </nav>
    @endif

    {{-- Listing layout: sidebar + catalog --}}
    <div class="grid grid-cols-1 gap-7 pb-16 pt-1 lg:grid-cols-[262px_1fr]">

        {{-- Sidebar filters --}}
        <aside class="h-max overflow-hidden rounded-[14px] border border-[#e5ddd2] bg-[#fffdfb] lg:sticky lg:top-28">
            <div class="flex items-center justify-between border-b border-[#e5ddd2] px-4 py-4">
                <b class="text-[11px] uppercase tracking-[0.06em]">Filtrează</b>
                <button type="button" wire:click="resetFilters" class="text-[11px] text-[#8c6529] underline">Resetează toate</button>
            </div>

            {{-- Oferte (preserved toggle) --}}
            <details open class="border-b border-[#e5ddd2]">
                <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3.5 text-[13px] font-semibold marker:content-['']">
                    Oferte
                    <span class="text-[#8c6529]">+</span>
                </summary>
                <div class="px-4 pb-4">
                    <label class="flex cursor-pointer items-center gap-2 text-[13px] text-[#5f594f]">
                        <input type="checkbox" wire:model.live="selectedOferte" class="h-4 w-4 accent-[#b58a43]">
                        <span>Doar produse cu reducere</span>
                    </label>
                </div>
            </details>

            {{-- Dynamic attribute filters — single-select preserved (selectedFilters.X scalar) --}}
            @foreach ($availableFilters as $attribute => $values)
                <details class="border-b border-[#e5ddd2]" @if(!empty($selectedFilters[$attribute])) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3.5 text-[13px] font-semibold marker:content-['']">
                        {{ $attribute }}
                        <span class="text-[#8c6529]">+</span>
                    </summary>
                    <div class="space-y-1.5 px-4 pb-4">
                        @foreach ($values as $val)
                            @php $isActive = ($selectedFilters[$attribute] ?? null) === $val; @endphp
                            <button type="button"
                                    wire:click="$set('selectedFilters.{{ $attribute }}', '{{ $val }}')"
                                    class="flex w-full items-center gap-2 text-left text-[13px] {{ $isActive ? 'font-semibold text-[#8c6529]' : 'text-[#5f594f]' }}">
                                <span class="grid h-4 w-4 shrink-0 place-items-center rounded-[3px] border text-[10px] leading-none {{ $isActive ? 'border-[#b58a43] bg-[#b58a43] text-white' : 'border-[#cfc5b7]' }}">
                                    @if($isActive)✓@endif
                                </span>
                                <span>{{ $val }}</span>
                            </button>
                        @endforeach
                        @if(!empty($selectedFilters[$attribute]))
                            <button type="button" wire:click="$set('selectedFilters.{{ $attribute }}', null)"
                                    class="mt-1.5 text-[11px] text-[#8c6529] underline">Șterge {{ $attribute }}</button>
                        @endif
                    </div>
                </details>
            @endforeach
        </aside>

        {{-- Catalog --}}
        <div>
            {{-- Top bar: title + chips + sort --}}
            <div class="flex flex-col gap-4 border-b border-[#e5ddd2] pb-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <div class="flex flex-wrap items-baseline gap-3">
                        <h2 class="font-display text-3xl font-semibold leading-none">{{ $categoryName }}</h2>
                        <span class="text-xs text-[#8c8376]">{{ $products->total() }} produse</span>
                    </div>

                    @if(!empty($appliedFilters))
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            @if($selectedOferte)
                                <span class="inline-flex min-h-[27px] items-center gap-1.5 rounded-[5px] border border-[#d8cec1] bg-[#fffdfb] px-2.5 text-[11px] text-[#635b51]">
                                    Oferte <button type="button" wire:click="resetOferte" class="text-[15px] leading-none text-[#82776a]">×</button>
                                </span>
                            @endif
                            @foreach ($selectedFilters as $attr => $val)
                                @if(!empty($val))
                                    <span class="inline-flex min-h-[27px] items-center gap-1.5 rounded-[5px] border border-[#d8cec1] bg-[#fffdfb] px-2.5 text-[11px] text-[#635b51]">
                                        {{ $val }} <button type="button" wire:click="$set('selectedFilters.{{ $attr }}', null)" class="text-[15px] leading-none text-[#82776a]">×</button>
                                    </span>
                                @endif
                            @endforeach
                            <a wire:click="resetFilters" class="cursor-pointer self-center text-[11px] text-[#8c6529] underline">Resetează filtrele</a>
                        </div>
                    @endif
                </div>

                <div class="flex shrink-0 items-center gap-2.5">
                    <label for="sort" class="text-[11px] text-[#8b8173]">Sortează după</label>
                    <select id="sort" wire:model.live="sortBy"
                            class="h-9 rounded-[7px] border border-[#e5ddd2] bg-white px-3 text-xs text-[#544c42]">
                        <option value="recomandate">Recomandate</option>
                        <option value="noutati">Noutăți</option>
                        <option value="pret_asc">Preț crescător</option>
                        <option value="pret_desc">Preț descrescător</option>
                    </select>
                </div>
            </div>

            {{-- Product grid --}}
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    <article wire:key="product-{{ $product->id }}"
                             class="group relative overflow-hidden rounded-[12px] border border-[#ece5dc] bg-[#fffdfb] transition duration-200 hover:-translate-y-1 hover:shadow-[0_16px_40px_rgba(33,25,14,0.07)]">
                        <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="relative block aspect-[4/4.55] overflow-hidden bg-[#eee]">
                            <img src="{{ asset(($product->images[0]) ?? 'storage/images/placeholder-images.webp') }}"
                                 alt="{{ strip_tags($product->name) }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.045]" />
                            <span class="pointer-events-none absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-black/30 to-transparent"></span>
                        </a>

                        {{-- Favorites (preserved Livewire component) --}}
                        <div class="absolute right-2.5 top-2.5 z-[2]">
                            <livewire:favorites-button :product-id="$product->id" wire:key="fav-{{ $product->id }}" />
                        </div>

                        <div class="p-[15px]">
                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}">
                                <h3 class="min-h-[43px] font-display text-[18px] font-semibold leading-[1.1] text-[#1a1714] transition-colors hover:text-[#8c6529]">
                                    {{ strip_tags($product->name) }}
                                </h3>
                            </a>

                            @if ($product->materials->isNotEmpty())
                                <p class="mb-3 mt-2 text-[11px] text-[#776e62]">{{ $product->materials->pluck('name')->implode(' · ') }}</p>
                            @endif

                            @if (!empty($product->colors_with_css) && count($product->colors_with_css))
                                <div class="mb-3 flex items-center gap-1.5">
                                    @foreach ($product->colors_with_css->take(4) as $color)
                                        <span class="h-4 w-4 rounded-full border border-black/10" style="background-color: {{ $color['css'] }}" title="{{ $color['name'] }}"></span>
                                    @endforeach
                                    @if(count($product->colors_with_css) > 4)
                                        <span class="ml-0.5 text-[10px] text-[#7d7264]">+ {{ count($product->colors_with_css) - 4 }} nuanțe</span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-baseline justify-between gap-2">
                                <span class="text-[17px] font-bold text-[#2e2923]">
                                    de la {{ number_format($product->price(), 2) }} <small class="text-[10px] font-medium text-[#7c7165]">lei / metru</small>
                                </span>
                            </div>

                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}"
                               class="mt-3.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-[#594838] underline underline-offset-[3px]">
                                Vezi detalii <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>

                    {{-- Consultation banner inserted into the grid (after the 3rd product) --}}
                    @if($loop->iteration === 3)
                        <aside class="relative col-span-full grid min-h-[178px] grid-cols-1 overflow-hidden rounded-[13px] bg-[#f2ece2] sm:grid-cols-[1.08fr_0.92fr]">
                            <div class="relative z-[1] max-w-[360px] p-7">
                                <h3 class="font-display text-[26px] font-semibold leading-[1.06] text-[#1a1714] md:text-[28px]">Confecție la comandă,<br>creată pentru tine</h3>
                                <p class="mb-4 mt-2 text-xs leading-[1.55] text-[#70665b]">Fiecare fereastră este unică. Beneficiezi de consultanță gratuită, măsurători precise și producție impecabilă.</p>
                                <a href="tel:{{ config('app.store_owner.phone') }}" class="inline-flex min-h-[37px] items-center rounded-[6px] bg-[#b58a43] px-4 text-[10px] font-bold uppercase tracking-[0.05em] text-white transition-colors hover:bg-[#8c6529]">Programează consultație</a>
                            </div>
                            <div class="relative overflow-hidden max-sm:min-h-[195px]">
                                <img src="{{ asset('storage/images/homepage/listing-consultation.webp') }}" alt="Consultant TEXTURRA aranjând o draperie" class="h-full w-full object-cover object-[center_52%]" />
                            </div>
                        </aside>
                    @endif
                @empty
                    <p class="col-span-full py-16 text-center text-[#8c8376]">Nu am găsit produse în această categorie.</p>
                @endforelse
            </div>

            {{-- Pagination (Laravel paginator preserved) --}}
            @if($products->hasPages() || $products->total() > 0)
                <div class="flex flex-col items-center justify-between gap-4 pt-7 text-[11px] text-[#766c61] sm:flex-row">
                    <span>Afișează {{ $products->firstItem() }}–{{ $products->lastItem() }} din {{ $products->total() }} produse</span>
                    <div>{{ $products->links('vendor.pagination.texturra') }}</div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("livewire:navigated", () => {});
        // Keep clean URL on filter/sort change (preserved from previous behaviour).
        document.addEventListener("DOMContentLoaded", function () {
            window.Livewire && Livewire.on('updateBrowserHistory', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                if (d && d.url) history.replaceState({}, '', d.url);
            });
        });
    </script>
</div>
