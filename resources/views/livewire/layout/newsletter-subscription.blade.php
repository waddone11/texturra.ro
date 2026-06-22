<?php /* Section 9 restyle: editorial dark band. Subscribe LOGIC UNCHANGED — same
   wire:submit.prevent="subscribe", wire:model.defer="email", $mode toggle, flashMessage feedback. */ ?>
<section aria-label="Newsletter" class="w-full bg-[#171411] font-dm text-[#FCFAF7]">
    <div class="mx-auto max-w-[1180px] px-5 py-16 sm:px-8 md:py-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B28D4E]">Newsletter</p>
            <h2 class="font-display text-3xl font-semibold leading-tight md:text-[40px]">
                Abonează-te la newsletter
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-[#FCFAF7]/70">
                Primești 10% reducere la prima comandă. Fii la curent cu noutățile, ofertele exclusive și lansările de colecții TEXTURRA.
            </p>

            <form wire:submit.prevent="subscribe" class="mx-auto mt-8 flex w-full max-w-md flex-col gap-3 sm:flex-row">
                <input type="email" wire:model.defer="email" required
                       placeholder="Adresa ta de email"
                       class="w-full flex-1 rounded-md border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 transition-colors focus:border-[#B28D4E] focus:outline-none focus:ring-1 focus:ring-[#B28D4E]" />
                <button type="submit"
                        class="shrink-0 rounded-md bg-[#B28D4E] px-7 py-3 text-[13px] font-semibold uppercase tracking-[0.1em] text-[#171411] transition-colors hover:bg-[#FCFAF7]">
                    {{ $mode === 'subscribe' ? 'Mă abonez' : 'Mă dezabonez' }}
                </button>
            </form>

            <div class="mt-4 text-xs text-[#FCFAF7]/55">
                <button type="button"
                        wire:click="$set('mode', '{{ $mode === 'subscribe' ? 'unsubscribe' : 'subscribe' }}')"
                        class="underline underline-offset-2 transition-colors hover:text-[#B28D4E]">
                    {{ $mode === 'subscribe' ? 'Vrei să te dezabonezi?' : 'Vrei să te reabonezi?' }}
                </button>
            </div>

            <div class="mt-10 flex flex-col items-center justify-center gap-4 text-[11px] uppercase tracking-[0.12em] text-[#FCFAF7]/60 sm:flex-row sm:gap-10">
                @foreach ([
                    ['t' => 'Oferte exclusive',        'i' => '<path d="M20.59 13.41 13.42 20.6a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7" cy="7" r="1.2"/>'],
                    ['t' => 'Inspirație & noutăți',    'i' => '<path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><circle cx="12" cy="12" r="3"/>'],
                    ['t' => 'Lansări în avanpremieră', 'i' => '<path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7a3 3 0 1 0-3-3c0 2 3 3 3 3Zm0 0a3 3 0 1 1 3-3c0 2-3 3-3 3Z"/>'],
                ] as $perk)
                    <span class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#B28D4E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $perk['i'] !!}</svg>
                        {{ $perk['t'] }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</section>
