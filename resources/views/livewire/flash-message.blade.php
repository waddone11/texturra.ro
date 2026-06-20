<div class="fixed top-4 right-4 z-50 w-96"
     x-data="{ show: @entangle('show'), message: @entangle('message'), type: @entangle('type') }">
    <div x-show="show"
         x-init="
            $watch('show', value => {
                if (value) {
                    setTimeout(() => show = false, 10000); // Visible for 10 seconds
                }
            });
         "
         x-transition
         class="px-4 py-2 rounded-lg shadow-lg text-white"
         :class="{
            'bg-green-500': type === 'success',
            'bg-red-500': type === 'error',
            'bg-yellow-500': type === 'warning',
            'bg-blue-500': type === 'info',
            'bg-gray-700': type === 'default'
         }">
        <span x-text="message" class="text-sm"></span>
    </div>
</div>
