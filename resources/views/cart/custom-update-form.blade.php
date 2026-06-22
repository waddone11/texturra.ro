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
            <label class="mb-1 block text-[11px] font-semibold text-[#5f594f]">Lățime (m)</label>
            <div class="flex items-center overflow-hidden rounded-md border border-[#171411]/25">
                <button type="button" class="flex h-9 w-9 items-center justify-center border-r border-[#171411]/15 text-[#171411] hover:bg-[#f1ece4]"
                        @click="length = Math.max(1, +(length - 0.1).toFixed(2))">−</button>
                <input type="number" x-model="length" name="length" step="0.1" min="1"
                       class="h-9 w-full border-0 p-0 text-center text-sm font-medium focus:outline-none focus:ring-0">
                <button type="button" class="flex h-9 w-9 items-center justify-center border-l border-[#171411]/15 text-[#171411] hover:bg-[#f1ece4]"
                        @click="length = Math.min(30, +(length + 0.1).toFixed(2))">+</button>
            </div>
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold text-[#5f594f]">Înălțime (m)</label>
            <div class="flex items-center overflow-hidden rounded-md border border-[#171411]/25">
                <button type="button" class="flex h-9 w-9 items-center justify-center border-r border-[#171411]/15 text-[#171411] hover:bg-[#f1ece4]"
                        @click="height = Math.max(0.5, +(height - 0.1).toFixed(2))">−</button>
                <input type="number" x-model="height" name="height" step="0.1" min="0.5" :max="maxHeight"
                       class="h-9 w-full border-0 p-0 text-center text-sm font-medium focus:outline-none focus:ring-0">
                <button type="button" class="flex h-9 w-9 items-center justify-center border-l border-[#171411]/15 text-[#171411] hover:bg-[#f1ece4]"
                        @click="height = Math.min(maxHeight, +(height + 0.1).toFixed(2))">+</button>
            </div>
        </div>
    </div>

    <!-- Manoperă + Bucăți -->
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="mb-1 block text-[11px] font-semibold text-[#5f594f]">Tip manoperă</label>
            <select x-model="manufactoringId" name="manufactoring_type_id"
                    class="w-full rounded-md border border-[#171411]/25 py-1.5 text-sm focus:border-[#B58A43] focus:ring-0">
                @foreach ($manufactoringTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->price }} lei/m)</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold text-[#5f594f]">Bucăți</label>
            <select x-model="pieces" name="pieces"
                    class="w-full rounded-md border border-[#171411]/25 py-1.5 text-sm focus:border-[#B58A43] focus:ring-0">
                <option value="1">1 bucată</option>
                <option value="2">2 bucăți</option>
            </select>
        </div>
    </div>

    <button type="submit"
            class="inline-flex h-9 items-center rounded-md border border-[#171411]/25 px-4 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#171411] transition-colors hover:border-[#B58A43] hover:text-[#8c6529]">
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
