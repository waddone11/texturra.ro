<div class="max-w-9xl mx-auto py-12 px-3 md:px-8">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/12 p-3 md:p-0">
            <livewire:sidebar-stats />
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-11/12 pl-3">
            <div class="pr-3">
                <h1 class="text-2xl font-semibold mb-6">Produse</h1>

                <!-- Navigation Bar with Search, Filter, and Add Button -->
                <div class="items-center mb-4">
                    <!-- Collapsible Add Product Section -->
                    <div x-data="attributeManager($wire)" x-init="init()" class="w-full mb-4">
                        <x-primary-link
                            @click="open = !open"
                            class="cursor-pointer"
                        >
                            <x-icons.plus class="h-5 w-5 mr-2" /> Adauga produs
                        </x-primary-link>

                        <div x-show="open" x-cloak class="mt-4 border p-4 rounded-md shadow-md">
                            <h2 class="text-lg font-semibold mb-4">Adaugă un produs nou</h2>
                            <!-- Product Form -->
                            <div class="flex flex-wrap md:flex-nowrap gap-6">
                                <div class="w-full md:w-9/12 border bg-gray-100 p-4 shadow-xl rounded">
                                    <form wire:submit.prevent="createProduct" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Titlu -->
                                        <div class="mb-4">
                                            <label class="block text-gray-700 font-bold">Titlu</label>
                                            <input type="text" name="name" class="w-full px-4 py-2 border rounded" value="{{ old('name') }}" wire:model="name" />
                                            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Descriere -->
                                        <div class="mb-4" wire:ignore>
                                            <label class="block text-gray-700 font-bold">Descriere</label>
                                            <div id="description-editor-add" class="w-full px-4 py-2 border border-black rounded-b bg-white"></div>
                                            <input type="hidden" id="description-input-add" wire:model.defer="description">
                                        </div>

                                        <!-- Preț -->
                                        <div class="mb-4">
                                            <label class="block text-gray-700 font-bold">Preț</label>
                                            <input type="number" name="price" step="0.01" class="w-full px-4 py-2 border rounded" value="{{ old('price') }}" wire:model="price" />
                                            @error('price') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Categorie -->
                                        <div x-data="{
                                            open: false,
                                            selectedCategory: @entangle('selectedCategoryName'),
                                            categoryId: @entangle('category_id'),
                                            setSelected(categoryName, categoryId) {
                                                this.selectedCategory = categoryName;
                                                this.categoryId = categoryId; // Updates the Livewire property
                                                this.open = false;
                                            }
                                        }" class="relative mt-2 mb-4">
                                            <label class="block text-gray-700 font-bold">Categorie</label>
                                            <button type="button" @click="open = !open" class="relative w-full cursor-default border border-black rounded bg-white py-2 pl-3 pr-10 text-left">
                                                <span class="block truncate" x-text="selectedCategory || 'Selectează o categorie'">Selectează o categorie</span>
                                                <span class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                    <x-icons.chevron-down class="h-5 w-5 text-gray-400" />
                                                </span>
                                            </button>

                                            <ul x-show="open" @click.away="open = false" class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                                                <li class="cursor-pointer py-2 px-4" @click="setSelected('Niciuna', null)">Niciuna</li>
                                                @foreach($categories as $parent)
                                                    <li class="cursor-pointer py-2 px-4" @click="setSelected('{{ $parent->name }}', {{ $parent->id }})">{{ $parent->name }}</li>
                                                    @foreach($parent->children as $child)
                                                        @include('components.category-option', ['child' => $child, 'depth' => 1])
                                                    @endforeach
                                                @endforeach
                                            </ul>

                                            <input type="hidden" wire:model="category_id" id="category_id" value="{{ old('category_id') }}">
                                            @error('category_id') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- File Input with Preview -->
                                        <div class="mb-4">
                                            <label class="block text-gray-700 font-bold">Imagini</label>
                                            <input
                                                wire:model="newImages"
                                                type="file"
                                                name="images[]"
                                                multiple
                                                class="w-full px-4 py-2 border border-black rounded"
                                                @change="previewImages($event)"
                                            />
                                            @error('images.*') <span class="text-red-500">{{ $message }}</span> @enderror

                                            <!-- Preview Uploaded Images -->
                                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-4">
                                                <template x-for="(image, index) in form.images" :key="index">
                                                    <div class="relative group">
                                                        <img
                                                            :src="image"
                                                            alt="Image Preview"
                                                            class="w-full h-24 object-cover border rounded-md shadow-md"
                                                        />
                                                        <x-primary-link
                                                            type="button"
                                                            @click="removeImage(index)"
                                                            class="text-red-500 mt-4 cursor-pointer">
                                                            Șterge
                                                        </x-primary-link>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="bg-white p-3 border border-black rounded mt-8 mb-4">
                                            <label class="block text-gray-700 font-bold mb-2">Variante de produs</label>

                                            <!-- Add New Variations -->
                                            <template x-for="(variant, index) in variants" :key="index">
                                                <div class="flex flex-wrap md:flex-nowrap items-center gap-4 mb-4 p-4 border border-gray-300 rounded bg-white">
                                                    <!-- Unitate de măsură -->
                                                    <div class="w-full md:w-1/6">
                                                        <label class="block text-gray-700 font-bold text-xs">Unitate de măsură</label>
                                                        <select x-model="variant.unitate" @change="$wire.set(`newVariations.${index}.unitate`, variant.unitate)" class="w-full px-4 py-2 border rounded text-xs">
                                                            <option value="">Alege Unitate de măsură</option>
                                                            <template x-for="value in unitate" :key="value.id">
                                                                <option :value="value.id" x-text="value.value"></option>
                                                            </template>
                                                        </select>
                                                    </div>

                                                    <!-- Cantitate per ambalaj -->
                                                    <div class="w-full md:w-1/6">
                                                        <label class="block text-gray-700 font-bold text-xs">Cantitate per ambalaj</label>
                                                        <select x-model="variant.ambalaj" @change="$wire.set(`newVariations.${index}.ambalaj`, variant.ambalaj)" class="w-full px-4 py-2 border rounded text-xs">
                                                            <option value="">Alege Cantitate per ambalaj</option>
                                                            <template x-for="value in ambalaje" :key="value.id">
                                                                <option :value="value.id" x-text="value.value"></option>
                                                            </template>
                                                        </select>
                                                    </div>

                                                    <!-- Imagine variantă -->
                                                    <div class="w-full md:w-1/6">
                                                        <label class="block text-gray-700 font-bold text-xs">Imagine variantă</label>
                                                        <!-- Remove x-model from the file input -->
                                                        <input type="file" @change="handleVariantImage($event, index)" class="w-full px-2 py-1 border rounded text-xs" />
                                                        <!-- Show a preview if available -->
                                                        <template x-if="variant.imagePreview">
                                                            <img :src="variant.imagePreview" class="mt-2 h-16 w-16 object-cover border rounded" />
                                                        </template>
                                                    </div>

                                                    <!-- Stoc -->
                                                    <div class="w-full md:w-1/6">
                                                        <label class="block text-gray-700 font-bold text-xs">Stoc</label>
                                                        <input type="number" x-model="variant.stock" @input="$wire.set(`newVariations.${index}.stock`, variant.stock)" class="w-full px-4 py-2 border rounded text-xs" />
                                                    </div>

                                                    <!-- Preț -->
                                                    <div class="w-full md:w-1/6">
                                                        <label class="block text-gray-700 font-bold text-xs">Preț</label>
                                                        <input type="number" step="0.01" x-model="variant.price" @input="$wire.set(`newVariations.${index}.price`, variant.price)" class="w-full px-4 py-2 border rounded text-xs" />
                                                    </div>

                                                    <!-- Delete Variant Button -->
                                                    <div class="w-full md:w-1/6">
                                                        <x-primary-link type="button" class="text-red-500 mt-4 cursor-pointer" @click="removeVariant(index)">
                                                            Șterge
                                                        </x-primary-link>
                                                    </div>
                                                </div>
                                            </template>

                                            <x-primary-link type="button" @click="addVariant()" class="mt-4 px-4 py-2 text-black rounded">
                                                + Adaugă variantă
                                            </x-primary-link>

                                        </div>

                                        <!-- Submit -->
                                        <div class="flex justify-end mt-4">
                                            <x-primary-button type="button" @click="$wire.productVariants = variants; $wire.createProduct()">Salvează</x-primary-button>
                                            <a href="{{ route('admin.products') }}" class="btn btn-secondary ml-4 underline">Anulează</a>
                                        </div>
                                    </form>
                                </div>

                                <!-- Preview Section -->
                                <div class="w-full md:w-3/12 border bg-gray-100 p-4 shadow-xl rounded">
                                    <h3 class="text-lg font-semibold mb-4">Previzualizare Produs</h3>
                                    <div class="bg-white border rounded-md overflow-hidden">
                                        <!-- Product Image -->
                                        <div class="w-full">
                                            <template x-if="form.images.length > 0">
                                                <img
                                                    :src="form.images[0]"
                                                    alt="Previzualizare"
                                                    class="w-full h-96 object-cover"
                                                />
                                            </template>
                                            <template x-if="form.images.length === 0">
                                                <img
                                                    src="/storage/images/placeholder_product.webp"
                                                    alt="Previzualizare"
                                                    class="w-full h-96 object-cover"
                                                />
                                            </template>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="p-4 border-t">
                                            <h4 class="font-bold text-md" x-text="$wire.name || 'Titlu produs'"></h4>
                                            <p class="text-sm text-gray-500" x-text="stripHtml($wire.description) || 'Descriere produs...'"></p>
                                            <p class="font-bold text-md mt-2" x-text="$wire.price ? $wire.price + ' RON' : '0.00 RON'"></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="flex flex-wrap gap-4">
                            <!-- Search by ID -->
                            <div>
                                <label class="block text-gray-700 text-xs font-bold">Caută după ID</label>
                                <input type="number" wire:model.defer="searchById" class="px-4 py-2 border rounded text-sm w-full" placeholder="ID produs">
                                <button wire:click="applyFilters" class="text-black px-3 py-2 border border-black rounded text-xs mt-2 w-full">
                                    Caută
                                </button>
                            </div>

                            <!-- Search by Name -->
                            <div>
                                <label class="block text-gray-700 text-xs font-bold">Caută după nume</label>
                                <input type="text" wire:model.defer="searchByName" class="px-4 py-2 border rounded text-sm w-full" placeholder="Nume produs">
                                <button wire:click="applyFilters" class="text-black px-3 py-2 border border-black rounded text-xs mt-2 w-full">
                                    Caută
                                </button>
                            </div>

                            <!-- Filter by Category -->
                            <div x-data="{ open: false }" class="relative w-48">
                                <label class="block text-gray-700 text-xs font-bold">Categorie</label>
                                <button @click="open = !open" class="relative w-full border rounded bg-white py-2 px-4 text-left text-sm">
                                    <span class="block truncate" x-text="$wire.selectedCategoryName || 'Alege categorie'"></span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <x-icons.chevron-down class="h-4 w-4 text-gray-400" />
                                    </span>
                                </button>

                                <ul x-show="open" @click.away="open = false" class="absolute z-10 mt-1 w-full max-h-56 overflow-auto rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 text-sm">
                                    <li class="cursor-pointer py-2 px-4 hover:bg-gray-100 font-bold" wire:click="resetCategory">
                                        🔄 Resetare Filtru
                                    </li>
                                    <li class="cursor-pointer py-2 px-4 hover:bg-gray-100" wire:click="filterByCategory(null)">
                                        Toate categoriile
                                    </li>
                                    @foreach($allCategories as $category)
                                        <li class="cursor-pointer py-2 px-4 hover:bg-gray-100" wire:click="filterByCategory({{ $category->id }})">
                                            {{ $category->name }}
                                        </li>
                                        @if ($category->children->isNotEmpty())
                                            @foreach($category->children as $child)
                                                <li class="cursor-pointer py-2 px-4 hover:bg-gray-100 pl-6" wire:click="filterByCategory({{ $child->id }})">
                                                    ├── {{ $child->name }}
                                                </li>
                                                @foreach($child->children as $subchild)
                                                    <li class="cursor-pointer py-2 px-4 hover:bg-gray-100 pl-12" wire:click="filterByCategory({{ $subchild->id }})">
                                                        ├──── {{ $subchild->name }}
                                                    </li>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    @endforeach
                                </ul>

                                <!-- Apply Filters Button -->
                                <button wire:click="applyFilters" class="text-black px-3 py-2 border border-black rounded text-xs mt-2 w-full">
                                    Aplică Filtru
                                </button>
                            </div>

                        </div>
                    </div>


                </div>

                <!-- Products Table -->
                <div class="w-full overflow-x-auto border" x-data="attributeManager($wire)">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left text-xs md:text-md">Images</th>
                            <th class="p-2 text-left text-xs md:text-md">Nume</th>
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
                                    <div class="flex space-x-2">
                                        @if (!empty($product->images) && is_array($product->images) && isset($product->images[0]))
                                            <img src="{{ asset($product->images[0]) }}" class="h-12 w-12 md:h-12 md:w-12 object-cover border border-gray-500 rounded shadow-xl" />
                                        @endif
                                    </div>
                                </td>
                                <!-- Name Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{$product->name}} - {{ $product->id }}
                                </td>
                                <!-- Description Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{$product->description_plain != '' ? 'avem' : 'N/A'}}
                                </td>
                                <!-- Name Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{$product->category->name ?? 'N/A'}}
                                </td>
                                <!-- Price Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{$product->price}} RON
                                </td>
                                <!-- Stock Column -->
                                <td class="px-2 py-2 text-xs md:text-md">
                                    {{ $product->general_stock }}
                                </td>
                                <!-- Actions Column -->
                                <td class="px-2 py-2 text-right text-xs md:text-md">
                                    <button
                                        @click="$wire.call('editProduct', {{ $product->id }}).then(() => toggleEdit({{ $product->id }}))"
                                        class="btn btn-secondary text-blue-600"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        wire:click="deleteProduct({{ $product->id }})"
                                        class="btn btn-secondary text-red-600"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <!-- Inline Edit Form -->
                            <tr x-show="isEditing({{ $product->id }})" x-cloak>
                                <td colspan="5" class="px-4 py-2 border-t">
                                    <div class="flex flex-wrap md:flex-nowrap gap-6">
                                        <div class="w-full md:w-9/12 border bg-gray-100 p-4 shadow-xl rounded">
                                            <form wire:submit.prevent="updateProduct({{ $product->id }})" enctype="multipart/form-data">
                                                @csrf
                                                <!-- Form Fields -->
                                                <div class="grid grid-cols-1 gap-4">
                                                    <!-- Name -->
                                                    <div class="w-full">
                                                        <label class="block text-gray-700">Titlu</label>
                                                        <input
                                                            type="text"
                                                            class="w-full border rounded px-4 py-2"
                                                            wire:model="name"
                                                        />
                                                        @error('name')
                                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <!-- Description -->
                                                    <div class="edit-products-table">
                                                        <label class="block text-gray-700">Descriere</label>
                                                        <div id="description-editor-{{ $product->id }}"
                                                             class="description-editor-edit w-full px-4 py-2 border border-black rounded-b bg-white h-[150px] overflow"
                                                             data-livewire-property="descriptions.{{ $product->id }}">
                                                            {!! $product->description ?? '' !!}
                                                        </div>
                                                        <input type="hidden" wire:model="description" value="{{ $product->description }}">
                                                    </div>

                                                    <!-- Price -->
                                                    <div>
                                                        <label class="block text-gray-700">Preț</label>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            class="w-full border rounded px-4 py-2"
                                                            wire:model="price"
                                                        />
                                                        @error('products.{{ $product->id }}.price')
                                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div
                                                        x-data="{
                                                            open: false,
                                                            selectedCategory: '{{ $product->category->name ?? 'Selectează o categorie' }}',
                                                            categoryId: {{ $product->category_id ?? 'null' }},
                                                            setSelected(categoryName, categoryId) {
                                                                this.selectedCategory = categoryName;
                                                                this.categoryId = categoryId;
                                                                $wire.set('category_id', categoryId); // Update Livewire property
                                                                this.open = false;
                                                            }
                                                        }"
                                                        class="relative mt-2 mb-4"
                                                    >
                                                        <label class="block text-gray-700 font-bold">Categorie</label>
                                                        <button
                                                            type="button"
                                                            @click="open = !open"
                                                            class="relative w-full cursor-default border border-black rounded bg-white py-2 pl-3 pr-10 text-left"
                                                        >
                                                            <span class="block truncate" x-text="selectedCategory || 'Selectează o categorie'">
                                                                Selectează o categorie
                                                            </span>
                                                            <span class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                                <x-icons.chevron-down class="h-5 w-5 text-gray-400" />
                                                            </span>
                                                        </button>

                                                        <ul
                                                            x-show="open"
                                                            @click.away="open = false"
                                                            class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"
                                                        >
                                                            <li
                                                                class="cursor-pointer py-2 px-4 {{ $product->category_id === null ? 'bg-gray-200' : '' }}"
                                                                @click="setSelected('Niciuna', null)"
                                                            >
                                                                Niciuna
                                                            </li>
                                                            @foreach($categories as $parent)
                                                                <li
                                                                    class="cursor-pointer py-2 px-4 {{ $product->category_id === $parent->id ? 'bg-gray-200' : '' }}"
                                                                    @click="setSelected('{{ $parent->name }}', {{ $parent->id }})"
                                                                >
                                                                    {{ $parent->name }}
                                                                </li>
                                                                @foreach($parent->children as $child)
                                                                    @include('components.category-option', ['child' => $child, 'depth' => 1, 'selectedId' => $product->category_id])
                                                                @endforeach
                                                            @endforeach
                                                        </ul>

                                                        <!-- Hidden Input to Sync Selected Category -->
                                                        <input
                                                            type="hidden"
                                                            id="category_id"
                                                            wire:model="category_id"
                                                        >
                                                        @error('category_id')
                                                        <span class="text-red-500">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <!-- File Input with Preview -->
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 font-bold">Imagini</label>
                                                        <input
                                                            wire:model="newImages.{{ $product->id }}"
                                                            type="file"
                                                            name="images[]"
                                                            multiple
                                                            class="w-full px-4 py-2 border border-black rounded"
                                                        />
                                                        @error('images.*') <span class="text-red-500">{{ $message }}</span> @enderror

                                                        <!-- Show Existing Images -->
                                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-4">
                                                            @if ($product->images)
                                                                @foreach ($product->images as $key => $image)
                                                                    <div class="relative group">
                                                                        <img
                                                                            src="{{ asset($image) }}"
                                                                            alt="Image Preview"
                                                                            class="w-full h-24 object-cover border rounded-md shadow-md"
                                                                        />
                                                                        <button
                                                                            type="button"
                                                                            wire:click="removeImage({{ $product->id }}, {{ $key }})"
                                                                            class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs rounded shadow-md hover:bg-red-700 cursor-pointer"
                                                                        >
                                                                            Șterge
                                                                        </button>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="bg-white p-3 border border-black rounded mt-8 mb-4">
                                                        <label class="block text-gray-700 font-bold mb-2">Variante de produs</label>

                                                        <!-- Existing Variations -->
                                                        <div>
                                                            @foreach ($productVariations as $index => $variation)
                                                                <div class="flex flex-wrap md:flex-nowrap items-center gap-4 mb-4 p-4 border border-gray-300 rounded bg-white">
                                                                    <!-- Unitate de măsură -->
                                                                    <div class="w-full md:w-1/6">
                                                                        <label class="block text-gray-700 font-bold text-xs">Unitate de măsură</label>
                                                                        <select wire:model.defer="productVariations.{{ $index }}.unitate" class="w-full px-4 py-2 border rounded text-xs">
                                                                            <option value="">Alege Unitate de măsură</option>
                                                                            @foreach ($allAttributes->where('name', 'Unitate de măsură')->first()->values as $value)
                                                                                <option value="{{ $value->id }}" {{ $variation['unitate'] == $value->id ? 'selected' : '' }}>
                                                                                    {{ $value->value }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <!-- Cantitate per ambalaj -->
                                                                    <div class="w-full md:w-1/6">
                                                                        <label class="block text-gray-700 font-bold text-xs">Cantitate per ambalaj</label>
                                                                        <select wire:model.defer="productVariations.{{ $index }}.ambalaj" class="w-full px-4 py-2 border rounded text-xs">
                                                                            <option value="">Alege Cantitate per ambalaj</option>
                                                                            @foreach ($allAttributes->where('name', 'Cantitate per ambalaj')->first()->values as $value)
                                                                                <option value="{{ $value->id }}" {{ $variation['ambalaj'] == $value->id ? 'selected' : '' }}>
                                                                                    {{ $value->value }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <!-- Imagine variantă -->
                                                                    <div class="w-full md:w-1/8">
                                                                        <label class="block text-gray-700 font-bold text-xs">Imagine variantă</label>
                                                                        <input type="file" wire:model.defer="productVariations.{{ $index }}.image" class="w-full px-2 py-1 border rounded text-xs" />
                                                                    </div>
                                                                    <div class="w-full md:w-1/8">
                                                                        <img src="{{ !empty($variation['image']) ? asset($variation['image']) : asset('storage/images/placeholder_product.webp') }}"
                                                                             class="h-12 border border-gray-400 rounded">
                                                                    </div>

                                                                    <!-- Stoc -->
                                                                    <div class="w-full md:w-1/6">
                                                                        <label class="block text-gray-700 font-bold text-xs">Stoc</label>
                                                                        <input type="number" wire:model.defer="productVariations.{{ $index }}.stock" class="w-full px-4 py-2 border rounded text-xs" />
                                                                    </div>

                                                                    <!-- Preț -->
                                                                    <div class="w-full md:w-1/6">
                                                                        <label class="block text-gray-700 font-bold text-xs">Preț</label>
                                                                        <input type="number" wire:model.defer="productVariations.{{ $index }}.price" step="0.01" class="w-full px-4 py-2 border rounded text-xs" />
                                                                    </div>

                                                                    <!-- Delete Button -->
                                                                    <div class="w-full md:w-1/6">
                                                                        <x-primary-link type="button" wire:click="deleteProductVariation({{ $variation['id'] }})" class="text-red-500 mt-4 cursor-pointer">
                                                                            Șterge
                                                                        </x-primary-link>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Add New Variations (for new variants) -->
                                                        <template x-for="(variant, index) in variants" :key="index">
                                                            <div class="flex flex-wrap md:flex-nowrap items-center gap-4 mb-4 p-4 border border-gray-300 rounded bg-white">
                                                                <!-- Unitate de măsură -->
                                                                <div class="w-full md:w-1/6">
                                                                    <label class="block text-gray-700 font-bold text-xs">Unitate de măsură</label>
                                                                    <select x-model="variant.unitate" @change="$wire.set(`newVariations.${index}.unitate`, variant.unitate)" class="w-full px-4 py-2 border rounded text-xs">
                                                                        <option value="">Alege Unitate de măsură</option>
                                                                        <template x-for="value in unitate" :key="value.id">
                                                                            <option :value="value.id" x-text="value.value"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>

                                                                <!-- Cantitate per ambalaj -->
                                                                <div class="w-full md:w-1/6">
                                                                    <label class="block text-gray-700 font-bold text-xs">Cantitate per ambalaj</label>
                                                                    <select x-model="variant.ambalaj" @change="$wire.set(`newVariations.${index}.ambalaj`, variant.ambalaj)" class="w-full px-4 py-2 border rounded text-xs">
                                                                        <option value="">Alege Cantitate per ambalaj</option>
                                                                        <template x-for="value in ambalaje" :key="value.id">
                                                                            <option :value="value.id" x-text="value.value"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>

                                                                <!-- Imagine variantă -->
                                                                <div class="w-full md:w-1/6">
                                                                    <label class="block text-gray-700 font-bold text-xs">Imagine variantă</label>
                                                                    <!-- Remove any wire:model here; use the @change handler -->
                                                                    <input type="file" @change="handleVariantImage($event, index)" class="w-full px-2 py-1 border rounded text-xs" />
                                                                    <!-- Show a preview if available -->
                                                                    <template x-if="variant.imagePreview">
                                                                        <img :src="variant.imagePreview" class="mt-2 h-16 w-16 object-cover border rounded" />
                                                                    </template>
                                                                </div>

                                                                <!-- Stoc -->
                                                                <div class="w-full md:w-1/6">
                                                                    <label class="block text-gray-700 font-bold text-xs">Stoc</label>
                                                                    <input type="number" x-model="variant.stock" @input="$wire.set(`newVariations.${index}.stock`, variant.stock)" class="w-full px-4 py-2 border rounded text-xs" />
                                                                </div>

                                                                <!-- Preț -->
                                                                <div class="w-full md:w-1/6">
                                                                    <label class="block text-gray-700 font-bold text-xs">Preț</label>
                                                                    <input type="number" step="0.01" x-model="variant.price" @input="$wire.set(`newVariations.${index}.price`, variant.price)" class="w-full px-4 py-2 border rounded text-xs" />
                                                                </div>

                                                                <!-- Delete Variant Button -->
                                                                <div class="w-full md:w-1/6">
                                                                    <x-primary-link type="button" class="text-red-500 mt-4 cursor-pointer" @click="removeVariant(index)">
                                                                        Șterge
                                                                    </x-primary-link>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <x-primary-link type="button" @click="addVariant()" class="mt-4 px-4 py-2 text-black rounded">
                                                            + Adaugă variantă
                                                        </x-primary-link>

                                                    </div>


                                                </div>
                                                <!-- Submit Button -->
                                                <div class="flex justify-end mt-4">
                                                    <x-primary-button
                                                        type="button"
                                                        @click="$wire.newVariations = variants; $wire.call('updateProduct', {{ $product->id }})"
                                                    >
                                                        Salvează
                                                    </x-primary-button>

                                                    <button type="button" @click="cancelEdit()" class="btn btn-secondary ml-4 underline">Anulează</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Preview Section -->
                                        <div class="w-full md:w-3/12 border bg-gray-100 p-4 shadow-xl rounded">
                                            <h3 class="text-lg font-semibold mb-4">Previzualizare Produs</h3>
                                            <div class="bg-white border rounded-md overflow-hidden">
                                                <!-- Product Image -->
                                                <div class="w-full">
                                                    <img
                                                        src="{{ asset($product->images[0] ?? 'storage/images/placeholder_product.webp') }}"
                                                        alt="Previzualizare"
                                                        class="w-full h-96 object-cover" />
                                                </div>

                                                <!-- Product Details -->
                                                <div class="p-4 border-t">
                                                    <h4 class="font-bold text-md">{{ $product->name }}</h4>
                                                    <p class="text-sm text-gray-500">{{ strip_tags($product->description) }}</p>
                                                    <p class="font-bold text-md mt-2">{{ $product->price }} RON</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


                <!-- Pagination links -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </main>
    </div>

    <script>
        function attributeManager($wire) {
            const allAttributes = @json($allAttributes);
            const unitate = allAttributes.find(attr => attr.name === 'Unitate de măsură')?.values || [];
            const ambalaje = allAttributes.find(attr => attr.name === 'Cantitate per ambalaj')?.values || [];

            return {
                $wire: $wire,
                editingId: null,
                open: false,
                form: { images: [] },
                unitate,
                ambalaje,
                variants: [],
                stripHtml(content) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = content;
                    return tempDiv.textContent || tempDiv.innerText || '';
                },
                addVariant() {
                    this.variants.push({
                        unitate: null,
                        ambalaj: null,
                        image: null,
                        imagePreview: null,
                        stock: '',
                        price: '',
                    });
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                },
                handleVariantImage(event, index) {
                    const file = event.target.files[0];
                    if (file) {
                        // Save file in local variant for preview only
                        this.variants[index].image = file;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.variants[index].imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                        // Do not call $wire.set() here—wire:model on the input already handles the file.
                    }
                },
                previewImages(event) {
                    if (event.target.files.length) {
                        this.form.images = Array.from(event.target.files).map(file => URL.createObjectURL(file));
                    }
                },
                removeImage(index) {
                    this.form.images.splice(index, 1);
                },
                toggleEdit(id) {
                    this.editingId = this.editingId === id ? null : id;
                    if (this.editingId) {
                        setTimeout(() => {
                            const editor = document.querySelector(`#description-editor-${id}`);
                            if (editor && !quillInstances[`#description-editor-${id}`]) {
                                const livewireProperty = `descriptions.${id}`;
                                initializeQuill(`#description-editor-${id}`, livewireProperty);
                            }
                        }, 100);
                    }
                },
                isEditing(id) {
                    return this.editingId === id;
                },
                cancelEdit() {
                    this.editingId = null;
                },
                // New method to close the accordion (or all accordions in this scope)
                closeAccordion() {
                    this.editingId = null;
                },
                // Using x-init to listen for the flashMessage event
                init() {
                    window.addEventListener('flashMessage', () => {
                        this.closeAccordion();
                    });
                },
            };
        }
    </script>

    <script>
        const quillInstances = {};

        function initializeQuill(selector, livewireProperty) {
            console.log(`Initializing Quill for selector: ${selector}`);
            const editorElement = document.querySelector(selector);
            if (!editorElement) {
                console.log(`Editor not found for selector: ${selector}`);
                return;
            }

            const quill = new Quill(editorElement, {
                theme: 'snow',
            });

            const hiddenInput = document.querySelector(`#description-input-add`);
            quill.on('text-change', function () {
                const content = quill.root.innerHTML.trim();
                hiddenInput.value = content; // Update hidden input
                hiddenInput.dispatchEvent(new Event('input')); // Trigger Livewire
                console.log(`Updated hidden input value: ${content}`);
            });

            // Initialize hidden input with existing value
            hiddenInput.value = quill.root.innerHTML.trim();
        }

        document.addEventListener('DOMContentLoaded', function () {
            initializeQuill('#description-editor-add', 'description');

            Livewire.hook('message.processed', () => {
                document.querySelectorAll('.description-editor-edit').forEach((editor) => {
                    const editorId = editor.id; // Unique editor id
                    const livewireProperty = editor.getAttribute('data-livewire-property');

                    if (!quillInstances[editorId]) {
                        initializeQuill(`#${editorId}`, livewireProperty);
                    }
                });
            });

            Livewire.hook('message.processed', () => {
                console.log('Livewire message processed');
                const descriptionInput = document.querySelector('#description-input-add');
                if (descriptionInput) {
                    console.log('Hidden input value on message processed:', descriptionInput.value);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            Livewire.hook('message.processed', () => {
                console.log('Livewire message processed');
                console.log($wire.productVariations);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('flashMessage', event => {
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el.__x && el.__x.$data.editingId !== undefined) {
                        el.__x.$data.editingId = null;
                    }
                });
            });
        });



    </script>

    <style>
        .ql-toolbar.ql-snow  {
            border: 1px solid #000 !important;
        }
        .ql-toolbar.ql-snow + .ql-container.ql-snow  {
            border-color: #000 !important;
        }
    </style>

</div>


