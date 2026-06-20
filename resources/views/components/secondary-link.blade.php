<!-- resources/views/components/primary-link.blade.php -->
<a {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 text-red-500 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
