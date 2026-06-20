@php
    $cartData = [
        'length' => (float) $item->length,
        'height' => (float) $item->height,
        'pieces' => (int) $item->pieces,
        'manufactoring_type_id' => (int) $item->manufactoring_type_id,
        'max_height' => (float) optional($item->product)->height ?? 3.0,
    ];
@endphp

<form method="POST"
      action="{{ route('cart.update.custom', $item->id) }}"
      x-data="customCartFormComponent({{ json_encode($cartData) }})"
      class="w-full max-w-md space-y-3">
    @csrf
    @method('PATCH')

    <!-- Lățime + Înălțime -->
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="text-xs font-medium mb-1 block">Lățime (m)</label>
            <div class="flex items-center border border-black rounded-md overflow-hidden">
                <button type="button" class="w-12 h-8 flex justify-center items-center border-r border-black"
                        @click="length = Math.max(1, +(length - 0.1).toFixed(2))">−</button>
                <input type="number" x-model="length" name="length" step="0.1" min="1"
                       class="w-full text-center font-medium border-0 focus:outline-none p-0">
                <button type="button" class="w-12 h-8 flex justify-center items-center border-l border-black"
                        @click="length = Math.min(30, +(length + 0.1).toFixed(2))">+</button>
            </div>
        </div>

        <div>
            <label class="text-xs font-medium mb-1 block">Înălțime (m)</label>
            <div class="flex items-center border border-black rounded-md overflow-hidden">
                <button type="button" class="w-12 h-8 flex justify-center items-center border-r border-black"
                        @click="height = Math.max(0.5, +(height - 0.1).toFixed(2))">−</button>
                <input type="number" x-model="height" name="height" step="0.1" min="0.5" :max="maxHeight"
                       class="w-full text-center font-medium border-0 focus:outline-none p-0">
                <button type="button" class="w-12 h-8 flex justify-center items-center border-l border-black"
                        @click="height = Math.min(maxHeight, +(height + 0.1).toFixed(2))">+</button>
            </div>
        </div>
    </div>

    <!-- Manoperă + Bucăți -->
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="text-xs font-medium mb-1 block">Tip manoperă</label>
            <select x-model="manufactoringId" name="manufactoring_type_id"
                    class="w-full border border-black rounded-md text-sm py-1">
                @foreach ($manufactoringTypes as $type)
                    <option value="{{ $type->id }}">
                        {{ $type->name }} ({{ $type->price }} lei/m)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-medium mb-1 block">Bucăți</label>
            <select x-model="pieces" name="pieces"
                    class="w-full border border-black rounded-md text-sm py-1">
                <option value="1">1 bucată</option>
                <option value="2">2 bucăți</option>
            </select>
        </div>
    </div>

    <button type="submit"
            class="w-[150px] bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition text-xs px-1 border border-black">
        Actualizează
    </button>
</form>

<script>
    function customCartFormComponent(item) {
        return {
            length: item.length,
            height: item.height,
            maxHeight: item.max_height || 3.0,
            pieces: item.pieces,
            manufactoringId: item.manufactoring_type_id,
        }
    }
</script>
