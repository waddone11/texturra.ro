<div class="sm:flex-1 flex justify-center h-16 md:h-32 items-center px-0 py-0 md:p-0">
    <a href="{{ route('home') }}">
{{--        <img--}}
{{--            class="h-16 md:h-32 w-auto ml-2 md:ml-0 p-1 md:p-0"--}}
{{--            src="{{ request()->routeIs('home') ? asset('storage/images/logo_white.png') : asset('storage/images/logo_black.png') }}"--}}
{{--            alt="Logo"--}}
{{--        />--}}
        <img
            class="h-16 md:h-32 w-auto ml-2 md:ml-0 p-1 md:p-0"
            src="{{ asset('storage/images/logo_black.png') }}"
            alt="Logo"
        />
    </a>
</div>
