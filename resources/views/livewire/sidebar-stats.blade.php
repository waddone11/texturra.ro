<nav>
    @can('admin')
        <!-- Accordion for Mobile -->
        <div class="block md:hidden">
            <button id="menuToggle" class="w-full text-left block p-2 text-gray-700 hover:bg-gray-200 rounded bg-gray-50 border">
                Menu <span id="menuIcon" class="float-right mr-2 block md:hidden">+</span>
            </button>
            <ul id="mobileMenu" class="hidden md:space-y-2 border-b">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                        Categorii ({{ $categoryCount ?? '0' }})
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                        Produse ({{ $productCount ?? '0' }})
                    </a>
                </li>
{{--                <li>--}}
{{--                    <a href="{{ route('admin.products.emag') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">--}}
{{--                        Produse cu ean({{ $productCount ?? '0' }})--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li>
                    <a href="{{ route('admin.orders') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                        Comenzi ({{ $productCount ?? '0' }})
                    </a>
                </li>
                <li>
                <a href="{{ route('admin.change-password') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                    Resetare parola
                </a>
                </li>
            </ul>
        </div>

        <!-- Desktop Menu -->
        <ul class="hidden md:block space-y-2 border-r">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.categories') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                Categorii ({{ $categoryCount ?? '0' }})
            </a>
        </li>
        <li>
            <a href="{{ route('admin.products') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                Produse ({{ $productCount ?? '0' }})
            </a>
        </li>
{{--        <li>--}}
{{--            <a href="{{ route('admin.products.emag') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">--}}
{{--                Produse Emag({{ $productCountEmag ?? '0' }})--}}
{{--            </a>--}}
{{--        </li>--}}
        <li>
            <a href="{{ route('admin.orders') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                Comenzi
            </a>
        </li>
        <li>
            <a href="{{ route('admin.change-password') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                Resetare parola
            </a>
        </li>
    </ul>
    @endcan





</nav>

<script>
    document.getElementById('menuToggle').addEventListener('click', function () {
        const menu = document.getElementById('mobileMenu');
        const icon = document.getElementById('menuIcon');
        menu.classList.toggle('hidden');
        icon.textContent = menu.classList.contains('hidden') ? '+' : '-';
    });
</script>
