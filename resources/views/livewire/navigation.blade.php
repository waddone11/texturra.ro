@php
    // Pages that render a full-bleed dark hero behind the nav: the live homepage
    // ("/" → home-old video hero) and the redesign preview ("/home-preview").
    // On these the nav floats transparent (cream text) and turns solid ivory on scroll.
    // Everywhere else it is a solid ivory bar with dark text (readable, no hero behind).
    $onHero = request()->routeIs('home') || request()->routeIs('home.preview');
    $activeSlug = request()->route('slug');
@endphp

<div>
    <nav
        x-data="{ open: false, scrolled: false }"
        x-init="scrolled = (window.pageYOffset || document.documentElement.scrollTop) > 90"
        @scroll.window="scrolled = (window.pageYOffset || document.documentElement.scrollTop) > 90"
        @if ($onHero)
            class="fixed inset-x-0 top-0 z-50 font-dm backdrop-blur-md transition-colors duration-300"
            :class="scrolled ? 'bg-[#FCFAF7]/85 text-[#171411] shadow-[0_1px_0_rgba(23,20,17,0.08)]' : 'bg-black/10 text-[#FCFAF7]'"
        @else
            class="relative z-50 font-dm border-b border-[#171411]/10 bg-[#FCFAF7]/90 text-[#171411] backdrop-blur-md"
        @endif
    >
        @if ($onHero)
            <!-- Readability scrim behind the transparent nav; fades out once the bar turns solid on scroll -->
            <div class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-b from-black/45 to-transparent transition-opacity duration-300"
                 :class="scrolled ? 'opacity-0' : 'opacity-100'"></div>
        @endif
        <!-- Utility bar (thin dark strip) -->
        <div class="relative z-10 hidden md:block bg-[#171411] text-[#FCFAF7]/90">
            <div class="mx-auto flex h-9 max-w-7xl items-center justify-between gap-4 px-4 text-[11px] tracking-[0.06em] lg:px-8">
                <div class="flex items-center gap-6">
                    <span><i class="fa-solid fa-truck mr-2 text-[#B28D4E]"></i>Transport gratuit peste 500 lei</span>
                    <span><i class="fa-solid fa-scissors mr-2 text-[#B28D4E]"></i>Mostre în 24–48h</span>
                    <span class="hidden lg:inline"><i class="fa-solid fa-comments mr-2 text-[#B28D4E]"></i>Consultanță gratuită</span>
                </div>
                <a href="{{ route('about') }}" class="flex items-center gap-2 transition-colors hover:text-[#B28D4E]">
                    <i class="fa-solid fa-location-dot text-[#B28D4E]"></i>
                    Showroom-uri Ploiești
                </a>
            </div>
        </div>

        <!-- Main row -->
        <div class="relative z-10 mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:h-20 lg:px-8">
            <!-- Left: real logo (white over hero, dark on solid pages) -->
            <div class="flex flex-1 items-center">
                <a href="{{ route('home') }}" aria-label="TEXTURRA — acasă">
                    @if ($onHero)
                        {{-- White logo while transparent over the hero; dark logo once the bar turns solid on scroll --}}
                        <img src="{{ asset('storage/images/logo_alb.svg') }}" alt="TEXTURRA"
                             class="h-11 w-auto md:h-14" x-show="!scrolled" />
                        <img src="{{ asset('storage/images/logo_negru.svg') }}" alt="TEXTURRA"
                             class="h-11 w-auto md:h-14" x-show="scrolled" x-cloak />
                    @else
                        <img src="{{ asset('storage/images/logo_negru.svg') }}" alt="TEXTURRA"
                             class="h-11 w-auto md:h-14" />
                    @endif
                </a>
            </div>

            <!-- Center: category menu (desktop) -->
            <div class="hidden items-center justify-center gap-x-6 text-[12.5px] uppercase tracking-[0.12em] lg:flex">
                @foreach ($topCategories as $cat)
                    <a href="{{ route('products.category', ['slug' => $cat->slug]) }}"
                       class="py-2 transition-colors hover:text-[#B28D4E] {{ $activeSlug === $cat->slug ? 'text-[#B28D4E]' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
                <a href="{{ route('about') }}"
                   class="py-2 transition-colors hover:text-[#B28D4E] {{ request()->routeIs('about') ? 'text-[#B28D4E]' : '' }}">
                    Despre noi
                </a>
            </div>

            <!-- Right: actions -->
            <div class="flex flex-1 items-center justify-end gap-4 md:gap-5">
                <!-- Search -->
                <button type="button" @click="$dispatch('open-search-modal')"
                        class="transition-colors hover:text-[#B28D4E]" aria-label="Caută">
                    <i class="fa-solid fa-magnifying-glass fa-lg"></i>
                </button>

                <!-- Account -->
                @auth
                    <x-dropdown align="right" width="48" contentClasses="bg-white text-sm">
                        <x-slot name="trigger">
                            <button class="transition-colors hover:text-[#B28D4E]" aria-label="Contul meu">
                                <i class="fa-regular fa-user fa-lg"></i>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="border-b border-gray-100 px-4 py-2 text-xs text-gray-500">{{ auth()->user()->name }}</div>
                            @can('admin')
                                <x-dropdown-link :href="route('filament.admin.pages.dashboard')" class="text-[#171411] hover:text-[#B28D4E]">
                                    Admin dashboard
                                </x-dropdown-link>
                            @endcan
                            @can('client')
                                <x-dropdown-link :href="route('account.index')" class="text-[#171411] hover:text-[#B28D4E]">
                                    Contul meu
                                </x-dropdown-link>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-start">
                                    <x-dropdown-link class="text-[#171411] hover:text-[#B28D4E]">
                                        Logout
                                    </x-dropdown-link>
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="transition-colors hover:text-[#B28D4E]" aria-label="Autentificare">
                        <i class="fa-regular fa-user fa-lg"></i>
                    </a>
                @endauth

                <!-- Favorites (Livewire — logic preserved) -->
                <livewire:favorites-count />

                <!-- Cart (Livewire — logic preserved) -->
                <livewire:cart-widget-menu />

                <!-- Mobile menu toggle -->
                <button @click="open = !open" class="transition-colors hover:text-[#B28D4E] lg:hidden" aria-label="Meniu">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile panel -->
        <div x-show="open" x-cloak x-transition.origin.top
             @click.away="open = false"
             class="border-t border-[#171411]/10 bg-[#FCFAF7] text-[#171411] lg:hidden">
            <div class="mx-auto max-w-7xl px-4 py-4">
                <p class="px-1 pb-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#B28D4E]">Categorii</p>
                <div class="flex flex-col">
                    @foreach ($topCategories as $cat)
                        <a href="{{ route('products.category', ['slug' => $cat->slug]) }}"
                           class="border-b border-[#171411]/5 py-3 text-sm uppercase tracking-[0.08em] hover:text-[#B28D4E]">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                    <a href="{{ route('about') }}"
                       class="border-b border-[#171411]/5 py-3 text-sm uppercase tracking-[0.08em] hover:text-[#B28D4E]">
                        Despre noi
                    </a>
                </div>

                <div class="mt-4 flex flex-col gap-2 text-sm">
                    @auth
                        @can('admin')
                            <a href="{{ route('filament.admin.pages.dashboard') }}" class="py-1 hover:text-[#B28D4E]">Admin dashboard</a>
                        @endcan
                        @can('client')
                            <a href="{{ route('account.index') }}" class="py-1 hover:text-[#B28D4E]">Contul meu</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="py-1 text-start hover:text-[#B28D4E]">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="py-1 hover:text-[#B28D4E]">Autentificare</a>
                        <a href="{{ route('register') }}" class="py-1 hover:text-[#B28D4E]">Cont nou</a>
                    @endauth
                </div>

                <div class="mt-4 border-t border-[#171411]/10 pt-3 text-[11px] tracking-[0.05em] text-[#171411]/70">
                    <i class="fa-solid fa-location-dot mr-2 text-[#B28D4E]"></i>Showroom-uri Ploiești ·
                    <i class="fa-solid fa-truck mx-2 text-[#B28D4E]"></i>Transport gratuit peste 500 lei
                </div>
            </div>
        </div>
    </nav>

    <!-- Product search modal (listens for the open-search-modal event dispatched above) -->
    <livewire:search-modal />
</div>
