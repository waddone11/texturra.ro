<div x-data="{ show: false, message: '', type: 'success' }"
     x-init="
        @if (session('flashMessage'))
            console.log('Session Flash Message:', @js(session('flashMessage')));
            message = @js(session('flashMessage')['message']);
            type = @js(session('flashMessage')['type']);
            show = true;
            setTimeout(() => show = false, 35000);
        @endif
     "
     x-on:flash-message.window="
        console.log('Flash Message Event:', $event.detail);
        message = $event.detail.message;
        type = $event.detail.type;
        show = true;
        setTimeout(() => show = false, 35000);
     "
     x-show="show"
     x-cloak
     class="fixed top-4 right-4 z-50 shadow-lg rounded-lg overflow-hidden w-96"
     :class="{
         'bg-green-500': type === 'success',
         'bg-red-500': type === 'error'
     }">
    <div class="px-4 py-2 rounded-lg shadow-lg text-black"
         :class="{
            'bg-green-500': type === 'success',
            'bg-red-500': type === 'error'
         }">
        <span x-text="message" class="text-black font-bold text-md"></span>
    </div>
</div>
