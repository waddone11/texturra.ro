<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            <livewire:sidebar-account />
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="container">
                <h1 class="text-2xl font-semibold mb-6">Produse favorite</h1>

                <!-- Search Bar -->
                <div class="flex justify-between items-center mb-4">
                    <input type="text" wire:model="search" placeholder="Search favorites..." class="px-4 py-2 border rounded" />
                </div>

                <!-- Favorite Products Listing -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                    @forelse ($favorites as $favorite)
                        <div class="bg-white border border-red-500 shadow-md rounded-lg overflow-hidden">
                            <!-- Product Image -->
                            <a href="{{ route('product.show', ['slug' => $favorite->product->slug]) }}">
                                <img src="{{ $favorite->product->images[0] ?? '/storage/images/placeholder-images.webp' }}" class="w-full h-48 object-cover p-2 sm:p-3"/>
                            </a>

                            <!-- Product Details -->
                            <div class="p-2 sm:p-4 border-t border-red-500 bg-gray-50">
                                <a href="{{ route('product.show', ['slug' => $favorite->product->slug]) }}" class="text-xs font-bold">
                                    {{ Str::limit($favorite->product->name, 50) }}
                                </a>

                                <p class="text-xs text-gray-600">{{ Str::limit($favorite->product->description, 100) }}</p>

                                <!-- Favorite Button -->
                                <div class="flex justify-between items-center mt-4">
                                    <p class="text-xs font-bold text-gray-800">{{ number_format($favorite->product->price, 2) }} {{ $favorite->product->currency }}</p>
                                    <button wire:click="removeFavorite({{ $favorite->product->id }})" class="text-red-500">
                                        <i class="fa-solid fa-heart-crack"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-gray-500">No favorite products yet.</p>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $favorites->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
