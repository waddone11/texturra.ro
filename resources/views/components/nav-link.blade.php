@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'inline-flex items-center p-2 px-3 pt-2 text-xs font-extrabold leading-5'
                : 'inline-flex items-center p-2 px-3 pt-2 text-xs font-extrabold leading-5 hover:border-black focus:outline-none focus:border-black transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
