# Prompt implementare AI / developer — Listare Texturra

```text
Construiește o pagină de listare produse pentru categoria „Draperii” a brandului românesc premium TEXTURRA. Nu schimba arhitectura existentă Laravel / Blade / Livewire / Vue decât dacă este necesar; păstrează endpoint-urile, cart-ul, favoritele și filtrarea existente. Înlocuiește doar structura vizuală și clasele CSS/utility classes astfel încât pagina să corespundă direcției de mai jos.

DIRECȚIE VIZUALĂ
- Stil: luxury editorial ecommerce, elegant, aerisit, cald, matur; nu marketplace.
- Fundal global: #FCFAF7 (ivory cald).
- Text: #1A1714 (charcoal cald).
- Accent: #B58A43, folosit cu reținere pentru badge-uri, CTA și stări active.
- Borduri: #E5DDD2, subtile.
- Titluri: Playfair Display / serif editorial.
- UI, meniuri, date, filtre: DM Sans / sans modern.
- Container desktop: max-width 1320px, 32px gutter.

HEADER
1) Utility bar dark de 48px cu 4 beneficii: transport gratuit, mostre gratuite, consultanță specializată, showroom-uri. Fiecare are icon line, titlu uppercase mic și subtitlu.
2) Header desktop sticky ivory de aproximativ 92px: logo Texturra stânga, meniul centrat, căutare + cont/favorite/coș în dreapta. Pe scroll: backdrop blur și border subtil.

HERO CATEGORIE
- După breadcrumb, pune un banner de 350px cu border-radius 18px.
- Folosește fotografie lifestyle cu living premium, canapea, fereastră înaltă, perdele/draperii beige și textile de lux.
- Text în stânga: eyebrow mic auriu; H1 „Draperii de lux pentru interioare rafinate”; subtitlu.
- Aplică exact acest overlay de la stânga la dreapta:
  linear-gradient(90deg, rgba(17,14,12,.93) 0%, rgba(17,14,12,.78) 30%, rgba(17,14,12,.48) 45%, rgba(17,14,12,.13) 62%, rgba(17,14,12,.02) 76%)
- Nu integra text în imagine; tot copy-ul trebuie să fie HTML semantic.

SUBCATEGORII
- Imediat sub hero: pill-uri premium cu border radius 999px: Toate, Blackout, Catifea, Din in, La comandă, Living, Dormitor.
- Stare activă aurie; normal fundal ivory/alb și border subtil.
- Pe mobil: carousel / scroll orizontal, fără wrap inestetic.

LISTARE
- Desktop: layout 262px sidebar filtre + conținut catalog.
- În sidebar: accordion pentru Material, Culoare, Opacitate, Cameră, Preț, Colecție.
- Sidebar sticky pe desktop. Pe mobil, transformă-l într-un filter drawer / bottom sheet cu buton „Filtrează”.
- În conținut: H2 „Draperii”, număr produse, chips pentru filtre active și un dropdown „Sortează după”.

CARDURI
- Grid 3 coloane pe desktop; 2 pe tabletă; 1 pe mobil.
- Imagini card în raport vertical 4:4.55, cu border radius 12px.
- Card: badge opțional sus stânga, wishlist translucid sus dreapta, imagine lifestyle, colecție, nume, material/opacitate, swatch-uri, preț „de la 45,00 lei / metru”, link „Vezi detalii →”.
- Hover desktop: imagine scale(1.045), card translateY(-4px), shadow discret.
- NU crea card separat pentru fiecare culoare a aceluiași SKU. Pune un singur produs + swatch-uri + „+ 11 nuanțe”.

BANNER EDITORIAL
- După primele 3 produse pune un card banner full-width în cadrul grilei.
- Raport 55% copy / 45% fotografie cu consultant aranjând o draperie.
- Copy: „Draperii la comandă, create pentru tine”, text scurt, CTA „Programează consultație”.

NEWSLETTER + FOOTER
- Newsletter dark, înălțime aproximativ 148px, cu fundal texturat și draperie discretă în dreapta. Copy pe stânga, email în centru, beneficii mici în dreapta. CTA auriu.
- Footer ivory, cu logo, linkuri produse/informații/suport/contact și metode de plată.

CERINȚE TEHNICE
- Imaginile trebuie să aibă lazy loading exceptând hero.
- Hero: fetchpriority="high".
- Păstrează accesibilitatea: aria-label, focus states, <button> pentru controale, headings corecte.
- Respectă culorile și spacing-ul din design; evită text prea mic. Dimensiune minimă UI: 11px, preferat 12px+.
- Nu folosi text generat în imagini, pseudo-logo-uri sau SVG-uri pline de text.
```
