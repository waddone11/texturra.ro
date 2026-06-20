<nav>
    <!-- Mobile Menu -->
    <div class="block md:hidden">
        <button id="menuToggle" class="w-full text-left block p-2 text-gray-700 hover:bg-gray-200 rounded bg-gray-50 border">
            Menu <span id="menuIcon" class="float-right mr-2 block md:hidden">+</span>
        </button>
        <ul id="mobileMenu" class="hidden md:space-y-2 border-b">
            <li>
                <a href="{{ route('account.index') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                    {{ __('Dashboard') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.change-password') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                    {{ __('Resetare parola') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.my-orders') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                    {{ __('Comenzile mele') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Desktop Menu -->
    <ul class="hidden md:block space-y-2 border-r">
        <li>
            <a href="{{ route('account.index') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                {{ __('Dashboard') }}
            </a>
        </li>
        <li>
            <a href="{{ route('account.change-password') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                {{ __('Resetare parola') }}
            </a>
        </li>
        <li>
            <a href="{{ route('account.my-orders') }}" class="block p-2 text-gray-700 hover:bg-gray-200 rounded text-xs md:text-sm">
                {{ __('Comenzile mele') }}
            </a>
        </li>
    </ul>
</nav>

<script>
    document.getElementById('menuToggle').addEventListener('click', function () {
        const menu = document.getElementById('mobileMenu');
        const icon = document.getElementById('menuIcon');
        menu.classList.toggle('hidden');
        icon.textContent = menu.classList.contains('hidden') ? '+' : '-';
    });
</script>
