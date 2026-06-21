<div>
    <h2 class="text-2xl font-bold mb-4 text-left mt-2 md:mt-12">
        {{ $categoryName }}
        <span class="textSuperSmall inline-block">
            {{ $products->total() }} produse
        </span>

        @if (!empty($appliedFilters))
            <div class="mt-2 text-xs text-black hidden md:block">
                <strong>Filtre aplicate:</strong>
                @foreach ($appliedFilters as $filter)
                    <div class="bg-gray-100 px-2 py-1 text-gray-400 border border-gray-200 h-4 rounded-md textSuperSmall mr-2 mt-1 w-24 inline" style="margin-top: 5px;">{{ $filter }}</div>
                @endforeach
            </div>
        @endif
    </h2>

    @if($childCategories->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-sm uppercase font-bold text-gray-600 mb-1">Subcategorii:</h2>
            <ul class="flex flex-wrap gap-2">
                @foreach($childCategories as $child)
                    <li>
                        <a href="{{ route('products.category', ['slug' => $child->slug]) }}"
                           class="inline-block border border-black text-black text-xs px-3 py-1 rounded">
                            {{ $child->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Filter Section -->
    <aside class="w-full rounded-md mb-8 md:mb-12">
        <div>
            <!-- Filter Selection -->
            <div id="filter-scroll" class="flex flex-nowrap gap-2 sm:gap-6 overflow-x-auto pb-2 p-0 sm:px-0 whitespace-nowrap scroll-smooth relative custom-scrollbar">
                <!-- Oferte Filter (kept static) -->
                <div class="relative shrink-0">
                    <button type="button" wire:click="$toggle('selectedOferte')"
                            class="border px-6 py-1 text-xs rounded-md transition w-40 relative
                        {{ $selectedOferte ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-400' }}">
                        Oferte
                        @if($selectedOferte)
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-green-500 cursor-pointer text-sm"
                                  wire:click="resetOferte">✖</span>
                        @endif
                    </button>
                </div>

                <!-- Dynamic Filters -->
                @foreach ($availableFilters as $attribute => $values)
                    <div class="relative shrink-0 w-40">
                        <select wire:model.live="selectedFilters.{{ $attribute }}"
                                class="border px-3 py-1 text-xs rounded-md transition w-full
                            {{ !empty($selectedFilters[$attribute]) ? 'bg-black text-white font-bold border-black' : 'bg-white text-black  font-bold border-gray-500' }}">
                            <option value="">{{ $attribute }}</option>
                            @foreach ($values as $val)
                                <option value="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                        @if (!empty($selectedFilters[$attribute]))
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-green-500 cursor-pointer text-sm"
                                  wire:click="$set('selectedFilters.{{ $attribute }}', null)">✖</span>
                        @endif
                    </div>
                @endforeach

                <!-- Reset Filters Button (for desktop) -->
                <button type="button" wire:click="resetFilters" class="hidden md:block border border-black bg-gray-100 text-black text-xs font-bold px-4 py-1 rounded-md shrink-0">
                    Reseteaza filtrele
                </button>
            </div>

            <div class="flex items-center justify-between mt-2 block">
                <!-- Reset Filters Button - aligned left -->
                <a type="button" wire:click="resetFilters" class="text-black underline text-xs font-bold cursor-pointer">
                    Resetează filtrele
                </a>

                <!-- Scroll Indicator - aligned right -->
                <div id="scroll-hint" class="shrink-0">
                    <img src="{{ asset('storage/images/swipe.svg') }}" alt="Swipe left or right" class="w-8 h-8 opacity-80 animate-pulse">
                </div>
            </div>

            <style>
                /* Hide scrollbar */
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
                    let filterScroll = document.getElementById("filter-scroll");
                    let scrollHint = document.getElementById("scroll-hint");

                    if (filterScroll && scrollHint) {
                        filterScroll.addEventListener("scroll", function () {
                            if (this.scrollLeft > 10) {
                                scrollHint.style.opacity = "0"; // Hide hint when scrolling starts
                            } else {
                                scrollHint.style.opacity = "1"; // Show hint if scrolled back
                            }
                        });
                    }

                    Livewire.on('updateBrowserHistory', (data) => {
                        if (data.url) {
                            history.replaceState({}, '', data.url);
                            console.log("URL successfully updated:", data.url);
                        } else {
                            console.error("updateBrowserHistory event received but no URL provided:", data);
                        }
                    });
                });
            </script>
        </div>
    </aside>

    <main class="w-full pl-0">
        <div class="containe mb-12">
            <!-- Product Listing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mt-8 mb-8">
                @forelse ($products as $product)
                    <!-- PRODUCT CARD -->
                    <div wire:key="product-{{ $product->id }}" class="">
                        <div class="bg-white overflow-hidden">
                            <!-- Product Image -->
                            <div class="overflow-hidden">
                                <div class="w-full relative">
                                    <livewire:favorites-button :product-id="$product->id" wire:key="favorites-{{ $product->id }}" />
                                    <div id="swiper-{{ $product->id }}" class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($product->detail_images ?? $product->images as $image)
                                                <div class="swiper-slide shadow-lg">
                                                    <a href="{{ route('product.show', ['slug' => $product->slug]) }}">
                                                        <img src="{{ asset($image) }}"
                                                             alt="{{ $product->name }}"
                                                             class="w-full h-auto object-cover p-0 rounded-2xl bg-white" />
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            new Swiper('#swiper-{{ $product->id }}', {
                                                slidesPerView: 1,
                                                spaceBetween: 10,
                                                pagination: {
                                                    el: '.swiper-pagination',
                                                    clickable: true,
                                                },
                                            });
                                        });
                                    </script>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-2">
                                <a href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                   class="text-sm uppercase hover:underline leading-5 font-bold line-clamp-3 h-16">
                                    {{ strip_tags($product->name) }}
                                </a>
                                <div class="text-xs mt-2">
                                        <!-- Display Material and Color (from clean pivots) -->
                                    <div class="text-xs mt-4 space-y-1">
                                        @if ($product->materials->isNotEmpty())
                                            <div>
                                                <span class="font-semibold text-gray-700">Material:</span>
                                                {{ $product->materials->pluck('name')->implode(', ') }}
                                            </div>
                                        @endif

                                        @if (!empty($product->colors_with_css))
                                            <div class="flex flex-wrap items-center text-xs text-gray-700">
                                                <span class="font-semibold mr-2">Culoare:</span>
                                                @foreach ($product->colors_with_css as $color)
                                                    <span class="flex items-center mr-4 mb-2 pt-2">
                                                                        <span class="w-5 h-5 rounded-full border border-gray-300 mr-1"
                                                                              style="background-color: {{ $color['css'] }}"></span>
                                                                        <span>{{ $color['name'] }}</span>
                                                                    </span>
                                                @endforeach
                                            </div>

                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <p class="text-xs sm:text-lg font-extrabold text-gray-800">
                                        {{ number_format($product->price(), 2) }} lei / m <br/>
                                    </p>
                                    <x-simple-link href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                                   class="text-xs text-right simpleLink font-extrabold">
                                        Vezi produsul
                                    </x-simple-link>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">No products found in this category.</p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </main>
</div>
