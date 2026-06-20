<div class="p-0 md:py-0 md:mb-8">
    <nav x-data="{ open: false }" class="bg-nav">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto mt-0 md:mt-4 md:mt-0 md:px-4 sm:px-6 lg:px-0 bg-white md:mt-12 rounded-t md:border-0">
            <div class="flex justify-between items-end h-16 md:h-24">
                <!-- Left Block: Navigation Links -->
                <div class="sm:flex-1 flex justify-start h-16 md:h-24">
                    <div class="hidden sm:flex ml-6 space-x-4 uppercase items-center">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-black">
                            Home
                        </x-nav-link>
                        <x-nav-link
                            href="javascript:void(0);"
                            class="text-black uppercase font-bold cursor-pointer flex items-center"
                            @click="open = !open"
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
                                class="ml-2 h-4 w-4 transition-transform duration-300"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                            </svg>
                        </x-nav-link>
                        <x-nav-link :href="route('about')" :active="request()->routeIs('about')" class="text-black">
                            Despre noi
                        </x-nav-link>
                    </div>
                </div>

                <!-- Center Block: Logo -->
                <div class="sm:flex-1 flex justify-center h-16 md:h-24 items-center px-2 py-0 md:p-0">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-auto w-auto fill-current text-black" />
                    </a>
                </div>

                <!-- Right Block: Mobile Menu Button -->
                <span class="flex-1 flex justify-end h-16 md:h-24 items-top md:items-center md:pr-8 relative -top-1 md:top-0">

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
                        <a href="{{ route('login') }}" class="hidden sm:block text-black text-xs font-extrabold uppercase mr-2">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:block text-black text-xs font-extrabold uppercase mr-2">
                            Register
                        </a>
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
            <div class="max-w-7xl mx-auto bg-white shadow-md border border-gray-200 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 md:gap-2">
                <!-- First Column: Top-Level Categories -->
                <div class="p-4">
                    <ul>
                        @foreach ($topCategories as $topCategory)
                            <li>
                                <button
                                    @click="$wire.setActiveCategory({{ $topCategory->id }})"
                                    class="text-gray-700 text-sm font-semibold hover:underline hover:text-gray-900 transition text-left {{ $activeCategoryId == $topCategory->id ? 'font-bold text-blue-500' : '' }}"
                                >
                                    {{ $topCategory->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Remaining Columns: Subcategories -->
                <div class="col-span-3 p-4 border-l border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @if (!empty($subcategories))
                            @foreach ($subcategories as $subcategory)
                                <div>
                                    <a href="{{ route('products.category', ['slug' => $subcategory['slug']]) }}">
                                        <h5 class="text-xs font-bold underline">{{ $subcategory['name'] }}</h5>
                                    </a>
                                    @if (!empty($subcategory['children']))
                                        <ul class="pl-2">
                                            @foreach ($subcategory['children'] as $child)
                                                <li class="text-gray-700 text-xs hover:underline hover:text-gray-800">
                                                    <a href="{{ route('products.category', ['slug' => $child['slug']]) }}">
                                                        {{ $child['name'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-sm">Nici o categorie de produse disponibila.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </div>
    </nav>
</div>
