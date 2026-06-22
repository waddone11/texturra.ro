@extends('layouts.base')

@section('content')
    <x-static-page title="Politica de Retur" eyebrow="Politici">
        <p>
            Pentru a asigura satisfacția clienților noștri, vă oferim posibilitatea de a returna produsele achiziționate în termen de 30 de zile calendaristice de la primirea acestora, în conformitate cu prevederile legale privind protecția consumatorului (OUG 34/2014).
        </p>
        <p>
            <strong>Condiții Generale pentru Returnare</strong><br/>
            Produsele trebuie returnate în ambalajul original, împreună cu toate accesoriile și documentele aferente (factură, certificat de garanție etc.).
            Produsele returnate trebuie să fie în aceeași stare în care au fost recepționate, fără urme de utilizare, deteriorare sau modificări.
        </p>
        <p>
            <strong>Produse Exceptate de la Retur</strong><br/>
            Conform legislației în vigoare, există anumite categorii de produse care nu pot fi returnate:
        </p>
        {{-- TODO review uman (juridic): formularea excepției pentru produse personalizate/la comandă
             se bazează pe excepțiile din OUG 34/2014 pentru bunuri confecționate după specificațiile
             clientului; confirmați cu consilierul juridic înainte de lansare. --}}
        <p>
            Produse confecționate la comandă, pe dimensiunile și specificațiile solicitate de client (perdele și draperii croite la măsură) — fiind produse personalizate, acestea sunt exceptate de la dreptul de retur, conform excepțiilor prevăzute de OUG 34/2014 pentru bunurile realizate după cerințele consumatorului.
            Produse care au fost utilizate, spălate, croite sau modificate de către client, ori cărora le lipsește ambalajul original.
        </p>
        <p>
            <strong>Pași pentru Returnare</strong><br/>
            Contactați echipa noastră de suport clienți pentru a înregistra solicitarea de retur. Asigurați-vă că aveți factura și datele comenzii la îndemână.
            Ambalați produsul în ambalajul original, asigurându-vă că este protejat corespunzător pentru transport.
            Expediți produsul la adresa indicată de către echipa noastră. Costurile de transport pentru retur sunt suportate de către client, exceptând cazurile în care produsul prezintă defecte de fabricație sau a fost livrat greșit.
            După primirea și verificarea produsului, vă vom rambursa suma achitată sau, la cerere, vom oferi un produs în schimb.
        </p>
        <p>
            <strong>Rambursarea Banilor</strong><br/>
            Rambursarea contravalorii produsului se face în termen de 14 zile calendaristice de la primirea produsului returnat, prin aceeași metodă de plată utilizată la achiziție, exceptând cazurile în care se convine altfel.
        </p>
        <p>
            <strong>Aspecte Legale</strong><br/>
            Conform OUG 34/2014, consumatorii au dreptul de a se retrage din contractul de vânzare la distanță fără a invoca un motiv, în termen de 14 zile calendaristice. Totuși, noi extindem acest termen la 30 de zile pentru a vă oferi o experiență mai bună.
        </p>
        <p>
            Pentru orice întrebări suplimentare legate de politica de retur, vă rugăm să ne contactați. Ne dorim să asigurăm o experiență de cumpărare plăcută și transparentă pentru toți clienții noștri!
        </p>
    </x-static-page>
@endsection
