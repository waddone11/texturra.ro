<div x-data="attributeManager()" also add imageUploader() class="relative" x-init="init()">
    <form wire:submit.prevent="createProduct" class="space-y-4">

        <!-- Nume, Preț -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Nume</label>
                <input type="text" wire:model.defer="name" class="w-full border-gray-300 rounded p-2 text-sm">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-sm text-gray-600">Preț</label>
                <input type="number" wire:model.defer="price" class="w-full border-gray-300 rounded p-2 text-sm">
                @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Stoc, EAN -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Stoc</label>
                <input type="number" wire:model.defer="general_stock" class="w-full border-gray-300 rounded p-2 text-sm">
                @error('general_stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-sm text-gray-600">EAN</label>
                <input type="text" wire:model.defer="ean" class="w-full border-gray-300 rounded p-2 text-sm">
                @error('ean') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Categorie (Select2) -->
        <div x-data="choicesDropdown()"
             x-init="initChoices()"
             wire:ignore
        >
            <label class="text-sm text-gray-600">Categorie</label>
            <select x-ref="selectEl"
                    class="w-full border-gray-300 rounded p-2 text-sm">
                <option value="">Selectează o categorie</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Descriere -->
        <div wire:ignore>
            <label class="text-sm text-gray-600">Descriere</label>
            <div id="description-editor" class="w-full border border-gray-300 rounded bg-white p-2"></div>
            <input type="hidden" id="description-input" wire:model.defer="description">
        </div>

        <!-- Imagini (File Upload + Preview) -->
        <div>
            <label class="text-sm text-gray-600">Imagini</label>
            <input type="file" wire:model="newImages" multiple class="w-full border-gray-300 rounded p-2 text-sm"
                   @change="previewImages($event)">

            @error('newImages.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <!-- ✅ Image Previews -->
            <div class="mt-4 flex gap-2">
                <template x-for="(image, index) in images" :key="index">
                    <div class="relative">
                        <img :src="image" class="h-24 w-24 object-cover border border-gray-400 rounded shadow-md">
                        <button type="button"
                                class="absolute top-0 right-0 bg-red-600 text-white px-2 py-1 text-xs rounded shadow-md"
                                @click="removeImage(index)">
                            ✖
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <!-- Attribute Management -->
            <label class="text-sm text-gray-600">Opțiuni produs</label>

            <!-- ✅ Preview Block -->
            <div class="border p-3 rounded shadow mt-2 bg-gray-100">
                <h3 class="text-sm font-semibold mb-2">Opțiuni adăugate</h3>
                <template x-for="(variation, index) in productVariations" :key="index">
                    <div class="flex items-center justify-between bg-white p-2 rounded shadow mb-2">
                        <span x-text="getAttributeName(variation.attribute_id)"></span>
                        <span x-text="getAttributeValue(variation.attribute_value_id)"></span>
                        <button type="button" @click="removeVariation(index)" class="text-red-500 text-xs">✖</button>
                    </div>
                </template>
            </div>

            <!-- ✅ Attribute Selection -->
            <div class="grid grid-cols-4 gap-2 items-center mt-4">
                <div>
                    <select x-model="selectedAttribute" @change="updateValues()"
                            class="w-full border-gray-300 rounded p-2 text-sm">
                        <option value="">Alege opțiune</option>
                        <template x-for="attribute in allAttributes" :key="attribute.id">
                            <option :value="attribute.id" x-text="attribute.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <select x-model="selectedValue" class="w-full border-gray-300 rounded p-2 text-sm">
                        <option value="">Alege valoare</option>
                        <template x-for="value in availableValues" :key="value.id">
                            <option :value="value.id" x-text="value.value"></option>
                        </template>
                    </select>
                </div>

                <button type="button" @click="addPreviewOption()" class="text-green-500 text-xs">✅</button>
            </div>

            <!-- Buttons to Open Accordions -->
            <div class="mt-2">
                <button type="button" @click="toggleAccordion('attribute')" class="text-xs text-blue-500 underline cursor-pointer">
                    + Adaugă opțiune
                </button>

                <button type="button" @click="toggleAccordion('value')" class="text-xs text-blue-500 underline cursor-pointer ml-4">
                    + Adaugă valoare
                </button>
            </div>

            <!-- ✅ Add Attribute (Accordion) -->
            <div x-show="openAttributeForm" x-transition.opacity.duration.200ms class="mt-4 border p-3 rounded shadow">
                <label class="text-sm text-gray-600">Nume opțiune</label>
                <input type="text" x-model="newAttribute" class="w-full border-gray-300 rounded p-2 text-sm">
                <button type="button" @click="addAttribute()" class="bg-green-500 text-white px-3 py-1 mt-2 rounded text-sm">
                    Adaugă opțiune
                </button>
            </div>

            <!-- ✅ Add Value (Accordion) -->
            <div x-show="openValueForm" x-transition.opacity.duration.200ms class="mt-4 border p-3 rounded shadow">
                <label class="text-sm text-gray-600">Alege opțiune</label>
                <select x-model="selectedAttributeForValue" class="w-full border-gray-300 rounded p-2 text-sm">
                    <option value="">Selectează opțiune</option>
                    <template x-for="attribute in allAttributes" :key="attribute.id">
                        <option :value="attribute.id" x-text="attribute.name"></option>
                    </template>
                </select>

                <label class="text-sm text-gray-600 mt-2">Valoare</label>
                <input type="text" x-model="newValue" class="w-full border-gray-300 rounded p-2 text-sm">

                <button type="button" @click="addValue()" class="bg-green-500 text-white px-3 py-1 mt-2 rounded text-sm">
                    Adaugă valoare
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded shadow text-sm">
                Adaugă produsul
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var quill = new Quill('#description-editor', { theme: 'snow' });
        quill.on('text-change', function () {
            document.getElementById('description-input').value = quill.root.innerHTML;
        });
    });

    function imageUploader() {
        return {
            images: [],
            previewImages(event) {
                this.images = Array.from(event.target.files).map(file => URL.createObjectURL(file));
            },
            removeImage(index) {
                this.images.splice(index, 1);
            }
        };
    }

    function choicesDropdown() {
        return {
            choicesInstance: null,
            initChoices() {
                this.choicesInstance = new Choices(this.$refs.selectEl, {
                    removeItemButton: true,
                    allowHTML: true,
                    searchEnabled: true,
                    itemSelectText: '',
                });

                this.$refs.selectEl.addEventListener('change', (event) => {
                    Livewire.dispatch('updateCategory', event.target.value);
                });

                Livewire.hook('message.processed', () => {
                    this.choicesInstance.destroy();
                    this.initChoices();
                });
            }
        };
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('attributeManager', () => ({
            allAttributes: [],
            productVariations: [],
            availableValues: [],
            selectedAttribute: '',
            selectedValue: '',
            loading: false,
            openAttributeForm: false,
            openValueForm: false,
            newAttribute: '',
            selectedAttributeForValue: '',
            newValue: '',

            init() {
                window.Livewire.on('attributesUpdated', (attributes) => {
                    this.allAttributes = attributes;
                });
            },

            toggleAccordion(type) {
                this.openAttributeForm = type === 'attribute' ? !this.openAttributeForm : false;
                this.openValueForm = type === 'value' ? !this.openValueForm : false;
            },

            updateValues() {
                let selected = this.allAttributes.find(attr => attr.id == this.selectedAttribute);
                this.availableValues = selected ? selected.values : [];
            },

            addPreviewOption() {
                if (!this.selectedAttribute || !this.selectedValue) return;

                let attrExists = this.productVariations.some(variation =>
                    variation.attribute_id == this.selectedAttribute &&
                    variation.attribute_value_id == this.selectedValue
                );

                if (!attrExists) {
                    this.productVariations.push({
                        attribute_id: this.selectedAttribute,
                        attribute_value_id: this.selectedValue
                    });
                }
            },

            removeVariation(index) {
                this.productVariations.splice(index, 1);
            },

            async addAttribute() {
                if (!this.newAttribute.trim()) return;
                this.loading = true;
                await Livewire.dispatch('addAttribute', this.newAttribute);
                this.loading = false;
                this.newAttribute = '';
                this.openAttributeForm = false;
            },

            async addValue() {
                if (!this.selectedAttributeForValue || !this.newValue.trim()) return;
                this.loading = true;
                await Livewire.dispatch('addValue', {
                    attribute_id: this.selectedAttributeForValue,
                    value: this.newValue
                });
                this.loading = false;
                this.newValue = '';
                this.openValueForm = false;
            },

            getAttributeName(id) {
                let attr = this.allAttributes.find(attr => attr.id == id);
                return attr ? attr.name : 'N/A';
            },

            getAttributeValue(id) {
                let attr = this.allAttributes.find(attr => attr.values.some(v => v.id == id));
                return attr ? attr.values.find(v => v.id == id)?.value || 'N/A' : 'N/A';
            }
        }));
    });

</script>
