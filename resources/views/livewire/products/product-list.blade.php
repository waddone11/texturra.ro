<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            <livewire:sidebar-stats />
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="pr-3">
                <h1 class="text-2xl font-semibold mb-6">Produse</h1>

                <!-- Add Product Component -->
                <div class="items-center mb-4">
                    <div x-data="{ open: false }" class="w-full mb-4">
                        <x-primary-link @click="open = !open" class="cursor-pointer">
                            <x-icons.plus class="h-5 w-5 mr-2" /> Adaugă produs
                        </x-primary-link>
                        <div x-show="open" x-cloak class="mt-4 border p-4 rounded-md shadow-md">
                            <livewire:products.product-create />
                        </div>
                    </div>
                </div>

                <!-- Filter Component -->
                <div class="items-center mb-4">
                    <livewire:products.product-filter />
                </div>

                <!-- Flash Notification -->
                <div id="flash-message" class="hidden bg-green-500 text-white text-sm p-2 rounded mb-4"></div>

                <!-- Products Table -->
                <div class="w-full overflow-x-auto border">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left text-xs md:text-md">Images</th>
                            <th class="p-2 text-left text-xs md:text-md">ID</th>
                            <th class="p-2 text-left text-xs md:text-md">Nume</th>
                            <th class="p-2 text-left text-xs md:text-md">EAN</th>
                            <th class="p-2 text-left text-xs md:text-md">Descriere</th>
                            <th class="p-2 text-left text-xs md:text-md">Categorie</th>
                            <th class="p-2 text-left text-xs md:text-md">Pret</th>
                            <th class="p-2 text-left text-xs md:text-md">Stoc</th>
                            <th class="p-2 text-right text-xs md:text-md">Actiuni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr class="@if($loop->even) bg-gray-50 @endif border-t">
                                <!-- Image Column -->
                                <td class="px-4 py-2">
                                    @if ($product->images && count($product->images) > 0)
                                        <img src="{{ asset($product->images[0]) }}"
                                             class="h-12 w-12 object-cover border border-gray-500 rounded shadow-xl">
                                    @endif
                                </td>
                                <!-- ID Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    <span class="bg-green-500 mr-2 p-1 rounded text-white">ID:{{ $product->id }}</span>
                                    <span class="block mt-1">
                                        @if($product->category)
                                            {{ $product->category->name }} (ID: {{ $product->category->id }})
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                                <!-- Name Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    <a href="{{ route('product.show', ['slug' => $product->slug]) }}" target="_blank"
                                       class="underline pt-1 leading-4">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <!-- EAN Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{ $product->ean ?? 'N/A' }}
                                </td>
                                <!-- Description Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {!! $product->description ? 'DA' : 'N/A' !!}
                                </td>
                                <!-- Category Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{ $product->category->name ?? 'N/A' }}
                                </td>
                                <!-- Price Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{ $product->price }} RON
                                </td>
                                <!-- Stock Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{ $product->general_stock }}
                                </td>
                                <!-- Actions Column -->
                                <td class="px-2 py-2 text-right text-xs md:text-md">
                                    @if($product->status == 1)
                                        <div x-data="{ openEdit: false }">
                                            <button @click="openEdit = true" class="text-blue-500">
                                                Edit
                                            </button>
                                            <button wire:click="deleteProduct({{ $product->id }})" class="text-red-500 ml-2">
                                                Sterge
                                            </button>
                                            <!-- Inline Modal for Editing -->
                                            <div x-show="openEdit" x-cloak class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50 z-50">
                                                <div class="bg-white p-6 rounded shadow-lg relative">
                                                    <button @click="openEdit = false" class="absolute top-2 right-2 text-red-500 text-2xl font-bold">
                                                        &times;
                                                    </button>
                                                    <div class="p-4" style="max-height: 80vh; overflow-y: auto;">
                                                        <livewire:products.product-edit :productId="$product->id" wire:key="product-edit-{{ $product->id }}" />
                                                    </div>
                                                    <button @click="open = false" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <button wire:click="restoreProduct({{ $product->id }})" class="text-green-500 ml-2">
                                            Reactiveaza
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener("livewire:load", () => {
        Livewire.on("flashMessage", (data) => {
            const flashMessage = document.getElementById("flash-message");
            flashMessage.textContent = data.message;
            flashMessage.classList.remove("hidden");
            setTimeout(() => {
                flashMessage.classList.add("hidden");
            }, 30000);
        });
    });
</script>
