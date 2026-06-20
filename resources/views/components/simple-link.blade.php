<!-- resources/views/components/primary-link.blade.php -->
<a {{ $attributes->merge(['class' => 'text-xs inline-flex items-right px-0 py-0 underline text-black rounded-md font-semibold uppercase tracking-widest hover:shadow-xl focus:outline-none focus:ring-0 focus:shadow-lg transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
