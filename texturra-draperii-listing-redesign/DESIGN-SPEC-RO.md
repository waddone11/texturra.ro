# Texturra — Redesign listare „Draperii”

## Obiectiv

Acest concept transformă pagina de produs dintr-o grilă foarte densă de SKU-uri într-o experiență de **luxury editorial commerce**. Direcția pune accent pe atmosferă, material, culoare și consultanță, fără să sacrifice filtrarea rapidă.

## Principii de layout

- Container desktop: `max-width: 1320px`; spațiu exterior: `32px`.
- Fundal: ivory cald `#FCFAF7`, nu alb steril.
- Text: charcoal cald `#1A1714`; detalii și borduri: tonuri taupe.
- Accent: auriu discret `#B58A43`, folosit pentru CTA-uri, badge-uri și stări active.
- Titluri: `Playfair Display`; UI, date și filtre: `DM Sans`.
- Grilă produse: **3 coloane**, nu 4–5. Draperiile au nevoie de imagini mari pentru a comunica textura și căderea materialului.

## Header

1. **Utility bar negru, 48px** — transport gratuit, mostre, consultanță și showroom-uri.
2. **Header ivory sticky, 92px** — logo stânga, navigație centrată, căutare și acțiuni în dreapta.
3. La scroll, fundalul rămâne semi-opac cu `backdrop-filter: blur(10px)`, astfel încât pagina rămâne premium și utilizabilă.

## Hero de categorie

- Înălțime desktop: `350px`.
- Fotografie lifestyle pe întreaga zonă, cu draperii și interior premium.
- Conținutul este în prima jumătate, aliniat la stânga.
- Overlay pentru lizibilitate:

```css
background: linear-gradient(
  90deg,
  rgba(17,14,12,.93) 0%,
  rgba(17,14,12,.78) 30%,
  rgba(17,14,12,.48) 45%,
  rgba(17,14,12,.13) 62%,
  rgba(17,14,12,.02) 76%
);
```

Asta păstrează imaginea vizibilă în partea dreaptă, dar oferă contrast ferm pentru copy în stânga. Hero-ul nu are CTA prea agresiv; pagina este o listare, iar conversia principală se face prin produse și filtrare.

## Subcategorii

Sub hero există pill-uri cu bordură fină: Toate, Blackout, Catifea, Din in, La comandă, Living, Dormitor. Starea activă este aurie. Pe mobil, bara devine scroll orizontal, fără să rupă layoutul.

## Filtrare

- Sidebar desktop: `262px`, sticky la `top: 112px`.
- Structură accordion: Material, Culoare, Opacitate, Cameră, Preț, Colecție.
- Filtre active apar și sus, lângă titlu, ca chips care pot fi eliminate individual.
- În sidebar există un card de consultanță. Este plasat după filtre pentru a nu concura cu produsele, dar pentru a ajuta utilizatorii care nu știu ce material se potrivește.

## Card produs

- Imagine verticală `4:4.55` pentru un look apropiat de fashion/editorial, nu de marketplace.
- Badge-ul este sus stânga, iar wishlist-ul este un cerc translucid sus dreapta.
- La hover, imaginea are un zoom discret `scale(1.045)` iar cardul urcă cu `translateY(-4px)`.
- Informația este ierarhizată:
  1. colecție,
  2. nume produs,
  3. material + opacitate,
  4. swatch-uri de culoare,
  5. preț pe metru,
  6. link „Vezi detalii”.

Nu se repetă aceeași draperie într-un card separat pentru fiecare culoare. Un singur produs comunică variațiile prin swatch-uri și mesajul `+ X nuanțe`.

## Banner editorial / consultanță

După primele trei produse apare un banner full-width în grilă. Raport: aproximativ `55% copy / 45% imagine`. Bannerul rupe ritmul repetitivei grile, vinde serviciul de confecție la comandă și permite programarea unei consultații fără să fie un pop-up intruziv.

## Newsletter și footer

Newsletterul este închis cu un fundal dark texturat, draperie vizuală în dreapta și overlay închis. Formularul are contrast puternic și CTA auriu. Footer-ul revine pe ivory, pentru a păstra finalul aerisit și premium.

## Adaptare mobil

- Sidebarul poate fi transformat într-un drawer / modal de filtre.
- Grila devine 2 coloane pentru tabletă și 1 coloană pe telefon.
- Hero-ul păstrează textul la stânga cu overlay mai dens.
- Categoriile devin scroll horizontal.
