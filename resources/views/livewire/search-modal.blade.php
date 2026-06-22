<div x-data="{ open: @entangle('modalOpen') }"
     x-show="open"
     x-cloak
     @open-search-modal.window="$wire.set('modalOpen', true)"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Search Products</h2>
            <button @click="open = false" wire:click="$set('modalOpen', false)" class="text-gray-500 hover:text-black text-2xl font-bold">
                &times;
            </button>
        </div>

        <!-- Search Input -->
        <input
            type="text"
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
            placeholder="Search products..."
            wire:model.live.debounce.300ms="searchQuery"
            @input="$wire.set('searchQuery', $event.target.value)"
        />

        <!-- Products List -->
        <div class="mt-4 max-h-96 overflow-y-auto border-t border-gray-300 pt-4">
            @if (strlen($searchQuery) < 3)
                <p class="text-gray-500 mt-4">Please type at least 3 characters to search.</p>
            @elseif (count($results) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach ($results as $result)
                        <li class="py-4 flex items-center space-x-4">
                            <!-- Product Image -->
                            <div class="shrink-0">
                                <img src="{{ asset($result->images[0] ?? '/storage/images/placeholder-images.webp') }}"
                                     alt="{{ $result->name }}"
                                     class="h-16 w-16 rounded object-cover border border-gray-200">
                            </div>
                            <!-- Product Details -->
                            <div>
                                <a href="{{ route('product.show', $result->id) }}" class="text-xs font-semibold ourColor2 hover:underline">
                                    {{ $result->name }}
                                </a>
                                <p class="text-gray-500 text-xs mt-1">
                                    {!!   Str::limit($result->description_plain, 150) !!}
                                </p>
                                <p class="text-black font-bold mt-1">Price: {{ number_format($result->price, 2) }} {{ $result->currency ?? 'RON' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 mt-4">No results found.</p>
            @endif
        </div>
    </div>
</div>
