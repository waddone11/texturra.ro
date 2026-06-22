@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginare" class="flex items-center gap-1.5">
        @php
            $btn = 'h-[34px] min-w-[34px] rounded-[6px] border border-[#e5ddd2] bg-white px-2 text-[12px] text-[#665d52] transition-colors';
            $active = 'h-[34px] min-w-[34px] rounded-[6px] border border-[#b58a43] bg-[#b58a43] px-2 text-[12px] font-semibold text-white';
            $disabled = 'h-[34px] min-w-[34px] rounded-[6px] border border-[#e5ddd2] bg-white px-2 text-[12px] text-[#cfc5b7]';
        @endphp

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="{{ $disabled }}" aria-disabled="true">‹</span>
        @else
            <button type="button" class="{{ $btn }} hover:border-[#b58a43] hover:text-[#8c6529]" wire:click="previousPage" wire:loading.attr="disabled" rel="prev" aria-label="Pagina anterioară">‹</button>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="{{ $btn }} cursor-default">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="{{ $active }}" aria-current="page">{{ $page }}</span>
                    @else
                        <button type="button" class="{{ $btn }} hover:border-[#b58a43] hover:text-[#8c6529]" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button type="button" class="{{ $btn }} hover:border-[#b58a43] hover:text-[#8c6529]" wire:click="nextPage" wire:loading.attr="disabled" rel="next" aria-label="Pagina următoare">›</button>
        @else
            <span class="{{ $disabled }}" aria-disabled="true">›</span>
        @endif
    </nav>
@endif
