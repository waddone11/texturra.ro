<div x-data="{ open: false }" class="relative">
    <!-- Trigger -->
    @if ($type === 'link')
        <!-- Link -->
        <a
            href="#"
            class="text-indigo-600 underline text-xs hover:text-indigo-800 transition"
            @click.prevent="open = !open"
        >
            {{ $title ?? __('home.connect_with_us') }}
        </a>
    @else
        <!-- Button -->
        <button
            class="bg-indigo-600 text-white px-4 py-2 text-xs rounded-full shadow-lg hover:bg-indigo-800 transition"
            @click="open = !open"
        >
            {{ $title ?? __('home.connect_with_us') }}
        </button>
    @endif

    <!-- Modal-like Background -->
    <div
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40"
        x-show="open"
        @click="open = false"
        x-cloak
    ></div>

    <!-- Centered Tooltip -->
    <div
        class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 bg-white rounded-lg shadow-lg z-50 shadow-xl text-center border-2 border-indigo-600 py-4"
        x-show="open"
        @click.away="open = false"
        x-cloak
    >
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            {{ $title ?? __('home.connect_with_us') }}
        </h3>
        <ul>
            @foreach ($links as $link)
                <li class="border-b last:border-none">
                    <a
                        href="{{ $link['url'] }}"
                        target="_blank"
                        class="block px-4 py-2 text-xs hover:underline text-left text-gray-800"
                    >
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        <button
            class="mt-4 bg-indigo-600 text-white px-4 py-2 text-xs rounded-full shadow-lg hover:bg-indigo-800 transition"
            @click="open = false"
        >
            {{ __('Close') }}
        </button>
    </div>
</div>
