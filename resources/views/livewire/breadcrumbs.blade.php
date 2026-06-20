<div class="w-full bg-gray-100 overflow-x-auto pt-1 mt-2 md:mt-8">
    <nav class="flex max-w-7xl mx-auto px-4 md:px-0 py-0.5 text-sm breadcrumbs text-white whitespace-nowrap">
        @foreach ($breadcrumbs as $index => $breadcrumb)
            <a href="{{ $breadcrumb['url'] }}" class="text-black font-bold text-xs">
                {{ $breadcrumb['label'] }}
            </a>

            @if (!$loop->last)
                <span class="mx-2 text-black ml-2 mr-2 relative -top-0.5 textSuperSmall font-extrabold"> > </span>
            @endif
        @endforeach
    </nav>
</div>
