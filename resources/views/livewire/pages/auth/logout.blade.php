<div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full text-start text-black">
            <x-dropdown-link>
                Log out
            </x-dropdown-link>
        </button>
    </form>
</div>
