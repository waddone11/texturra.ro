# Prompt de implementare — Pagina de produs TEXURRA

```text
Ești un senior frontend engineer și UI designer. Implementează pagina de detaliu produs pentru un brand românesc premium de textile pentru casă, TEXTURRA. Stack-ul existent are deja date de produs, categorii, prețuri, variații și coș; tu faci doar redesignul fără să rupi logica existentă.

OBIECTIV
Transformă pagina de produs dintr-un configurator dens într-un luxury product configurator editorial: fotografie dominantă, ierarhie clară, configurare a dimensiunilor în pași, cost estimat vizibil și CTA de adăugare în coș foarte clar.

PRODUS EXEMPLU
Draperie Velvet 308 – Albastru-Verzui Pastel Blackout din Catifea de Lux.
Preț de bază: 45.00 lei / metru.
Material: Catifea premium.
Categorie: Draperii blackout.
Cod produs: TXT-21.
Culoare activă: Albastru-Verzui Pastel.

DESIGN SYSTEM
- background principal: #fbf8f3;
- suprafețe: #fffdf9;
- text: #171411;
- text secundar: #746d65;
- linii/border: #e3dcd2;
- accent auriu: #ad7c32;
- accent forest: #153f3c;
- headline font: Playfair Display / Cormorant Garamond sau serif editorial echivalent;
- interfață: DM Sans / Inter;
- radius card mare 18px;
- fără UI albastru, fără stil de marketplace, fără carduri înghesuite.

STRUCTURĂ
1. Top utility bar dark cu: transport gratuit, mostre gratuite, consultanță specializată, showroom-uri.
2. Header ivory cu logo, categorii, search, cont, favorite, coș.
3. Breadcrumb compact.
4. Product section în două coloane desktop:
   - stânga: media gallery 48%; imagine principală aproape pătrată, radius 18px; miniaturi sub imagine;
   - dreapta: titlu serif mare, preț ancorat în dreapta, badge-uri, meta catalog, descriere scurtă, swatch-uri de culoare.
5. Callout de consultanță ivory + auriu înainte de configurator: „Ai nevoie de ajutor la comandă?” și link „Contactează-ne”.
6. Configurator în ordine: lățime, înălțime, tip confecționare, număr bucăți, total estimat, CTA.
   - dimensiuni = custom numeric stepper cu minus, valoare și plus;
   - fiecare opțiune de confecționare = card cu icon, denumire, cost/m; selecția trebuie să fie clară;
   - după orice schimbare, totalul estimat se actualizează din prețul de bază × lățime × înălțime × bucăți + cost manoperă/m.
7. Buton principal full width „ADAUGĂ ÎN COȘ”; favorite ca icon button adiacent.
8. Trust strip cu 4 beneficii.
9. Specificații în tabs vertical pe desktop / accordion pe mobil: Specificații, Descriere, Transport, Retur, Instrucțiuni de îngrijire.
10. Recomandări: cinci carduri mari lifestyle, fiecare cu wishlist, swatch și preț.
11. Newsletter dark cu draperie în dreapta și footer ivory.

ACCESIBILITATE
- folosește button real pentru toate acțiunile;
- aria-pressed pentru swatch-uri și opțiuni selectabile;
- focus-visible vizibil;
- form labels asociate controalelor;
- contrast AA.

RESPONSIVE
- Mobile-first;
- sub 768px: media peste conținut, header simplificat, configurator în 1 coloană, CTA sticky la baza ecranului după configurator;
- nu ascunde informații critice.

IMPLEMENTARE
- Folosește componente reutilizabile: ProductGallery, ColorSwatches, DimensionStepper, FinishSelector, PriceSummary, BenefitStrip, ProductSpecs, RelatedProducts.
- Păstrează rutele, schema de date, funcția de add-to-cart și tracking-ul existent.
- Nu hardcoda datele în componente; primește produsul și opțiunile prin props / view model.
- Nu folosi imagini cu text incorporat. Toate textele trebuie să fie HTML, SEO-friendly.
```
