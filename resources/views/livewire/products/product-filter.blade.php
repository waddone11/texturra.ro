<div class="pb-4">
    <h2 class="text-lg font-semibold mb-3">Filtre</h2>

    <div class="flex flex-wrap gap-4 items-end">
        <!-- Search by Name -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">Nume</label>
            <input type="text" wire:model.defer="searchName"
                   class="w-32 border-gray-300 rounded p-1 text-xs"
                   placeholder="Introduceți numele">
        </div>

        <!-- Search by ID -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">ID</label>
            <input type="number" wire:model.defer="searchId"
                   class="w-20 border-gray-300 rounded p-1 text-xs"
                   placeholder="ID">
        </div>

        <!-- Filter by Status -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">Stare</label>
            <select wire:model.defer="status"
                    class="w-24 border-gray-300 rounded p-1 text-xs">
                <option value="">Toate</option>
                <option value="1">Active</option>
                <option value="0">Arhivate</option>
            </select>
        </div>

        <!-- Filter by Category -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">Categorie</label>
            <select wire:model.defer="selectedCategory"
                    class="w-40 border-gray-300 rounded p-1 text-xs">
                <option value="">Toate</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }} - {{ $category->id }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by EAN -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">EAN</label>
            <select wire:model.defer="eanFilter"
                    class="w-24 border-gray-300 rounded p-1 text-xs">
                <option value="">Toate</option>
                <option value="1">Cu EAN</option>
                <option value="0">Fără EAN</option>
            </select>
        </div>


        <!-- Filter by pret -->
        <div class="flex flex-col text-xs">
            <label class="text-gray-600 mb-1">PRET</label>
            <select wire:model.defer="priceFilter" class="w-24 border-gray-300 rounded p-1 text-xs">
                <option value="">Toate</option>
                <option value="1">Cu PRET</option>
                <option value="0">Fără PRET</option>
            </select>
        </div>


        <!-- ✅ Apply Filters & Reset Filters Buttons -->
        <div class="flex gap-2">
            <button wire:click="applyFilters"
                    class="bg-green-500 text-white px-3 py-1 rounded shadow text-xs">
                Aplică filtrele
            </button>
            <a href="{{ route('admin.products') }}"
                    class="bg-gray-500 text-white px-3 py-1 rounded shadow text-xs">
                Resetează filtrele
            </a>
        </div>
    </div>
</div>
