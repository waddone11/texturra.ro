@props(['title', 'eyebrow' => 'Informații'])

{{-- Reusable content-page shell: editorial header + readable prose column (≈760px),
     consistent with the 2026 redesign (ivory bg, Playfair title, DM Sans body, gold accent). --}}
<div class="w-full bg-[#FCFAF7] font-dm">
    <div class="mx-auto max-w-[760px] px-5 py-14 sm:px-6 md:py-20">
        <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B58A43]">{{ $eyebrow }}</p>
        <h1 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[40px]">{{ $title }}</h1>
        <div class="my-6 h-px w-12 bg-[#B58A43]"></div>
        <article class="prose prose-neutral max-w-none leading-relaxed text-[#3a342e]
                        prose-headings:font-display prose-headings:font-semibold prose-headings:text-[#171411]
                        prose-h2:mt-9 prose-h2:text-xl prose-p:my-3.5
                        prose-a:font-medium prose-a:text-[#8c6529] prose-strong:text-[#171411]
                        prose-li:my-1 prose-ul:my-3">
            {{ $slot }}
        </article>
    </div>
</div>
