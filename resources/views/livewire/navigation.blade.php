<div class="">
    <nav x-data="{ open: false }"
         class="{{ request()->routeIs('home') ? 'bg-transparent absolute top-0 left-0 w-full z-50' : 'bg-nav relative' }}">

        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto mt-0 md:mt-4 md:mt-0 md:px-4 sm:px-6 lg:px-0 {{ request()->routeIs('home') ? 'bg-transparent': 'bg-white'}} md:mt-12 rounded-t md:border-0">
            <div class="flex justify-between items-end h-16 md:h-32">
                <!-- Left Block: Navigation Links -->
                <div class="sm:flex-1 flex justify-start h-16 md:h-32">
                    <div class="hidden sm:flex space-x-4 uppercase items-center">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="{{ request()->routeIs('home') ? 'bg-white rounded-2xl': ''}}">
                            Home
                        </x-nav-link>
                        <x-nav-link
                            href="javascript:void(0);"
                            class="text-black uppercase font-bold cursor-pointer flex items-center {{ request()->routeIs('home') ? 'bg-white rounded-2xl': ''}}"
                            @click="open = !open"
                            :active="request()->routeIs('products.category') || request()->routeIs('product.show')"

                        >
                            Produse
                            <!-- Arrow Icons -->
                            <svg
                                x-show="!open"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                x-cloak
                                class="ml-2 h-4 w-4 transition-transform duration-300"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg
                                x-show="open"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                x-cloak
                                class="ml-2 h-4 w-4 transition-transform duration-300"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                            </svg>
                        </x-nav-link>
                        <x-nav-link
                            :href="route('about')"
                            :active="request()->routeIs('about')"
                            class="text-black {{ request()->routeIs('home') ? 'bg-white rounded-2xl': ''}}">
                            Despre noi
                        </x-nav-link>
                    </div>
                </div>

                <!-- Center Block: Logo -->
                <div class="sm:flex-1 flex justify-center h-16 md:h-32 items-center px-0 py-0 md:p-0">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-auto w-auto fill-current text-black" />
                    </a>
                </div>

                <!-- Right Block: Mobile Menu Button -->
                <span class="flex-1 flex justify-end h-16 md:h-32 items-top md:items-center md:pr-8 relative -top-1 md:top-0">

                    <livewire:favorites-count />

                    <livewire:cart-widget-menu />

                    <!-- Mobile Menu Button -->
                    @auth
                        <x-dropdown align="right" width="48" class="hidden sm:flex" contentClasses="bg-white text-sm">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 md:px-3 py-1 md:py-2 border border-black text-black text-xs font-extrabold rounded-md bg-transparent hover:bg-gray-200 focus:outline-none transition ease-in-out duration-150 mt-5 md:mt-0">
                                    <div>
                                        <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                        <span class="md:hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25c3.038 0 5.5-2.462 5.5-5.5S15.038 3.25 12 3.25 6.5 5.712 6.5 8.75s2.462 5.5 5.5 5.5ZM4.75 20.25a7.25 7.25 0 0 1 14.5 0"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a 1 1 0 01-1.414 0l-4-4a 1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('admin')
                                    <x-dropdown-link :href="route('admin.dashboard')" class="text-black hover:text-gray-700">
                                        Admin dashboard
                                    </x-dropdown-link>
                                @endcan
                                @can('client')
                                    <x-dropdown-link :href="route('account.index')" class="text-black hover:text-gray-700">
                                        My Account
                                    </x-dropdown-link>
                                @endcan

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-start text-black">
                                        <x-dropdown-link class="text-black hover:text-gray-700">
                                            Logout
                                        </x-dropdown-link>
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else

                        <x-nav-link
                            :href="route('login')"
                            :active="request()->routeIs('login')"
                            class="hidden md:block text-black {{ request()->routeIs('home') ? 'bg-white rounded-2xl': ''}} mr-3">
                            LOGIN
                        </x-nav-link>

                        <x-nav-link
                            :href="route('register')"
                            :active="request()->routeIs('register')"
                            class="hidden md:block  text-black {{ request()->routeIs('home') ? 'bg-white rounded-2xl': ''}} mr-3">
                            CONT NOU
                        </x-nav-link>
                    @endauth

                    <div class="sm:hidden flex items-center relative top-1 md:top-3">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-black hover:text-black focus:outline-none transition">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
            </div>
        </div>
        <!-- Categories and Subcategories -->
        <div class="absolute w-full z-50"
             x-show="open"
             x-transition
             @click.away="open = false"
             x-cloak
        >
            <div class="max-w-7xl mx-auto py-2 px-2 md:py-8 md:px-8 md:mt-4 bg-white md:shadow-xl md:rounded-xl z-100 overflow-auto">
                <!-- Display parent categories in a grid of 3 columns -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-1 md:gap-3">
                    @php
                        // Map each parent category name to its corresponding icon URL.
                        $categoryIcons = [
                            'Perdele' => asset('storage/images/icons/perdele.png'),
                            'Draperii' => asset('storage/images/icons/draperii.png'),
                            'Covoare' => asset('storage/images/icons/covoare.png'),
                            'Lenjerii de pat' => asset('storage/images/icons/lenjerii.png'),
                            'Accesorii' => asset('storage/images/icons/accesorii.png'),
                            'Galerii & Sine' => asset('storage/images/icons/sine.png'),


                        ];
                        $defaultIcon = asset('storage/images/icons/default.png');
                    @endphp
                    @foreach ($topCategories as $topCategory)

                        <div class="flex flex-col md:flex-row md:items-start md:space-x-4 space-y-2 md:space-y-0 md:p-1 text-center md:text-left">
                            <!-- Icon -->
                            <div class="flex justify-center md:items-start">
                                <img
                                    src="{{ $categoryIcons[$topCategory->name] ?? $defaultIcon }}"
                                    alt="{{ $topCategory->name }}"
{{--                                    class="w-20 p-1 object-contain mx-auto transition-transform duration-300 group-hover:scale-110 bg-gray-50 rounded-full shadow-md border border-gray-200"--}}
                                    class="object-contain w-16 h-16 md:w-16 md:h-16"--}}
                                />
                            </div>

                            <!-- Category Name & Subcategories -->
                            <div class="flex flex-col items-center md:items-start">
                                <h3 class="text-sm font-bold uppercase mt-2">
                                    <a href="{{ route('products.category', ['slug' => $topCategory->slug]) }}"
                                       class="hover:underline text-black">
                                        {{ $topCategory->name }}
                                    </a>
                                </h3>

{{--                                @if (!empty($subcategories[$topCategory->id]))--}}
{{--                                    <ul class="mt-1 space-y-0.5">--}}
{{--                                        @foreach ($subcategories[$topCategory->id] as $child)--}}
{{--                                            <li class="text-xs text-gray-700 hover:underline">--}}
{{--                                                <a href="{{ route('products.category', ['slug' => $child['slug']]) }}">--}}
{{--                                                    {{ $child['name'] }}--}}
{{--                                                </a>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </nav>
</div>
