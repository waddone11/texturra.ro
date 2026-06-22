# TEXURRA — Specificație design pentru pagina de produs

## Direcție

Pagina de produs trece dintr-o structură de configurator tehnic, densă și cu ierarhie slabă, într-un **luxury product configurator**: fotografie mare, titlu editorial, configurare clară pe pași și un rezumat de preț permanent vizibil. Obiectivul este ca produsul să fie perceput ca o piesă premium de interior, fără să ascundem regulile de comandă la metru.

## 1. Header și breadcrumb

- Bară superioară dark cu cele patru promisiuni de serviciu: transport, mostre, consultanță, showroom-uri.
- Header ivory, aerisit, cu logo, categorii, search și acțiunile de cont.
- Breadcrumb îngust, deasupra conținutului; utilizatorul înțelege instant traseul: Acasă → Draperii → Blackout → produs.

## 2. Zona principală produs

Pe desktop, layout-ul este în două coloane:

- media galerie: ~48%;
- detaliu + configurator: ~52%.

Imaginea principală are raport aproape pătrat și radius mare (`18px`). În locul unui simplu thumbnail, este poziționată ca piesă editorială dominantă. Miniaturile de jos permit explorarea: cameră completă, close-up pe țesătură, mod de prindere și scenă lifestyle.

## 3. Titlu, identificare și preț

- Titlul este serif, 40–46px pe desktop; produsul capătă statut de colecție, nu doar SKU.
- Badges scurte: `Blackout`, `Catifea`, `Premium`.
- Prețul este în dreapta titlului, cu TVA inclus dedesubt; rămâne evident fără a domina fotografia.
- Informațiile de catalog sunt consolidate într-un rând discret: categorie, cod, material, culoare.

## 4. Alegerea culorii

Culoarea nu mai este afișată doar ca text. Este o linie de swatch-uri reale de țesătură. Starea activă are inel auriu și un indicator accesibil (`aria-pressed`). Pentru fiecare produs există o singură carte, iar nuanțele sunt variații ale aceleiași familii.

## 5. Callout de consultanță

După culoare există un callout ivory-auriu:

> Ai nevoie de ajutor la comandă? Echipa noastră te ghidează pas cu pas.

Este poziționat înaintea dimensiunilor, exact în punctul în care un client poate avea dubii. CTA-ul este link discret „Contactează-ne”, nu un pop-up intruziv.

## 6. Configurator în pași

Configuratorul are structură predictibilă:

1. Lățime — control stepper, informație min/max;
2. Înălțime — control stepper, informație max;
3. Modul de confecționare — carduri cu icon, nume și cost suplimentar;
4. Număr bucăți — stepper;
5. Rezumat total — calcul vizibil înainte de CTA.

Nu se folosesc butoane negre compacte fără separare. Opțiunile sunt carduri cu border fin, iar starea selectată utilizează `border-color` auriu și fundal ivory cald.

## 7. CTA și rezumat estimat

Zona de conversie este un bloc compact:

- „Total estimat” în dreapta;
- detaliu de calcul într-un rând mic dedesubt;
- buton principal full-width, auriu închis, `ADAUGĂ ÎN COȘ`;
- favorit separat ca acțiune secundară.

Pe mobil, rezumatul și CTA-ul devin sticky la baza ecranului după ce utilizatorul trece de configurator.

## 8. Bariere de încredere

Sub configurator apare o bandă cu patru elemente: mostre gratuite, transport gratuit, retur 14 zile și plată securizată. Aceasta reduce fricțiunea înainte de a cere plata.

## 9. Specificații și informații practice

În locul unui tabel lung sub un accordion generic, folosim un tab vertical în stânga și panou mare în dreapta:

- Specificații;
- Descriere;
- Transport;
- Retur;
- Instrucțiuni de îngrijire.

Conținutul principal combină un tabel concis cu beneficii vizuale: blackout total, izolare fonică, cădere naturală, textură premium, rezistență la uzură și utilizare pentru spații luminoase.

## 10. Cross-sell / recomandări

Blocul „S-ar putea să îți placă” păstrează cardurile mari, cu 5 produse vizibile pe desktop. Cardul include:

- foto lifestyle;
- culoare/swatch;
- colecție + denumire redusă;
- preț de la;
- favorite.

## 11. Newsletter și footer

Newsletter-ul folosește fundal aproape-negru cu textură, margine aurie de lumină și draperie în partea dreaptă. Formularul este simplu: un câmp și un singur CTA. Footer-ul rămâne ivory pentru ca finalul paginii să nu fie prea greu vizual.

## Tokenuri UI

```css
--paper: #fbf8f3;
--paper-2: #f3eee6;
--ink: #171411;
--ink-muted: #746d65;
--line: #e3dcd2;
--gold: #ad7c32;
--gold-dark: #8c5e1b;
--forest: #153f3c;
--radius-lg: 18px;
--radius-md: 12px;
--shadow-soft: 0 18px 48px rgba(31, 23, 17, .08);
```

## Responsive

- `>= 1180px`: două coloane media/configurator, 5 recomandări.
- `768–1179px`: două coloane mai compacte, recomandări 3–4.
- `< 768px`: galeria sus, configurator jos; header condensat; opțiunile de confecționare în două coloane; CTA sticky după configurare.
