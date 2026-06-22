# Pre-Deploy Audit — zone critice (bani + cont)

**Dată:** 2026-06-22 · **Mod:** READ-ONLY (zero cod modificat — doar acest raport) · **Branch:** `phase-pre-deploy-audit`

Harta exactă a ce funcționează și ce e rupt înainte de deployment. Verdict: ✅ merge · ⚠️ parțial · 🔴 rupt.

---

## TASK 1 — Badge coș în nav 🔴 RUPT (cauză identificată)

**Simptom:** adaugi produs în coș → badge-ul din nav rămâne 0 / nu se actualizează.

**Componente:** `app/Livewire/CartWidgetMenu.php` (badge nav) + `app/Livewire/CartWidget.php` (coș plutitor). Ambele: `mount()` → `updateCart()` → `$cartCount = Cart::where(...)->sum('quantity')`, ascultă eventul Livewire `cartUpdated`.

**Cauze reale (cumulate):**
1. **🔴 Produsele CUSTOM au `quantity = NULL` în DB.** `addCustomProduct` (CartController) NU setează `quantity`. Badge-ul face `->sum('quantity')` → NULL contează ca 0 → **un coș cu doar perdele custom afișează badge 0**, deși coșul are produse. (Confirmat în DB: rândurile custom au `quantity` gol, `length` setat.)
2. **🔴 Fără update live la adăugare.** Add = form POST clasic → `back()` (reload). Eventul Livewire `cartUpdated` NU e dispecerizat de un controller (doar de componente Livewire), deci badge-ul se bazează exclusiv pe `mount()` la reload — dar acolo lovește bug-ul #1.
3. **⚠️ Script mort (Livewire v2):** `cart-widget-menu.blade.php` conține `Livewire.emit('cartUpdated')` — API Livewire **v2**, eliminat în v3 (corect: `Livewire.dispatch`). No-op / eroare silențioasă.
4. **⚠️ Posibilă instabilitate session_id** la guest: în DB rândurile de coș au `session_id`-uri diferite per add (parțial artefact de testare via curl fără cookie-uri — de confirmat în browser real; dacă apare și în browser, e bug major: coșul guest se "pierde" între request-uri).

**De reparat:** (a) `addCustomProduct` să seteze `quantity` (ex. `= $pieces` sau 1) **SAU** badge-ul să numere altfel (ex. `count()` linii, sau `sum(quantity ?? pieces ?? 1)`); (b) dispecerizează un refresh real la add (redirect e ok, dar trebuie ca numărul să includă custom); (c) curăță scriptul `Livewire.emit`; (d) confirmă stabilitatea sesiunii guest în browser real.

---

## TASK 2 — Coș (cart) ✅ FUNCȚIONAL (cu 1 inconsistență + stil vechi)

**Stocare:** tabel `carts` (user_id / session_id, product_id, quantity, price, length, height, manufactoring_type_id, pieces). Custom = dimensiuni pe coloane separate; standard = quantity.

