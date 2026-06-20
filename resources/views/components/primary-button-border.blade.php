<button {{ $attributes->merge([
    'type'  => 'submit',
    'class' => 'inline-flex items-center px-2 py-1 border bg-green-500 shadow-xl text-white rounded-md font-semibold text-xs dark:text-gray-800 uppercase tracking-widest dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
