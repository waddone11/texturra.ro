# PRODUCT_VARIATIONS_AUDIT — Faza Filament-2a (READ-ONLY)

Audit al fluxului produs + variații înainte de design-ul `ProductResource` în Filament.
**Nimic nu a fost modificat.** Citit direct din cod + date reale din DB (`texturra_baza`, 22 produse).
Branch: `phase-filament-2a-audit-products`. Data: 2026-06-21.

> ⚠️ Regula brief-ului confirmată din nou: **Brief ≠ Realitate.** Auditul Filament-0 + comentarii
> vechi din cod (ex. `Attribute.php` listează "Unitate de măsură / Cantitate / Image") sunt **stale**.
> Datele reale spun altceva (atributele reale sunt **Culoare** și **Material**). Tot ce urmează e din DB live.

---

## REZUMAT EXECUTIV (citește asta întâi)

1. **`Product.type` (enum `standard|custom`) este comutatorul custom/standard** — load-bearing pe storefront
   (`detail.blade.php:146`). Date reale: **21 custom (Draperii), 1 standard (Lenjerie de pat)**. Custom/standard
   **ESTE determinabil** → regula de oprire pe asta **NU se declanșează**. DAR adminul vechi **nu setează** niciodată
   `type` (doar seeder-ul / editare DB). Filament **trebuie** să-l expună ca câmp de prim rang.
2. **Variațiile actuale NU sunt variante vandabile** — sunt **etichete de spec** (1 rând = 1 pereche atribut/valoare).
   Frontend-ul **nu** lasă clientul să aleagă o variație; coșul nu stochează niciun `variation_id`/`attribute_value_id`.
   Adică: matricea de variații pe care vrem s-o construim **nu există azi** — e teren nou, nu o migrare.
3. **Nu există legătură categorie ↔ atribute.** `Characteristic` (presupus de audit) e taxonomie **eMAG**, e **goală**
   (0 rânduri) și nu leagă atribute de variații. Atributele sunt **globale** (Culoare, Material), atașate liber.
   → formularul "atribute filtrate pe categorie" e o **decizie de construit** (raportat, nu STOP).
4. **Două sisteme de culoare deconectate:** `ColorGroup`+`Color` (paletar bogat: 11 grupuri, 155 culori cu `cod_css`
   + imagini) **vs** `attribute_values` cu `attribute=Culoare` (23 nume text liber). Legate doar prin **string match**
   fragil (`AttributeValue.value === Color.name`). Paletarul real e `ColorGroup`, dar variațiile nu-l referă prin FK.
5. **Stoc în 3 locuri** (capcană): `products.general_stock` (coloană), `product_stocks` (per locație), `product_variations.stock`.
6. **Coloana JSON `product_variations.attribute_value_ids`** există + cast `array`, dar e **NULL peste tot** și nescrisă
   de niciun cod → pivotul `product_variation_attribute_values` e singura sursă de adevăr. Ignoră JSON-ul.

Niciun STOP dur. Două "decizii de construit" de adus la design (vezi secțiunea finală).

---

## TASK 1 — Modelul de variații REAL

### Product (`app/Models/Product.php`)
`$fillable` (extras relevant): `name, slug, description, **price**, sale_price, min_sale_price, max_sale_price,
recommended_price, category_id, family_type_id, vat_id, **general_stock**, **height**, **type**, images(JSON),
characteristics(JSON), attachments(JSON), offer_details(JSON), product_code, ean, barcode, acquisition_price, status, …`
+ multe câmpuri eMAG (`emag_*`, `commission_percentage`, `part_number`, `ownership`, `is_synced`).

- **Preț: pe Product** (`price`) — plus discounturi calculate în `Product::price()` (produs + categorie, `:176-219`).
- **Stoc: pe Product** (`general_stock`), dar și `stocks()` → `hasMany(ProductStock)` (per locație), **și** pe fiecare variație.
- `height` = înălțime maximă a produsului (folosită ca plafon pentru configuratorul custom, vezi TASK 4). **Nu există `length`/`width` pe produs** — lățimea custom vine din coș.
- `characteristics` pe Product = **coloană JSON liberă** (accesor/mutator json_encode la `:99-133`), **NU** relația cu modelul `Characteristic`. Produsul **nu are** metodă `characteristics()` relațională.
- `type` = enum `standard|custom` (migrare `2025_05_23_095723`, default `standard`). **Comutatorul cheie.**
- Relații: `category()` belongsTo, `familyType()` belongsTo, `vat()` belongsTo, `stocks()` hasMany ProductStock,
  `variations()` hasMany ProductVariation, `discount()` hasMany Discount.