- **Add standard** ✅ — dedup corect cu `whereNull('length'/'height'/'manufactoring_type_id')` → **fix-ul anti-clobber e ÎN LOC** (nu strică liniile custom). Re-add → `increment('quantity')`.
- **Add custom** ✅ — fiecare config = linie nouă; preț = `(price() + manoperă) × length`.
- **Update cantitate / update custom / remove** ✅ — pe linie individuală, recalcul preț corect, stock check.
- **Merge guest→user la login** ✅ — `Cart::mergeSessionIntoUser` cu `IDENTITY_KEYS = [product_id, length, height, manufactoring_type_id, pieces]`, null-safe, **păstrează dimensiunile** + sumează cantități. **Ambele fix-uri de bani (IDENTITY_KEYS + whereNull) confirmate prezente**, cu teste (`CartMergeTest`, `CartAddStandardTest`).
- **🔴 Inconsistență subtotal:** `CartController::index()` calculează `quantity × price` pentru TOATE liniile (greșit pt. custom, unde `price` e deja total). **Mascat** fiindcă view-ul recalculează corect (`pieces × price` custom / `quantity × price` standard). Logică duplicată, fragilă — de unificat.
- **⚠️ Stil:** pagina coș (`cart/index.blade.php`) e pe design VECHI (gri/negru), NU pe redesign-ul 2026 (#FCFAF7/Playfair/auriu). De restilizat.

---

## TASK 3 — Checkout ✅ FUNCȚIONAL cap-coadă (dar fără plată online + stil de aliniat)

**Flux:** `/checkout` (CheckoutController@index) → adrese (livrare + facturare), metodă plată, voucher, note → **POST `/order/store`** (OrderController@store) creează comanda.

- **Order creat** ✅ — calcul preț (custom: `(length×price + length×manoperă)×pieces`; standard: `price×qty`), VAT, voucher, shipping (gratuit > prag), **stock check + decrement**, status `placed`.
- **Meta dimensiuni** ✅ — `order_product.meta` (JSON: is_custom, length, height, manufactoring_type_id, nume, preț) → comanda reflectă exact ce s-a configurat.
- **Voucher** ✅ — validare + `VoucherUsage` marcat `used`.
- **🔴 Fără gateway de plată real** — metodele (online card / ramburs / transfer) sunt doar UI; `payment_method` se salvează dar NU se procesează nicio tranzacție. Comanda se creează imediat indiferent de metodă (periculos pt. "online" — n-ar trebui confirmată înainte de plată). Netopia/Stripe = de integrat.
- **⚠️ Rută orfană:** `POST /checkout` (`checkout.process`, web.php) NU are handler în CheckoutController (form-ul postează la `/order/store`). De șters.
- **⚠️ Stil checkout:** `checkout/index.blade.php` e negru/gri (vechi-ish), NU redesign-ul ivory/Playfair. Există și `index2`/`index_copy` neutilizate. De restilizat + curățat duplicatele.
- **Blocaje posibile:** lipsă adresă default livrare/facturare → hidden input gol → validarea pică (UX: trebuie să existe/selectezi adresă).

---

## TASK 4 — PDF comandă ⚠️/🔴 EXISTĂ dar SUB nivelul ofertelor

- **Există** ✅ — `GenerateInvoiceJob` dispecerizat în `OrderController@store` (la plasare). Tip: `factura` (online) sau `proforma` (altele). DomPDF → `pdfs.factura` / `pdfs.proforma`, salvat în `storage/.../invoices/`, înregistrare `Invoice`.
- **🔴 Calitate sub PDF-ul de ofertă:** ofertele au PDF branded premium (logo TEXTURRA, footer corporate cu stats/social, paletă, DejaVu Sans). Factura/proforma comenzii = Arial, header simplu, **fără footer/branding**, și **`Helpers::stripDiacritics()` strică diacriticele românești** (ă, ț...).
- **De făcut:** aliniază PDF-ul comenzii la nivelul ofertei (footer branded, paletă, font cu diacritice).

---

## TASK 5 — Email-uri ✅ în mare FUNCȚIONAL (1 mailable nefolosit)

- **Confirmare comandă** ✅ — `SendOrderConfirmationEmailJob` → `OrderConfirmationMail` (view `emails.order_confirmation`), dispecerizat la plasare.
- **Reset parolă** ✅ — notificarea default Laravel (`Password::sendResetLink`).
- **Verificare email** ✅ — `CustomVerifyEmail` legat în modelul User.
- **❌ `UserAccountCreated`** — mailable definit dar **niciodată trimis** (cod mort sau de cablat).
- **Config** ✅ — `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit:1025`, `MAIL_FROM=no-reply@texturra.ro`. Mailpit în docker (UI :8025). Pe prod trebuie SMTP real.

---

## TASK 6 — Auth + reset parolă ✅ FUNCȚIONAL (social login rupt)

- **Login / Register / Logout** ✅ — Livewire class-based, `Auth::attempt` cu rate-limiting, session regenerate, hash parolă, merge coș la login/register, email verificare la register.
- **Reset parolă cap-coadă** ✅ — forgot (`Password::sendResetLink`) → email cu link → `reset-password/{token}` (`Password::reset`, token `#[Locked]`, parolă min 8 confirmată) → salvat + event `PasswordReset`.
- **Confirm password** ✅. **Cont client** (`/account`, change-password, my-orders, my-favorites) ✅ — toate merg, middleware `auth+verified+role:client`.
- **🔴 Social login rupt:** `SocialAuthController` folosește `Str::random()` **fără `use Illuminate\Support\Str;`** (eroare runtime) + **fără credențiale OAuth** în `.env` (github/facebook/tiktok). Nefuncțional până la fix import + config.
- **Notă stil:** paginile auth folosesc Tailwind modern; de verificat consistența cu redesign-ul (unele extind `layouts.base`, forgot/reset `layouts.guest`).

---

## Listă prioritizată de reparări (înainte de deployment)

**🔴 Blocante / bani / cont:**
1. **Badge coș** (TASK 1): setează `quantity` pe liniile custom (sau schimbă numărarea) — altfel coșul pare gol pentru perdele custom. + curăță `Livewire.emit`. + confirmă sesiunea guest în browser real.
2. **Plată online** (TASK 3): integrează gateway real (Netopia/Stripe) SAU ascunde opțiunea "online" până atunci (acum creează comanda fără să încaseze).
3. **Social login** (TASK 6): adaugă `use Illuminate\Support\Str;` + credențiale OAuth, SAU ascunde butoanele social.
4. **SMTP prod** (TASK 5): configurează un mailer real (acum mailpit local).

**⚠️ Importante (calitate/consistență):**
5. **PDF comandă** (TASK 4): rebranding la nivelul ofertei + fix diacritice (scoate `stripDiacritics`).
6. **Subtotal coș** (TASK 2): unifică logica controller vs view (acum controller-ul e greșit pe custom, mascat de view).
7. **Redesign coș + checkout** (TASK 2/3): stil vechi gri/negru → redesign 2026.
8. Curăță: rută orfană `checkout.process`, view-uri `checkout/index2`/`index_copy`, mailable `UserAccountCreated` nefolosit.

---

## Recomandări TESTE automate (plasă de siguranță)

Există deja: `CartMergeTest`, `CartAddStandardTest` (bun). De adăugat:
- **Coș/preț:** add custom → linie cu dimensiuni + preț corect; add standard → quantity sumată; **badge count include custom** (regression pt. TASK 1); subtotal corect custom+standard.
- **Checkout/comandă:** order.store creează Order + `order_product.meta` cu dimensiunile corecte; stock decrementat; voucher aplicat o singură dată; comandă blocată dacă stoc insuficient.
- **PDF/email:** GenerateInvoiceJob produce fișier; OrderConfirmationMail trimis (Mail::fake) la plasare.
- **Auth:** reset parolă cap-coadă (Notification::fake → token → parolă nouă); login rate-limit; merge coș guest→user păstrează dimensiuni (deja acoperit parțial).
- **Smoke pe paginile cheie** (deja există SmokeTest homepage/login) → extinde la /cart, /checkout, /produs, /produse/{slug}.
