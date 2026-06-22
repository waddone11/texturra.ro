<div>
    <section class="bg-white mt-8">
        <div class="max-w-(--breakpoint-xl) px-4 py-12 mx-auto space-y-8 overflow-hidden sm:px-6 lg:px-8">
            <nav class="flex flex-wrap justify-left md:justify-center -mx-5 -my-2">
                <div class="px-5 py-2">
                    <script src="https://mny.ro/npId.js?p=143929" type="text/javascript" data-version="horizontal" data-contrast-color="#ffffff" ></script>
                </div>
                <div class="px-5 py-2">
                    <h3 class="text-black font-bold">Info utile</h3>
                    <ul>
                        <li>
                            <a href="{{ route('politica-livrare') }}" class="text-base text-xs text-black ">
                                Politica livrare
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('politica-retur') }}" class="text-base text-xs text-black ">
                                Politica retur
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('politica-confidentialitate') }}" class="text-base text-xs text-black ">
                                Politica confidentialitate
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('politica-gdpr') }}" class="text-base text-xs text-black ">
                                Politica GDPR
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('termeni-conditii') }}" class="text-base text-xs text-black ">
                                Termeni si conditii
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sitemap') }}" class="text-base text-xs text-black ">
                                Harta site
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="px-5 py-2">
                    <h3 class="text-black font-bold">Clienti</h3>
                    <ul>
                        <li><a href="https://anpc.ro/" target="_blank" class="custom-color">www.anpc.gov.ro</a></li>
                    </ul>
                    <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener"><img alt="Soluționarea online a litigiilor" src="{{ asset('storage/images/solutii.webp') }}" class="h-12"></a>
                    <a href="https://anpc.ro/ce-este-sal/" target="_blank" rel="noopener"><img alt="Soluționarea alternativă a litigiilor" src="{{ asset('storage/images/anpc.webp') }}" class="h-12"></a>
                </div>
                <div class="px-5 py-2">
                    <h3 class="text-black font-bold">Contact</h3>
                    <ul class="text-xs text-black">
                        <li>Companie: {{ config('app.store_owner.name') }}</li>
                        <li>Adresa Legală: {{ config('app.store_owner.legal_address') }}</li>
                        <li>Nr. Înregistrare: {{ config('app.store_owner.registration_number') }}</li>
                        <li>Cod Unic: {{ config('app.store_owner.unique_code') }}</li>
                        <li>Cod CAEN: {{ config('app.store_owner.caen_code') }}</li>
                        <li>Telefon: {{ config('app.store_owner.phone') }}</li>
                        <li>IBAN: {{ config('app.store_owner.iban') }}</li>
                        <li>Bancă: {{ config('app.store_owner.bank') }}</li>
                    </ul>
                </div>


            </nav>
            <div class="flex justify-center mt-8 space-x-6">
                <a href="https://www.facebook.com/www.texturra.ro" target="_blank" rel="noopener" class="text-gray-400 hover:text-black">
                    <span class="sr-only">Facebook</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="https://www.instagram.com/texturra_home/" target="_blank" rel="noopener" class="text-gray-400 hover:text-black">
                    <span class="sr-only">Instagram</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="https://www.tiktok.com/@texturra_home" target="_blank" rel="noopener" class="text-gray-400 hover:text-black">
                    <span class="sr-only">Tik Tok</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9.145 21.912c-3.713-.308-6.658-3.418-6.658-7.196 0-3.974 3.23-7.204 7.204-7.204.208 0 .414.009.618.025v2.706a4.496 4.496 0 0 0-.618-.043c-2.479 0-4.497 2.018-4.497 4.497 0 2.099 1.444 3.878 3.398 4.372v2.62zm9.71-16.73a4.44 4.44 0 0 0 3.344 1.549v2.594a7.236 7.236 0 0 1-4.497-1.571v7.842c0 3.975-3.229 7.204-7.204 7.204v-2.62a4.497 4.497 0 0 0 4.497-4.497V3.383h3.86v1.799z"/>
                    </svg>
                </a>
            </div>
            <p class="mt-8 text-base leading-6 text-center text-gray-400">
                &copy; {{ config('app.name') }}. Toate drepturile rezervate.
            </p>
        </div>
    </section>
</div>

