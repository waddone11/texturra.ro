@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-white text-start text-xs font-medium focus:outline-none focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-white transition duration-150 ease-in-out'
                : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-xs font-medium hover:text-white hover:bg-black hover:border-white focus:outline-none transition duration-150 ease-in-out';

    if(request()->routeIs('home')) {
        $classes .= 'text-white';
    } else {
        $classes .= 'text-black';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
