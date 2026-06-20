<div class="{{ $layoutKey === 'details' ? 'h-10 w-10 mt-2' : 'absolute top-1 right-3 text-right h-12 w-12 z-30' }} overflow-hidden pb-1 pl-1">
    <button wire:click.prevent="toggleFavorite" class="z-30 text-red-400 p-1 pr-0"
            onclick="event.stopPropagation();">
        <i class="{{ $isFavorite ? 'fa-solid' : 'fa-regular' }} fa-heart {{ $layoutKey === 'details' ? 'fa-lg' : 'fa-md' }}"></i>
    </button>
</div>
