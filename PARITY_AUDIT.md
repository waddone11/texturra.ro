# PARITY_AUDIT — admin vechi Livewire `/admin` vs Filament `/panel` (READ-ONLY)

Faza Filament-8a. Înainte de a șterge adminul vechi + swap `/panel`→`/admin`.
**Nimic șters, niciun swap.** Comparație + gap-uri clasificate. Branch `phase-filament-8a-parity`.

> **VERDICT: swap-ul NU e încă sigur.** Adminul Filament are paritate completă pe **Categorii** și
> **Comenzi**, dar **Produse** are gap-uri reale (managementul imaginilor, arhivare/restore, EAN/cod),
> plus un **blocker transversal** (admin nu-și poate schimba parola pe `/panel`) și o **inconsistență în
> codul meu** (ștergere comenzi). Trebuie o fază 8b de „pre-swap fixes" înainte de a șterge ceva.

---

## Suprafața admin veche (ce ștergem la swap)
Rute `/admin/*` (`routes/web.php:192-212`, guard `CheckRole:admin,employee`):
dashboard, categories, products, products-emga, products/create, products/{id}/edit, products/filter, orders, change-password.
Plus `/utilities/*` (`web.php:24-74`) — **independente de Livewire-ul `/admin`, NU se șterg la swap.**

---

## CATEGORII — `Categories\Crud` ↔ `CategoryResource` → **PARITATE ✅**
| Funcție | Vechi | Filament | Verdict |
|---|---|---|---|
| CRUD + parent select | da | da | HAS |
| Anti-ciclu parent | **NU** | da (`excludedParentIds`) | Filament **mai bun** |
| Search | da | da | HAS |
| status/is_allowed/is_ean/is_warranty toggles | nu | da | Filament **mai bun** |
| Soft delete | NU (model fără SoftDeletes) | NU | paritate |
| Arbore vizual recursiv (expand/collapse) | da (`category-item.blade`) | tabel plat + coloană „Părinte" + filtru | **PARTIAL** (ierarhia e editabilă/filtrabilă, dar vizualul nested dispare) |

→ Niciun blocker. O decizie minoră (arbore vizual vs tabel plat).

## COMENZI — `Orders\Crud` ↔ `OrderResource` → **PARITATE ✅ (cu o inconsistență de cod)**
| Funcție | Vechi | Filament | Verdict |
|---|---|---|---|
| Listă + search (nr/client) | da | da | HAS |
| Update status + note | da (string liber) | da (Select 5 valori) | Filament **mai bun** |
| Detalii (adrese, sume, produse) | accordion | Placeholders + RelationManager cu **dimensiuni custom din meta** | Filament **mai bun** |
| Hard delete | da (`$order->delete()`) | **da, pe pagina Edit** (vezi mai jos) | paritate de fapt |
| Download factură existentă | **link da** (`crud.blade:69-73`) | facturi afișate read-only, **fără link download** | **PARTIAL** |
| AWB / Voucher | nu | read-only da | Filament **mai bun** |
| Generare factură / resend email | **NU în UI** (doar URL `/utilities`) | NU | paritate (rutele supraviețuiesc) |

⚠️ **Corecție la ce ți-am raportat eu la Filament-6:** am zis „OrderResource NU are delete". **Greșit.** Tabelul n-are delete, **dar pagina Edit are `DeleteAction` în header** (`Pages/EditOrder.php` — default din generator). Deci comenzile **SUNT** hard-deletable în Filament. De fapt asta e *paritate* cu vechiul (care făcea hard delete), dar **contrazice decizia mea „date financiare nu se șterg"**. → DECIZIE: scot DeleteAction (politica financiară) sau o las (paritate)? Recomand să-l scot.

## PRODUSE — `ProductList/ListEmag/Create/Edit/Filter` ↔ `ProductResource` → **GAP-uri REALE ⛔**
| Funcție veche | Cite | Filament |
|---|---|---|
| **Upload + management imagini** (multi, ștergere individuală, stocare `/storage/...` JSON) | `ProductCreate.php:84-142`, `ProductEdit.php:94-156` | **MISSING** — doar preview read-only (decizie F-5) |
| **Arhivare** (delete = `status→0`) + **Restore** (`status→1`) | `ProductList.php:48-66`, `ProductListEmag.php:52,63` | **MISSING** — Filament face **hard delete** (fără SoftDeletes); fără arhivare/restore |
| **EAN required + unique** la create | `ProductCreate.php:25` | **MISSING** — EAN opțional, non-unique |
| **Auto `product_code`** (`TEX-…uniqid`) | `ProductCreate.php:70,79` | **MISSING** — text liber, risc cod gol/duplicat |
| Filtre operaționale: search pe **ID exact**, **EAN** (cu/fără), **preț** (cu/fără/0), **status** (activ/arhivat) | `ProductList.php:73-97` | **MISSING** — Filament are doar filtre category + type |
| Lista EAN separată (`/products-emga`) | `ProductListEmag.php:71` (`whereNotNull('ean')`) | **MISSING** ca pagină, dar = un simplu filtru „are EAN" |
| Variații/atribute (Material via variații) + creare atribute inline | `ProductCreate.php:92-120`, `ProductEdit.php:102-185` | **MISSING** editare (Filament doar afișează material read-only) |
| Editor rich-text (Quill) descriere | view edit | **PARTIAL** — Filament Textarea simplu |
| name/price/category/stock de bază, slug auto | da | HAS |
| Filament adaugă: `type` live, sale_price, vat, height, **paletar cu stoc per culoare** | — | Filament **mai bun** (model nou de stoc) |

