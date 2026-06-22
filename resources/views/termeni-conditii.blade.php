@extends('layouts.base')

@section('content')
    <x-static-page title="Termeni și Condiții" eyebrow="Legal">
        <p>
            Vă mulțumim că ați ales să utilizați serviciile noastre. Acești termeni și condiții reglementează utilizarea site-ului nostru și plasarea comenzilor online.
        </p>
        <p>
            <strong>Utilizarea site-ului</strong><br/>
            Accesarea și utilizarea acestui site implică acceptarea termenilor și condițiilor specificate aici. Ne rezervăm dreptul de a actualiza termenii și condițiile fără notificare prealabilă.
        </p>
        <p>
            <strong>Condiții de vânzare</strong><br/>
            - Toate comenzile plasate pe site sunt supuse disponibilității produselor;<br/>
            - Prețurile afișate includ TVA și alte taxe aplicabile;<br/>
            - Livrarea comenzilor este realizată conform politicii de livrare.
        </p>
        <p>
            <strong>Responsabilitatea utilizatorului</strong><br/>
            Utilizatorii sunt responsabili pentru acuratețea informațiilor furnizate la plasarea comenzilor și pentru menținerea confidențialității datelor de autentificare.
        </p>
        <p>
            <strong>Politica de returnare</strong><br/>
            Produsele pot fi returnate conform politicii noastre de retur detaliate <a href="{{ route('politica-retur') }}">aici</a>.
        </p>
        <p>
            <strong>Limitarea răspunderii</strong><br/>
            Nu suntem răspunzători pentru eventualele întârzieri sau erori în livrarea comenzilor din motive independente de controlul nostru (de exemplu, condiții meteorologice, pandemii, etc.).
        </p>
        <p>
            <strong>Legislație aplicabilă</strong><br/>
            Acești termeni și condiții sunt guvernați de legislația din România. Orice dispută va fi soluționată pe cale amiabilă, iar în caz de eșec, prin instanțele competente.
        </p>
    </x-static-page>
@endsection
