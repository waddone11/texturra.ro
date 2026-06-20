<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            <livewire:sidebar-stats />
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="container">
                    <h1 class="text-2xl font-semibold mb-6">Categories</h1>

                    <!-- Navigation Bar with Search, Filter, and Add Button -->
                    <div class="flex justify-between items-center mb-4">
                        <!-- Search Bar -->
                        <div class="flex items-center space-x-4">
                            <input type="text" wire:model="search" placeholder="Search categories..." class="px-4 py-2 border rounded" />
                        </div>

                        <!-- Add Category Button (opens modal) -->
                        <div x-data="{ modalOpen: @entangle('modalOpen') }">
                            <!-- Add Category Button -->
                            <x-primary-button-border type="button" @click="modalOpen = true" wire:click="createNewCategory">
                                <x-icons.plus class="h-5 w-5 mr-2" />
                                Add Category
                                </x-primary-button>

                                <!-- Modal for Adding/Editing Category -->
                                <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
                                    <!-- Modal Background -->
                                    <div class="absolute inset-0 bg-black bg-opacity-50" @click="modalOpen = false"></div>

                                    <!-- Modal Content -->
                                    <div class="bg-white rounded-lg p-6 w-full max-w-3xl z-10 overflow-y-auto max-h-[90vh]">
                                        <h2 class="text-lg font-semibold">{{ $isEditMode ? 'Edit Category' : 'Add Category' }}</h2>

                                        <!-- Form -->
                                        <form wire:submit.prevent="{{ $isEditMode ? 'updateCategory' : 'createCategory' }}">
                                            <div class="mb-4">
                                                <label class="block text-gray-700">Name</label>
                                                <input type="text" wire:model="name" class="w-full px-4 py-2 border rounded" />
                                                @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-gray-700">Description</label>
                                                <textarea wire:model="description" class="w-full px-4 py-2 border rounded"></textarea>
                                                @error('description') <span class="text-red-500">{{ $message }}</span> @enderror
                                            </div>

                                            <div x-data="{
                                        open: false,
                                        selectedCategory: @entangle('selectedCategoryName'),
                                        setSelected(categoryName, categoryId) {
                                            this.selectedCategory = categoryName;
                                            $wire.set('parent_id', categoryId);
                                            this.open = false;
                                        }
                                    }" class="relative mt-2">
                                                <label for="parent_category" class="block text-sm font-medium text-gray-900">Parent Category</label>

                                                <!-- Dropdown Button -->
                                                <button type="button" @click="open = !open"
                                                        class="relative w-full cursor-default border border-black rounded bg-white py-2 pl-3 pr-10 text-left"
                                                        style="height: 42px;">
                                            <span class="flex items-center">
                                                <span class="block truncate" x-text="selectedCategory">Select a category</span>
                                            </span>
                                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                <x-icons.chevron-down class="h-5 w-5 text-gray-400" />
                                            </span>
                                                </button>

                                                <!-- Dropdown Options -->
                                                <ul x-show="open" @click.away="open = false"
                                                    class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                    <li class="relative cursor-default select-none py-2 text-gray-900 border-b border-gray-200"
                                                        role="option" @click="setSelected('None', null)">
                                                        <span class="pl-3 block truncate font-normal">None</span>
                                                    </li>
                                                    @foreach($allCategories as $parent)
                                                        <li class="relative cursor-default select-none py-2 text-gray-900 border-b border-gray-200"
                                                            role="option" @click="setSelected('{{ $parent->name }}', {{ $parent->id }})">
                                                            <span class="pl-3 block truncate font-normal">{{ $parent->name }}</span>
                                                        </li>
                                                        @foreach($parent->children as $child)
                                                            @include('components.category-option', ['child' => $child, 'depth' => 1])
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="flex justify-end mt-4">
                                                <x-primary-button type="submit">
                                                    {{ $isEditMode ? 'Update Category' : 'Create Category' }}
                                                </x-primary-button>

                                                <x-secondary-button @click="modalOpen = false" wire:click="resetFields">
                                                    Cancel
                                                </x-secondary-button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                        </div>
                    </div>

                    <!-- Categories Table -->
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left  text-xs md:text-md">Name</th>
                            <th class="p-2 text-right  text-xs md:text-md">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allCategories as $category)
                            <x-category-item :category="$category" :depth="0" />
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination links -->
                    <div class="mt-4">
                        {{ $allCategories->links() }}
                    </div>
                </div>
        </main>
    </div>
</div>
