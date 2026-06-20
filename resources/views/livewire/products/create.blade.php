@extends('layouts.base')

@section('content')
    <div class="max-w-9xl mx-auto py-12 px-3 md:px-6" x-data="attributeManager()">
        <div class="flex flex-wrap md:flex-nowrap">
            <!-- Sidebar (1/5) -->
            <aside class="w-full md:w-2/12 p-3 md:p-0">
                <livewire:sidebar-stats />
            </aside>

            <!-- Main Content (4/5) -->
            <main class="w-full md:w-10/12 pl-3">
                <div class="pr-3">
                    <h1 class="text-2xl font-semibold mb-6">Adaugă Produs</h1>

                    <!-- Product Form -->
                    <div class="flex flex-wrap md:flex-nowrap gap-6">
                        <div class="w-full md:w-9/12 border bg-gray-100 p-4 shadow-xl rounded">
                            <form wire:submit.prevent="createProduct" enctype="multipart/form-data">
                                @csrf

                                <!-- Titlu -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold">Titlu</label>
                                    <input type="text" name="name" class="w-full px-4 py-2 border rounded" value="{{ old('name') }}" x-model="form.name" />
                                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Descriere -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold">Descriere</label>
                                    <div id="description-editor" class="w-full px-4 py-2 border border-black rounded-b bg-white"></div>
                                    <input type="hidden" name="description" id="description-input" value="{{ old('description') }}" x-model="form.description" />
                                    @error('description') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Preț -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold">Preț</label>
                                    <input type="number" name="price" step="0.01" class="w-full px-4 py-2 border rounded" value="{{ old('price') }}" x-model="form.price" />
                                    @error('price') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Categorie -->
                                <div x-data="{
                                    open: false,
                                    selectedCategory: '{{ old('category_id') ? $categories->find(old('category_id'))->name : 'Selectează o categorie' }}',
                                    setSelected(categoryName, categoryId) {
                                        this.selectedCategory = categoryName;
                                        document.getElementById('category_id').value = categoryId;
                                        this.open = false;
                                    }
                                }" class="relative mt-2 mb-4">
                                    <label class="block text-gray-700 font-bold">Categorie</label>
                                    <button type="button" @click="open = !open" class="relative w-full cursor-default border border-black rounded bg-white py-2 pl-3 pr-10 text-left">
                                        <span class="block truncate" x-text="selectedCategory">Selectează o categorie</span>
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

                                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id') }}">
                                    @error('category_id') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- File Input with Preview -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold">Imagini</label>
                                    <input
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
                                                    class="text-red-500 mt-4">
                                                    Șterge
                                                </x-primary-link>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Variante -->
                                <div class="bg-white p-3 border  border-black rounded mt-8 mb-4">
                                    <label class="block text-gray-700 font-bold mb-2">Variante de produs</label>

                                    <template x-for="(variant, index) in variants" :key="index">
                                        <div class="flex items-center gap-4 mb-4 p-4 border border-gray-300 rounded bg-white">
                                            <!-- Mărime -->
                                            <div class="w-1/5">
                                                <label class="block text-gray-700 font-bold text-xs">Mărime</label>
                                                <select
                                                    x-model="variant.attributes.size"
                                                    class="w-full px-4 py-2 border rounded text-xs"
                                                >
                                                    <option value="">Alege Mărime</option>
                                                    <template x-for="value in sizes" :key="value.id">
                                                        <option :value="value.id" x-text="value.value"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <!-- Culoare -->
                                            <div class="w-1/5">
                                                <label class="block text-gray-700 font-bold text-xs">Culoare</label>
                                                <select
                                                    x-model="variant.attributes.color"
                                                    class="w-full px-4 py-2 border rounded text-xs"
                                                >
                                                    <option value="">Alege Culoare</option>
                                                    <template x-for="value in colors" :key="value.id">
                                                        <option :value="value.id" x-text="value.value"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <!-- Material -->
                                            <div class="w-1/5">
                                                <label class="block text-gray-700 font-bold text-xs">Material</label>
                                                <select
                                                    x-model="variant.attributes.material"
                                                    class="w-full px-4 py-2 border rounded text-xs"
                                                >
                                                    <option value="">Alege Material</option>
                                                    <template x-for="value in materials" :key="value.id">
                                                        <option :value="value.id" x-text="value.value"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <!-- Stoc -->
                                            <div class="w-1/5">
                                                <label class="block text-gray-700 font-bold text-xs">Stoc</label>
                                                <input
                                                    type="number"
                                                    x-model="variant.stock"
                                                    class="w-full px-4 py-2 border rounded text-xs"
                                                />
                                            </div>

                                            <!-- Preț -->
                                            <div class="w-1/5">
                                                <label class="block text-gray-700 font-bold text-xs">Preț</label>
                                                <input
                                                    type="number"
                                                    x-model="variant.price"
                                                    step="0.01"
                                                    class="w-full px-4 py-2 border rounded text-xs"
                                                />
                                            </div>

                                            <!-- Remove Button -->
                                            <div>
                                                <x-primary-link type="button" @click="removeVariant(index)"  class="text-red-500 mt-4">
                                                    Șterge
                                                </x-primary-link>
                                            </div>
                                        </div>
                                    </template>

                                    <x-primary-link type="button" @click="addVariant()">
                                        + Adaugă variantă
                                    </x-primary-link>
                                </div>

                                <!-- Submit -->
                                <div class="flex justify-end mt-4">
{{--                                    <x-primary-button-border type="submit">--}}
{{--                                        Salvează produs--}}
{{--                                    </x-primary-button-border>--}}
{{--                                    <button type="submit" class="btn btn-primary">Salvează</button>--}}
                                    <x-primary-button type="submit">Salvează</x-primary-button>
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
                                            src="/storage/images/placeholder_product.png"
                                            alt="Previzualizare"
                                            class="w-full h-96 object-cover"
                                        />
                                    </template>
                                </div>

                                <!-- Product Details -->
                                <div class="p-4 border-t">
                                    <h4 class="font-bold text-md" x-text="form.name || 'Titlu produs'"></h4>
                                    <p class="text-sm text-gray-500" x-text="form.description || 'Descriere produs...'"></p>
                                    <p class="font-bold text-md mt-2" x-text="form.price ? form.price + ' RON' : '0.00 RON'"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <script>
                    function attributeManager() {
                        const allAttributes = @json($allAttributes);
                        const sizes = allAttributes.find(attr => attr.name === 'Mărime')?.values || [];
                        const colors = allAttributes.find(attr => attr.name === 'Culoare')?.values || [];
                        const materials = allAttributes.find(attr => attr.name === 'Material')?.values || [];

                        return {
                            // Form Data
                            form: {
                                name: '',
                                description: '',
                                price: '',
                                images: []
                            },

                            // Attribute Data
                            sizes,
                            colors,
                            materials,
                            variants: [],

                            // Methods for Managing Variants
                            addVariant() {
                                this.variants.push({
                                    attributes: {
                                        size: null,
                                        color: null,
                                        material: null,
                                    },
                                    stock: '',
                                    price: '',
                                });
                            },

                            removeVariant(index) {
                                this.variants.splice(index, 1);
                            },

                            // Methods for Managing Images
                            previewImages(event) {
                                if (event.target.files.length) {
                                    this.form.images = Array.from(event.target.files).map(file => URL.createObjectURL(file));
                                }
                            },

                            removeImage(index) {
                                this.form.images.splice(index, 1);
                            }
                        };
                    }
                </script>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        // Initialize Quill editor
                        var quill = new Quill('#description-editor', {
                            theme: 'snow'
                        });

                        // Update hidden input value on change
                        quill.on('text-change', function () {
                            document.getElementById('description-input').value = quill.root.innerHTML;
                        });

                        // Pre-fill Quill editor with old data
                        var oldDescription = {!! json_encode(old('description', '')) !!};
                        if (oldDescription) {
                            quill.root.innerHTML = oldDescription;
                        }
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
            </main>
        </div>
    </div>


@endsection