## DASHBOARD — `AdminDashboardController` ↔ Filament Dashboard → **SAFE ✅**
Dashboard-ul vechi e un **placeholder gol** („we will provide usfull information here…"). Singurele cifre reale =
2 badge-uri în nav (`SidebarStats`: nr categorii + nr produse); `productCountPrice`/`productCountEan` calculate dar
**niciodată afișate**. → Niciun KPI real de pierdut. Filament arată count-urile native în tabele. **Nu e gap.**
(Un dashboard cu KPI-uri ar fi **feature NOU**, nu paritate.)

## PAROLĂ — `Account\ChangePassword` ↔ Filament → **BLOCKER ⛔**
Panelul Filament **nu are `->profile()`** (`AdminPanelProvider` — grep `profile` = 0). După ștergerea `/admin`,
un admin/employee **nu-și poate schimba parola** pe `/panel`. (Ruta client `account.change-password` e storefront, rămâne.)

---

## GAP LIST consolidat (de rezolvat ÎNAINTE de swap)

### ⛔ BLOCKER — trebuie replicat în Filament înainte de a șterge `/admin`
1. **Management imagini produs** (upload multi + ștergere). Azi read-only în Filament; e SINGURA cale de a gestiona imaginile, iar frontend-ul citește `images` JSON. *Load-bearing.* (Atenție: convenția `/storage/...` — FileUpload trebuie configurat să nu o spargă.)
2. **Arhivare/Restore produs** (`status` 0/1) + **gardă pe hard-delete**. Vechiul nu ștergea niciodată dur; Filament șterge dur (fără SoftDeletes) și n-are arhivare/restore. Risc de pierdere date.
3. **Schimbare parolă admin pe `/panel`** → adaugă `->profile()` în `AdminPanelProvider` (trivial, dar regresie dură dacă lipsește).
4. **EAN required+unique** + **auto `product_code`** la create — garduri de integritate (relevante pt eMAG); pierdute în Filament.

### 🟡 DECIZIA TA
5. **Filtre produs operaționale** (ID/EAN/preț/status) — port în tabelul Filament. Înlocuiesc complet `ProductFilter` ȘI pagina `/products-emga`. Recomand să le adăugăm (workflow zilnic: găsește produse fără preț/EAN).
6. **Variații/atribute/Material editabile** — încă necesare (Material pt eMAG?) sau înlocuite definitiv de paletar? Dacă necesare → urcă la BLOCKER. (Recomand un pivot `product_material` curat, ca la culoare — fază separată.)
7. **DeleteAction pe comenzi** (`EditOrder`) — scot (politica „date financiare") sau las (paritate cu vechiul)? Recomand scot.
8. **Editor descriere**: Textarea simplu vs RichEditor (Quill) — descrierile HTML existente se editează ca text brut acum.
9. **Download factură** din OrderResource (azi facturile-s read-only fără link). Recomand un Action de download.
10. **Arbore categorii vizual** vs tabel plat — confirmă că tabelul plat e OK.
11. **`/utilities` generare-factură + resend-email**: supraviețuiesc (nu-s în `/admin`), dar n-au buton Filament. Decizie: le expunem ca acțiuni pe OrderResource sau rămân URL-uri directe?

### ✅ SAFE TO DROP (mort/duplicat/superseded — se șterge la swap fără regret)
- **`Products\Crud.php` (568 linii) + view-urile orfane** (`crud.blade`, `crud_old`, `crud_lastest`, `create`, `edit`, `product-create2`) — **cod mort, nerutat** (referă rute inexistente). Se poate șterge ACUM.
- **`ProductListEmag` / `ProductFilter`** ca pagini separate — după ce filtrele (#5) intră în tabelul Filament.
- **Dashboard placeholder** + `SidebarStats` dead counts — count-urile reale-s native în Filament.
- Plumbing Choices.js/Alpine/flash-message/modaluri inline — superseded de Filament.

---

## RECOMANDARE — Faza 8b „pre-swap fixes" (înainte de orice ștergere)
Replicăm cele 4 BLOCKER-e (imagini, arhivare/restore+gardă delete, `->profile()`, EAN/product_code) + portăm filtrele (#5),
rezolvăm decizia delete comenzi (#7). Apoi: confirmare finală → **swap-ul** (ștergere `/admin` Livewire + dead code).
Variațiile/Material (#6) le tratăm ca decizie separată (probabil `product_material` pivot, ca la culoare).

**STOP.** Nimic șters, niciun swap. Pe baza gap-urilor decidem împreună scopul fazei 8b.
