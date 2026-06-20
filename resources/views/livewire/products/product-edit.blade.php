<div x-data="attributeSelector({{ json_encode($allAttributes) }}, $wire.entangle('productVariations'), $wire)"
     x-ref="attributeBox"
     x-init="init()"
     @submit.prevent="prepareAndSubmit()">
    <div x-data="imageUploader({{ json_encode($existingImages) }}, {{ $productId }})" class="text-left">
        <form class="space-y-4">
                <!-- Nume, Preț -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Nume</label>
                        <input type="text" wire:model.defer="name" class="w-full border-gray-300 rounded p-2 text-sm">
                        @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Preț</label>
                        <input type="number" step="0.01" wire:model.defer="price" class="w-full border-gray-300 rounded p-2 text-sm">
                        @error('price')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Stoc, EAN -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Stoc</label>
                        <input type="number" wire:model.defer="general_stock" class="w-full border-gray-300 rounded p-2 text-sm">
                        @error('general_stock')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">EAN</label>
                        <input type="text" wire:model.defer="ean" class="w-full border-gray-300 rounded p-2 text-sm">
                        @error('ean')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Categorie (Choices.js with hidden input) -->
                <div x-data="choicesDropdown()" x-init="initChoices()" wire:ignore>
                    <label class="text-sm text-gray-600">Categorie</label>
                    <select x-ref="selectEl" class="w-full border-gray-300 rounded p-2 text-sm">
                        <option value="">Selectează o categorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @if($cat->id == $category_id) selected @endif>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" wire:model="category_id">
                </div>

                <!-- Descriere (Quill Editor) -->
                <div wire:ignore x-data x-init="initializeQuill('#description-editor-{{ $productId }}', '#description-input-{{ $productId }}')">
                    <label class="text-sm text-gray-600">Descriere</label>
                    <div id="description-editor-{{ $productId }}" class="w-full border border-gray-300 rounded bg-white p-2 h-[150px] overflow-y-auto">
                        {!! $description !!}
                    </div>
                    <input type="hidden" id="description-input-{{ $productId }}" wire:model="description">
                </div>

                <!-- Imagini (File Upload + Preview) -->
                <div>
                    <label class="text-sm text-gray-600">Imagini</label>
                    <input type="file" wire:model="newImages" multiple class="w-full border-gray-300 rounded p-2 text-sm"
                           @change="previewImages($event)">
                    @error('newImages.*')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror

                    <div class="mt-4 flex gap-2">
                        <template x-for="(image, index) in images" :key="index">
                            <div class="relative">
                                <img :src="image" class="h-24 w-24 object-cover border border-gray-400 rounded shadow-md">
                                <button type="button" @click="removeImage(index)" class="absolute top-0 right-0 bg-red-600 text-white px-2 py-1 text-xs rounded shadow-md">
                                    ✖
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Opțiuni produs (Dynamic Attribute/Value Section) -->
                <div class="relative">
                    <label class="text-sm text-gray-600 mb-4">Opțiuni produs</label>

                    <!-- Preview Section: Display existing attribute/value pairs -->
                    <div class="mb-4 mt-4" x-show="selectedOptions.length > 0">
                        <span class="text-xs font-medium">Opțiuni selectate:</span>
                        <template x-for="(option, index) in selectedOptions" :key="index">
                            <div class="flex justify-between items-center p-2 border rounded mb-2">
                                <span x-text="option.name + ' : ' + option.value"></span>
                                <button type="button" @click="removeOption(index)" class="text-red-500">Șterge</button>
                            </div>
                        </template>
                    </div>

                    <!-- Selection Block: To add new options -->
                    <div class="mb-4 mt-4 border border-gray-200 p-4">
                        <div class="flex items-end space-x-2">
                            <div class="w-1/3">
                                <label class="text-sm text-gray-600">Opțiune</label>
                                <select x-model="selectedAttribute" @change="updateValues()"
                                        class="w-full border-gray-300 rounded p-2 text-sm">
                                    <option value="">Selectează o opțiune</option>
                                    <template x-for="attribute in attributes" :key="attribute.id">
                                        <option :value="attribute.id" x-text="attribute.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="w-1/3" x-data="choicesValueDropdown()" x-init="initChoices()" wire:ignore>
                                <label class="text-sm text-gray-600">Valoare</label>
                                <select x-ref="valueSelect" class="w-full border-gray-300 rounded p-2 text-sm">
                                    <option value="">Selectează valoare</option>
                                    <template x-for="value in values" :key="value.id">
                                        <option :value="value.id" x-text="value.value"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <x-primary-link class="cursor-pointer" @click="addOption()">
                                    <x-icons.plus class="h-5 w-5 mr-2" /> opțiune
                                </x-primary-link>
                            </div>
                        </div>
                    </div>

                    <!-- Addition Block: To create new attributes/values -->
                    <div class="mb-4">
                        <div class="flex space-x-4">
                            <button type="button" @click="toggleAccordion('attribute')" class="text-xs text-blue-500 underline">
                                + Adaugă opțiune nouă
                            </button>
                            <button type="button" @click="toggleAccordion('value')" class="text-xs text-blue-500 underline">
                                + Adaugă valoare nouă
                            </button>
                        </div>
                        <div x-show="openAttributeForm" x-transition class="mt-4 border p-3 rounded shadow">
                            <label class="text-sm text-gray-600">Nume opțiune nouă</label>
                            <input type="text" x-model="newAttribute" class="w-full border-gray-300 rounded p-2 text-sm">
                            <button type="button" @click="addAttribute()" class="bg-green-500 text-white px-3 py-1 mt-2 rounded text-sm">
                                Adaugă opțiune
                            </button>
                        </div>
                        <div x-show="openValueForm" x-transition class="mt-4 border p-3 rounded shadow">
                            <label class="text-sm text-gray-600">Alege opțiune</label>
                            <select x-model="selectedAttributeForValue" class="w-full border-gray-300 rounded p-2 text-sm">
                                <option value="">Selectează opțiune</option>
                                <template x-for="attribute in attributes" :key="attribute.id">
                                    <option :value="attribute.id" x-text="attribute.name"></option>
                                </template>
                            </select>
                            <label class="text-sm text-gray-600 mt-2">Valoare nouă</label>
                            <input type="text" x-model="newValue" class="w-full border-gray-300 rounded p-2 text-sm">
                            <button type="button" @click="addValue()" class="bg-green-500 text-white px-3 py-1 mt-2 rounded text-sm">
                                Adaugă valoare
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded shadow text-sm">
                        Actualizează produsul
                    </button>
                </div>
            </form>
    </div>
    <script>
        // ✅ Quill editor setup
        function initializeQuill(editorSelector, inputSelector) {
            setTimeout(() => {
                const editorElement = document.querySelector(editorSelector);
                if (!editorElement) {
                    console.log(`Editor not found for selector: ${editorSelector}`);
                    return;
                }
                const quill = new Quill(editorElement, { theme: 'snow' });
                const hiddenInput = document.querySelector(inputSelector);
                if (hiddenInput) {
                    quill.root.innerHTML = hiddenInput.value;
                    quill.on('text-change', function () {
                        const content = quill.root.innerHTML.trim();
                        hiddenInput.value = content;
                        hiddenInput.dispatchEvent(new Event('input'));
                    });
                    hiddenInput.value = quill.root.innerHTML.trim();
                }
            }, 100);
        }

        // ✅ Choices.js dropdown for attribute values
        function choicesValueDropdown() {
            return {
                instance: null,

                initChoices() {
                    this.$nextTick(() => {
                        this.rebuild();
                    });

                    window.addEventListener('values-updated', () => {
                        this.rebuild();
                    });
                },

                rebuild() {
                    if (this.instance) {
                        this.instance.destroy();
                    }

                    setTimeout(() => {
                        this.instance = new Choices(this.$refs.valueSelect, {
                            searchEnabled: true,
                            shouldSort: false,
                            itemSelectText: '',
                        });

                        this.$refs.valueSelect.addEventListener('change', (e) => {
                            const value = e.target.value;
                            this.$dispatch('value-selected', value); // 🔁 Send to parent
                        });
                    }, 50);
                }
            };
        }

        // ✅ Image uploader with preview support
        function imageUploader(initialImages = [], productId) {
            return {
                images: initialImages,
                productId: productId,
                previewImages(event) {
                    this.images = Array.from(event.target.files).map(file => URL.createObjectURL(file));
                },
                removeImage(index) {
                    if (this.productId && this.$wire) {
                        this.$wire.call('removeImage', this.productId, index);
                    }
                    this.images.splice(index, 1);
                }
            };
        }

        // ✅ Alpine.js controller for managing attributes/values
        function attributeSelector(initialAttributes, productVariations, livewire) {
            return {
                attributes: initialAttributes,
                selectedOptions: productVariations,
                selectedAttribute: '',
                selectedValue: '',
                values: [],
                openAttributeForm: false,
                openValueForm: false,
                newAttribute: '',
                selectedAttributeForValue: '',
                newValue: '',
                loading: false,
                livewire: livewire,

                init() {
                    window.addEventListener('attributesUpdated', event => {
                        let detail = event.detail;
                        if (Array.isArray(detail) && detail.length > 0 && detail[0].attributes) {
                            this.attributes = detail[0].attributes;
                        } else if (detail.attributes) {
                            this.attributes = detail.attributes;
                        }
                        console.log("Updated attributes:", this.attributes);
                    });

                    this.$el.addEventListener('value-selected', (e) => {
                        this.selectedValue = e.detail;
                    });
                },

                updateValues() {
                    const attribute = this.attributes.find(attr => attr.id == this.selectedAttribute);
                    this.values = attribute ? attribute.values : [];
                    this.selectedValue = '';

                    // 🔁 Notify Choices.js to rebuild value dropdown
                    window.dispatchEvent(new CustomEvent('values-updated'));
                },

                addOption() {
                    if (this.selectedAttribute && this.selectedValue) {
                        const attribute = this.attributes.find(attr => attr.id == this.selectedAttribute);
                        const value = this.values.find(v => v.id == this.selectedValue);
                        const exists = this.selectedOptions.some(opt =>
                            opt.attribute_id == this.selectedAttribute &&
                            opt.attribute_value_id == this.selectedValue
                        );
                        if (!exists) {
                            this.selectedOptions.push({
                                attribute_id: this.selectedAttribute,
                                attribute_value_id: this.selectedValue,
                                name: attribute?.name || '',
                                value: value?.value || '',
                            });
                            this.selectedAttribute = '';
                            this.selectedValue = '';
                            this.values = [];
                        }
                    }
                },

                removeOption(index) {
                    const option = this.selectedOptions[index];
                    if (option.id && this.livewire) {
                        this.livewire.call('deleteVariation', option.id).then(() => {
                            this.selectedOptions.splice(index, 1);
                        });
                    } else {
                        this.selectedOptions.splice(index, 1);
                    }
                },

                toggleAccordion(type) {
                    if (type === 'attribute') {
                        this.openAttributeForm = !this.openAttributeForm;
                        this.openValueForm = false;
                    } else if (type === 'value') {
                        this.openValueForm = !this.openValueForm;
                        this.openAttributeForm = false;
                    }
                },

                async addAttribute() {
                    if (!this.newAttribute.trim()) return;
                    this.loading = true;
                    await this.livewire.addAttribute(this.newAttribute);
                    this.loading = false;
                    this.newAttribute = '';
                    this.openAttributeForm = false;
                },

                async addValue() {
                    if (!this.selectedAttributeForValue || !this.newValue.trim()) return;
                    this.loading = true;
                    await this.livewire.addValue(this.selectedAttributeForValue, this.newValue);
                    this.loading = false;
                    this.newValue = '';
                    this.selectedAttributeForValue = '';
                    this.openValueForm = false;
                },

                ensurePendingOption() {
                    if (this.selectedAttribute && this.selectedValue) {
                        const exists = this.selectedOptions.some(opt =>
                            opt.attribute_id == this.selectedAttribute &&
                            opt.attribute_value_id == this.selectedValue
                        );
                        if (!exists) {
                            const attribute = this.attributes.find(attr => attr.id == this.selectedAttribute);
                            const value = this.values.find(v => v.id == this.selectedValue);
                            this.selectedOptions.push({
                                attribute_id: this.selectedAttribute,
                                attribute_value_id: this.selectedValue,
                                name: attribute?.name || '',
                                value: value?.value || '',
                            });
                            this.selectedAttribute = '';
                            this.selectedValue = '';
                            this.values = [];
                        }
                    }
                },

                prepareAndSubmit() {
                    this.ensurePendingOption();
                    this.livewire.updateProduct();
                }
            };
        }
    </script>


    <style>
        .ql-toolbar.ql-snow {
            border: 1px solid #000 !important;
        }
        .ql-container.ql-snow {
            border: 1px solid #000 !important;
        }
    </style>
</div>
