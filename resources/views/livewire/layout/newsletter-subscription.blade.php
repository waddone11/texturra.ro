<section class="bg-black text-white py-12">
    <div class="max-w-4xl mx-auto text-center px-6">
        <h2 class="text-xl md:text-2xl font-light tracking-widest uppercase mb-4">
            Abonează-te la newsletter
        </h2>
        <p class="text-sm md:text-base mb-6">
            Primești 10% reducere la prima comandă. Fii la curent cu noutățile, ofertele exclusive și lansările de colecții TEXTURRA.
        </p>


        <form wire:submit.prevent="subscribe" class="flex flex-col sm:flex-row justify-center items-center gap-3">
            <input type="email" wire:model.defer="email" required
                   placeholder="Email"
                   class="px-4 py-2 w-full sm:w-64 text-black  border border-white bg-black focus:ring-2 focus:ring-[#56bcbf]">
            <button type="submit"
                    class="uppercase tracking-widest bg-white text-black px-6 py-2 transition">
                {{ $mode === 'subscribe' ? 'Abonează-te' : 'Dezabonează-te' }}
            </button>
        </form>

        <div class="text-xs mt-4 text-white text-center">
            <button wire:click="$set('mode', '{{ $mode === 'subscribe' ? 'unsubscribe' : 'subscribe' }}')" class="underline">
                {{ $mode === 'subscribe' ? 'Vrei să te dezabonezi?' : 'Vrei să te reabonezi?' }}
            </button>
        </div>
    </div>
</section>