### ProductVariation (`app/Models/ProductVariation.php`)
- `$fillable = ['product_id', 'sku', 'price', 'stock']` → **SKU, preț, stoc sunt pe variație** (preț poate suprascrie produsul).
- `casts = ['attribute_value_ids' => 'array']` → coloană JSON care **există dar e NULL peste tot, nescrisă de cod**. Capcană moartă.
- `attributeValues()` = `belongsToMany(AttributeValue, 'product_variation_attribute_values')` (pivot).

### Pivot (`ProductVariationAttributeValue` → tabel `product_variation_attribute_values`)
- Coloane: `id, product_variation_id, attribute_value_id, timestamps`. Model + relații belongsTo de ambele părți.

### Attribute / AttributeValue
- `Attribute`: `$fillable = ['name','description']`, `values()` hasMany AttributeValue.
  **Date reale:** doar 2 atribute → **id=1 „Culoare" (23 valori), id=2 „Material" (3 valori: Voal, Catifea, …).**
  (Comentariul din `Attribute.php:23-26` cu „Unitate de măsură/Cantitate/Image" e **stale**, ignoră-l.)
- `AttributeValue`: `$fillable = ['attribute_id','value','extra_info']`. Relații: `attribute()`, `productVariations()` (belongsToMany),
  **și `color()` = `belongsTo(Color, 'value', 'name')`** — match pe STRING (valoarea atributului = numele culorii). Fragil.

### Color / ColorGroup — "paletarul"
- `ColorGroup`: `$fillable = ['name','slug','image_path']`, `colors()` hasMany Color. **Date reale: 11 grupuri**
  (Roșu & Burgundia, Roz/Piersică, Portocaliu, Galben & Auriu, Crem & Bej, Maro, Verde, Albastru & Ou de rață, …),
  fiecare cu `image_path` `.avif`.
- `Color`: `$fillable = ['color_group_id','name','cod_css']`, `group()` belongsTo. **Date reale: 155 culori** cu hex (`#800020`, …).
- **Acesta e paletarul real** (bogat, reutilizabil, grupat). **DAR** variațiile **nu-l referă prin FK** — leagă culoarea doar prin
  numele text din `attribute_values` (attribute=Culoare), printr-un string-match fragil. Două sisteme de culoare paralele.

### Date reale (snapshot)
| Tabel | Rânduri | Notă |
|---|---|---|
| products | 22 | 21 `custom` / 1 `standard` |
| product_variations | 43 | toate cele 22 produse au variații (~2 fiecare) |
| product_variation_attribute_values (pivot) | 43 | 1:1 cu variațiile (fiecare variație = 1 valoare atribut) |
| attributes | 2 | Culoare(23), Material(3) |
| attribute_values | 26 | nume text liber |
| color_groups | 11 | paletar (cu imagini) |
| colors | 155 | cu `cod_css` |
| characteristics | **0** | taxonomie eMAG, goală |
| manufactoring_types | 6 | confecționare custom |

**Exemplu real (produs 3):** are 2 rânduri de variație — var.7 → `attribute_value_id=3` („Catifea"=Material),
var.8 → `attribute_value_id=6` („Violet Imperial"=Culoare). Ambele cu preț 45, stoc 500. Deci „variațiile" sunt
**etichete de spec**, nu combinații vandabile.

### Verificare empirică a "bug-ului de pivot"
Codul de attach din admin (`ProductCreate.php:117-119`) are o formă suspectă
(`attach([$attribute_id => ['attribute_value_id' => $value_id]])` — riscă să scrie `attribute_id` în loc de `attribute_value_id`).
**Verificat pe date live: pivot.attribute_value_id ia valori 3–26, toate ID-uri valide de attribute_value** (atributele au doar id 1,2;
dacă bug-ul ar fi activ, toate ar fi 1 sau 2). → **bug-ul NU se manifestă în datele actuale** (probabil seeder a scris corect,
sau forma funcționează). Irelevant oricum: Filament își va scrie propriul attach corect. **De evitat forma asta la reimplementare.**

---

## TASK 2 — Legătura categorie ↔ atribute (CRUCIAL)

**Concluzie: NU EXISTĂ nicio legătură categorie ↔ atribute de variație.**

- `Characteristic` (`app/Models/Characteristic.php`) = taxonomie **eMAG** per categorie
  (`category_id, characteristic_id, type_id, is_mandatory, is_filter, value_tags, …`), **belongsTo Category**.
  **NU** leagă categoria de `Attribute`/`AttributeValue`. În plus, în DB are **0 rânduri** → inactivă azi.
- `FamilyType` (eMAG family type) — la fel, metadata eMAG, nu sistem de atribute.
- Adminul vechi încarcă **toate** atributele global (`Attribute::with('values')->get()`,
  `ProductCreate.php:39`), fără filtrare pe categorie. `Attribute` **nu are** relație cu Category.
- Deci sistemul actual: **orice produs poate primi orice atribut/valoare** (liber, global). Categoria nu constrânge nimic.

→ **DECIZIE DE CONSTRUIT** (raportat, conform regulii de oprire — nu STOP): pentru viziunea „formular adaptat pe
categorie" (Draperii→Culoare; Covoare→Dimensiune+Culoare) trebuie creată o **relație nouă categorie↔atribute**
(ex. pivot `category_attribute`, sau reuse al `Characteristic` extins). Nu există azi.

---

## TASK 3 — Fluxul actual de creare/editare (admin vechi Livewire)

**Există 3 editoare divergente** (nu împart cod): `ProductCreate`/`ProductEdit` (cel „viu", rute `admin.products*`)
și `Crud.php` (mai vechi, paralel, cu atribute hardcodate `unitate`/`ambalaj` + abuzează un atribut „Image").

- **FĂRĂ categorie-first, FĂRĂ matrice.** Formular plat. Categoria e doar încă un câmp; nu reîncarcă/filtrează atribute.
- **Variațiile se adaugă MANUAL, una câte una** — fiecare = **o singură** pereche atribut/valoare
  (Alpine `attributeSelector`, `product-create.blade.php:245-332`). Nu există generare combinatorie.
- **UI-ul nu cere SKU/preț/stoc per variație.** Se derivă la salvare:
  SKU = `'SKU-'.strtoupper(uniqid())`; `price` = prețul produsului; `stock` = `general_stock` împărțit egal pe variații
  (`ProductCreate.php:96-113`).
- **Salvare:** `ProductVariation::create([...])` apoi attach în **pivot DOAR** (`:117-119`). **Coloana JSON
  `attribute_value_ids` NU e scrisă niciodată** (grep confirmă: doar cast-ul există). Pivot = sursa de adevăr.
- **`ProductEdit::updateProduct` șterge TOATE variațiile produsului apoi le recreează** (`:102-119`) — destructiv (pierde SKU-uri/stoc).
- **`type` NU e setat/citit** în niciun flux de admin (`grep "'type' =>"` → nimic pe write produs). Doar seeder-ul îl pune.
- **Culoare**: nu există picker dedicat — culoarea trece prin sistemul generic Attribute/AttributeValue. `Color`/`ColorGroup`
  nu sunt atinse de admin-ul de produs.

---

## TASK 4 — Frontend: variații + dimensiuni

Rută `/produs/{slug}` → `ProductController::show` → `resources/views/products/detail.blade.php`
(`detail2/3.blade.php` sunt **moarte**, nereferite). **Niciun Livewire în fluxul de cumpărare** — Blade + form-uri HTML către `CartController`.

- **Comutator custom/standard** = `@if($product->type === 'custom')` (`detail.blade.php:146`) →
  `product-form-custom` vs `product-form-standard`. **NU pe categorie, NU pe variații.**
- **Selecție variație: NU EXISTĂ pe fluxul de cumpărare.** Controllerul calculează `$availableColors/Sizes/Materials`
  din variații dar le folosește **doar la afișare** (tabel spec, read-only). **Nimic nu se postează în coș ca variație.**
  Coșul **nu are** `variation_id`/`attribute_value_id` (confirmat în `Cart`/`CartController`/migrări).
- **Custom (Draperii/Perdele):** `product-form-custom.blade.php`, configurator Alpine. Postează la `cart.add.custom`:
  `length` (1–30 m), `height` (0.5..`$product->height`), `manufactoring_type_id` (radio din `ManufactoringType::orderBy('price')`),
  `pieces` (1/2). Preț = `($product->price() + $manufactoringType->price) * $length` (`CartController.php:108-111`).
  Mereu rând NOU în coș.
- **Standard (Covoare/Așternuturi):** `product-form-standard.blade.php` = **doar stepper de cantitate**. **Niciun selector de mărime.**
  O „mărime standard" **nu** e modelată ca variație/AttributeValue selectabilă. Azi, fiecare mărime ar fi un **Product separat**.
- **Coș (`Cart`)**: identitate linie = `IDENTITY_KEYS = [product_id, length, height, manufactoring_type_id, pieces]`.
  Relație doar cu `ManufactoringType`. **Fără** legătură cu ProductVariation/AttributeValue/Color.

→ **Ce NU atingem:** storefront-ul, configuratorul custom, logica de coș/comandă. Filament scrie doar în modelele de admin.
Dar **orice** redesign de variații trebuie să păstreze `Product.type`, `Product.price`, `Product.height` și tabela
globală `ManufactoringType` (de astea depinde cumpărarea).

---

## IMPLICAȚII PENTRU FILAMENT (ProductResource)

### Ce e DEJA acolo și reutilizabil
- **`Product.type`** = sursă curată custom/standard → un `Select`/`Radio` (Draperii→custom, Covoare/Așternut→standard).
  Formularul se poate adapta pe `type` (`live()` + secțiuni condiționale), **independent de categorie**.
- **Paletarul = `ColorGroup` (11) + `Color` (155, cu `cod_css`/imagini)** — bogat, gata de folosit pentru un picker vizual.
- **`Attribute`/`AttributeValue`** (Culoare, Material) — există, dar libere/globale.
- **`ManufactoringType`** (6) — pentru produse custom (doar referință; nu se editează din ProductResource neapărat).
- Câmpurile de bază Product (preț, stoc, vat, categorie, imagini JSON) — mapare directă.

### Ce trebuie CONSTRUIT (deciziile de design pentru sesiunea următoare)
1. **Relație categorie ↔ atribute** (nu există). Ca formularul să ofere automat atributele potrivite pe categorie
   (Draperii→Culoare; Covoare→Dimensiune+Culoare). Variante: pivot nou `category_attribute`, sau extindere `Characteristic`.
2. **Matrice de variații reală** (nu există azi — variațiile sunt etichete 1-atribut). Dacă vrem combinații vandabile
   (alegi Culoare×Dimensiune → N variații cu SKU/preț/stoc), e funcționalitate **nouă**, nu portare.
3. **Reconciliere culoare:** paletar bogat `ColorGroup/Color` **vs** `attribute_values` text liber, legate prin string fragil.
   Decizie: variațiile referă `Color` prin FK (recomandat) **sau** rămân pe `attribute_values` cu sincronizare la paletar?
4. **Dimensiuni standard pentru Covoare/Așternut:** azi NU sunt selectabile (un produs/mărime). Dacă „mărimea standard"
   devine atribut de variație, trebuie și suport pe frontend — **dar** asta atinge storefront-ul (în afara scopului Filament admin;
   de discutat separat).

### Capcane (de evitat în implementare)
- **Stoc triplu** (`general_stock` vs `product_stocks` vs `variation.stock`) — alege o sursă canonică, nu scrie în toate.
- **Coloana JSON `attribute_value_ids`** — moartă (NULL peste tot). Nu o folosi; pivotul e adevărul. Eventual de șters (fază separată).
- **Forma de `attach` din adminul vechi** (`[$attribute_id => ['attribute_value_id' => …]]`) — suspectă; folosește attach corect (`attach($valueId)`).
- **`ProductEdit` șterge-și-recreează** toate variațiile — Filament Relation Manager trebuie să facă upsert, nu wipe.
- **`type` nesetat din adminul vechi** — produsele noi default `standard`; expune-l explicit ca să nu rămână custom-uri necomutate.
- **`characteristics` pe Product = JSON liber**, nu relația `Characteristic` — nu le confunda în resource.

---

## EVALUAREA REGULII DE OPRIRE
- *Model fundamental diferit (JSON în loc de pivot)?* → **Nu.** Folosește pivot belongsToMany (normal). JSON-ul există dar e mort. **Continuă.**
- *Nicio legătură categorie↔atribute?* → **Confirmat că nu există** → **raportat ca decizie de construit** (conform regulii, nu STOP dur).
- *Custom vs standard nedeterminabil?* → **Determinabil** prin `Product.type` (21 custom/1 standard în date). **NU se declanșează STOP.**

**Concluzie:** niciun STOP dur. Două decizii de design de luat împreună (categorie↔atribute + matrice/culoare) înainte de a scrie `ProductResource`.
