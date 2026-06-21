<div wire:loading.class="opacity-50 pointer-events-none relative">
    <!-- Loading Spinner -->
    <div wire:loading class="absolute inset-0 flex items-center justify-center bg-gray-200 bg-opacity-75">
        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500"></div>
    </div>
    <!-- Filter Selection -->
    <div id="filter-scroll" class="flex flex-nowrap gap-2 sm:gap-6 overflow-x-auto pb-2 px-2 sm:px-0 whitespace-nowrap scroll-smooth relative custom-scrollbar">
        <!-- Oferte Filter -->
        <div class="relative shrink-0">
            <button type="button" wire:click="$toggle('selectedOferte')"
                    class="border px-6 py-1 text-xs rounded-md transition w-24 md:w-40 relative
                    {{ $selectedOferte ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300' }}">
                Oferte
                @if($selectedOferte)
                    <span class="absolute right-2 mr-1 top-1/2 transform -translate-y-1/2 text-red-500 cursor-pointer text-sm" wire:click="resetOferte">✖</span>
                @endif
            </button>
        </div>

        <!-- Size Filter -->
        @if (!empty($availableSizes))
            <div class="relative shrink-0 w-24 md:w-40">
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
            <div class="relative shrink-0 w-24 md:w-40">
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
            <div class="relative shrink-0 w-24 md:w-40">
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
        <button type="button" wire:click="resetFilters" class="hidden md:block border bg-black text-white text-xs font-bold px-4 py-1 rounded-md shrink-0">
            Reseteaza filtrele
        </button>
    </div>

    <!-- Scroll Indicator (Hand with Arrows) -->
    <div id="scroll-hint" class="relative left-2 top-4 right-0 top-1/2 -translate-y-1/2 flex justify-left md:hidden pointer-events-none transition-opacity duration-500 opacity-100">
        <img src="{{ asset('storage/images/swipe.svg') }}" alt="Swipe left or right" class="w-8 h-8 opacity-80 animate-pulse">
    </div>

    <!-- Reset Filters Button -->
    <a type="button" wire:click="resetFilters" class="block md:hidden text-black underline text-xs font-bold ml-3 cursor-pointer">
        Reseteaza filtrele
    </a>

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
                console.log("Filter URL update received:", data);

                if (Array.isArray(data) && data.length > 0 && data[0].url) {
                    history.replaceState({}, '', data[0].url);
                    console.log("URL successfully updated:", data[0].url);
                } else {
                    console.error("updateBrowserHistory event received but no URL provided:", data);
                }
            });
        });
    </script>
</div>
