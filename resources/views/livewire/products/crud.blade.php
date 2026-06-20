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
                    // trigger the add product compoente
                </div>

                <div class="items-center mb-4">
                   filter component
                </div>

                <!-- Products Table -->
                <div class="w-full overflow-x-auto border" x-data="attributeManager($wire)">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left text-xs md:text-md">Images</th>
                            <th class="p-2 text-left text-xs md:text-md">Nume</th>
                            <th class="p-2 text-left text-xs md:text-md">EAN</th>
                            <th class="p-2 text-left text-xs md:text-md">Descriere</th>
                            <th class="p-2 text-left text-xs md:text-md">Categorie</th>
                            <th class="p-2 text-left text-xs md:text-md">Pret</th>
                            <th class="p-2 text-left text-xs md:text-md">Stoc</th>
                            <th class="p-2 text-right text-xs md:text-md">Actiuni</th>
                        </tr>
                        </thead>
                        // on action we gona have the edit and delete button ,
                        // when edit a product will have to trigger the edit component
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


