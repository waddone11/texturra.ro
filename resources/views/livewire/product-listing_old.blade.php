<div>
    <h2 class="text-2xl font-bold mb-4 md:mb-8 text-left mt-2 md:mt-12">
        {{ $categoryName }}
        <span class="bg-gray-100 textSuperSmall text-gray-500 rounded-xl shadow-xl px-2 py-1">
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

    <!-- Filter Section -->
    <aside class="w-full rounded-md mb-8 md:mb-12">
        <div>
            <!-- Filter Selection -->
            <div id="filter-scroll" class="flex flex-nowrap gap-2 sm:gap-6 overflow-x-auto pb-2 p-0 sm:px-0 whitespace-nowrap scroll-smooth relative custom-scrollbar">
                <!-- Oferte Filter -->
                <div class="relative flex-shrink-0">
                    <button type="button" wire:click="$toggle('selectedOferte')"
                            class="border px-6 py-1 text-xs rounded-md transition w-40 relative
                        {{ $selectedOferte ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300' }}">
                        Oferte
                        @if($selectedOferte)
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-red-500 cursor-pointer text-sm" wire:click="resetOferte">✖</span>
                        @endif
                    </button>
                </div>

                <!-- Size Filter -->
                @if (!empty($availableSizes))
                    <div class="relative flex-shrink-0 w-40">
                        <select wire:model.live="selectedSize"
                                class="border px-3 py-1 text-xs rounded-md transition w-full
                                {{ $selectedSize ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300' }}">
                            <option value="">Mărime</option>
                            @foreach ($availableSizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                        @if($selectedSize)
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-red-500 cursor-pointer text-sm"
                                  wire:click="$set('selectedSize', null)">✖</span>
                        @endif
                    </div>
                @endif


                <!-- Color Filter -->
                @if (!empty($availableColors))
                    <div class="relative flex-shrink-0 w-40">
                        <select wire:model.live="selectedColor"
                                class="border px-3 py-1 text-xs rounded-md transition w-full
                                {{ $selectedColor ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300' }}">
                            <option value="">Culoare</option>
                            @foreach ($availableColors as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                        @if($selectedColor)
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-red-500 cursor-pointer text-sm"
                                  wire:click="$set('selectedColor', null)">✖</span>
                        @endif
                    </div>
                @endif

                <!-- Material Filter -->
                @if (!empty($availableMaterials))
                    <div class="relative flex-shrink-0 w-40">
                        <select wire:model.live="selectedMaterial"
                                class="border px-3 py-1 text-xs rounded-md transition w-full
                                {{ $selectedMaterial ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300' }}">
                            <option value="">Material</option>
                            @foreach ($availableMaterials as $material)
                                <option value="{{ $material }}">{{ $material }}</option>
                            @endforeach
                        </select>
                        @if($selectedMaterial)
                            <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-red-500 cursor-pointer text-sm"
                                  wire:click="$set('selectedMaterial', null)">✖</span>
                        @endif
                    </div>
                @endif

                <!-- Reset Filters Button -->
                <button type="button" wire:click="resetFilters" class="hidden md:block border border-black bg-gray-100 text-black text-xs font-bold px-4 py-1 rounded-md flex-shrink-0">
                    Reseteaza filtrele
                </button>
            </div>

            <div class="flex items-center justify-between mt-2 block md:hidden">
                <!-- Reset Filters Button - aliniat la stânga -->
                <a type="button" wire:click="resetFilters" class="text-black underline text-xs font-bold md:ml-3 cursor-pointer">
                    Resetează filtrele
                </a>

                <!-- Scroll Indicator - aliniat la dreapta -->
                <div id="scroll-hint" class="flex-shrink-0">
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
                        // console.log("Debug: Filter Params:", data[0].params);
                        // console.log("Debug: New URL:", data[0].url);

                        if (data[0].url) {
                            history.replaceState({}, '', data[0].url);
                            console.log("URL successfully updated:", data[0].url);
                        } else {
                            console.error("updateBrowserHistory event received but no URL provided:", data);
                        }
                    });

                });
            </script>
        </div>
    </aside>

    <main class="w-full pl-0">
        <div class="container">

            <!-- Product Listing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mt-8">
                @forelse ($products as $product)
                    <div wire:key="product-{{ $product->id }}">
                        <div class="bg-white border border-green-500 shadow-md rounded-md overflow-hidden">
                        <!-- Product Image -->
                        <div class="w-full">
                            <livewire:favorites-button :product-id="$product->id" wire:key="favorites-{{ $product->id }}" />
                            <a href="{{ route('product.show', ['slug' => $product->slug ?? $product->slug]) }}" class="hover:underline">
                                <img
                                    src="{{ asset($product->images[0] ?? 'storage/images/placeholder_product.webp') }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-48 object-cover p-0 rounded-t rounded-b-none border-b border-green-500 bg-gray-100 bg-f4f4f5"
                                />
                            </a>

                            @php
                                $originalPrice = $product->price ?? 0;
                                $discountedPrice = $product->price();
                                $discountPercentage = $originalPrice > 0 ? round((($originalPrice - $discountedPrice) / $originalPrice) * 100) : 0;
                            @endphp

                            @if($originalPrice > $discountedPrice)
                                <div class="w-full h-6 py-1 bg-black px-2 textSuperSmall font-extrabold text-white flex items-center space-x-2">
                                    <span>Discount:</span>
                                    <span class="text-red-600">-{{ $discountPercentage }}%</span>
                                    <span class="line-through opacity-60">
                                {{ number_format($originalPrice, 2) }} lei</span>
                                    <span class="text-white">Acum: {{ number_format($discountedPrice, 2) }} lei</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="p-2 sm:p-4 bg-gray-50">
                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="text-sm hover:underline leading-3 heightLink font-bold">
                                {{ Str::limit(strip_tags($product->name), 100) }}
                            </a>
                            <!-- Additional Details -->
                            <div class="text-xs mt-2">
                                <p>Brand: <span class="font-semibold">{{ $product->brand_name ?? 'N/A' }}</span></p>
                                <p class="relative -left-4 {{ $product->stock() > 0 ? 'bg-green-500' : 'bg-red-600' }}  w-1/2 text-white pl-4 rounded-r-xl">Stoc:
                                    <span class="font-semibold {{ $product->stock() > 0 ? 'text-white' : 'text-white' }}">
                                {{ $product->stock()  > 0 ? 'Disponibil' : 'Indisponibil' }}
                            </span>
                                </p>
                                <p class="mt-2">Info:</p>
                                @php
                                    $sizes = $product->variations->flatMap(function ($variation) {
                                        return $variation->attributeValues->where('attribute.name', 'Unitate de măsură')->pluck('value');
                                    })->unique()->sort()->values();

                                    $selectedSizes = collect($filters['size'] ?? []);
                                @endphp
                                <div class="mt-2 h-auto md:h-12">
                                    @if ($sizes->isNotEmpty())
                                        @foreach ($sizes as $size)
                                            <span class="px-2 py-0.5 textSuperSmall font-semibold rounded-full mr-2 mb-2 inline-flex items-center whitespace-nowrap shadow-md
                                        {{ $selectedSizes->contains($size) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                        {{ $size }}
                                    </span>
                                        @endforeach
                                    @else
                                        <span class="bg-gray-300 px-2 text-xs text-white rounded">Nu există mărimi disponibile</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Price and Button -->
                            <div class="flex items-center justify-between mt-4">
                                <p class="text-xs sm:text-lg font-extrabold text-gray-800">
                                    {{ number_format($product->price(), 2) }} {{ $product->currency }} lei <br/>
                                </p>
                                <x-primary-button-border
                                    href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                    class="text-lg md:text-xs text-right simpleLink font-extrabold">
                                    Vezi produsul
                                </x-primary-button-border>
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
