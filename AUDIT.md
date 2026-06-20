# AUDIT.md — texturra.ro (Laravel 11, e-commerce perdele / produse custom)

**Tip:** audit read-only. Niciun fișier de cod modificat.
**Data:** 2026-06-20.
**Sursă:** carry-over dintr-un mediu de producție cPanel/shared hosting (vezi §4).

> Notă transversală importantă (Brief ≠ Realitate): codul de bază pare a fi **fork dintr-o aplicație de studio de tatuaje cu programări** (joburi `GenerateTattoo*`, `BookingReminder`, `tattoo:generate-*`, `reminders:*`). O parte semnificativă din cod este moștenită din acel domeniu și e moartă în contextul perdelelor. Detalii la §5.

---

## 0. Stare Git (de escaladat la planificare)

| Fapt | Valoare |
|---|---|
| Commits | **0** (`branch 'main' does not have any commits yet`) |
| Fișiere tracked | **0** — tot arborele e untracked |
| `.gitignore` | conține **doar** `node_modules` (13 bytes) — NU ignoră `.env`, `vendor/`, `/storage`, dump SQL, `__MACOSX/` |

> Implicație: dacă se face `git add .` acum, s-ar comite `texturra_baza.sql` (dump prod cu date clienți + hash parolă), `__MACOSX/`, `vendor/`, symlink `public/storage` cu cale prod, `docker-compose.yml` cu parole. `.gitignore` trebuie reparat **înainte** de primul commit (Faza 1).

---

## 1. Versiuni curente

### PHP / Laravel (composer.lock — versiuni instalate)
| Pachet | Constrângere (composer.json) | Instalat (lock) |
|---|---|---|
| php | `^8.2` | — |
| laravel/framework | `^11.9` | **v11.33.2** |
| livewire/livewire | `^3.5` | **v3.5.12** |
| **livewire/volt** | `^1.0` | **v1.6.6** — PREZENT și UTILIZAT |
| **filament/filament** | — | **NU este instalat** |
| laravel/horizon | `^5.29` | instalat |
| laravel/socialite | `^5.16` | instalat |
| laravel/ui | `^4.5` | instalat |
| laravel/breeze (dev) | `^2.2` | scaffolding auth |
| laravel/tinker | `^2.9` | instalat |
| barryvdh/laravel-dompdf | `^3.0` | facturi PDF |
| intervention/image | `^3.9` | procesare imagini (v3 — vezi §5, `Facades\Image` rupt) |
| phpoffice/phpspreadsheet | `^4.1` | import Excel furnizor "Safir" |
| openai-php/client | `^0.10.3` | instalat dar **NEFOLOSIT** (OpenAI apelat prin Guzzle brut) |
| mcamara/laravel-localization | `^2.0` | i18n |
| spatie/laravel-cookie-consent | `^3.3` | cookie banner |
| phpunit/phpunit (dev) | `^11.0.1` | teste |

### Frontend (package.json)
| Element | Valoare |
|---|---|
| Build tool | **Vite ^6.2.5** (`laravel-vite-plugin ^1.2.0`) — NU Mix |
| **Tailwind** | **^3.4.17** (NU v4) |
| Plugins Tailwind | forms, typography, aspect-ratio, **line-clamp (deprecat, inclus nativ în TW v3.3+)** |
| Alpine.js | ^3.14.3 (+ `@alpinejs/focus`) |
| Altele notabile | FullCalendar (5+6, moștenit din booking), flatpickr, moment, **puppeteer ^23.9**, sass, axios |

> Volt este folosit real în fluxurile auth + profile: `routes/web.php`, `routes/auth.php`, `app/Providers/VoltServiceProvider.php`, și `resources/views/livewire/pages/auth/*` + `livewire/profile/*` (login/register sunt clase Livewire normale; forgot/reset/verify/confirm + profile sunt Volt SFC). Eliminarea Volt (țintă) atinge aceste fișiere.

> Versiunile „ultima stabilă" pentru Laravel/Livewire/Filament/Tailwind se vor **verifica online la momentul fiecărui upgrade** (principiul 6), nu se presupun aici.

---

## 2. Suprafața de admin (custom, fără Filament)

**Acces:** prefix `/admin`, middleware `auth` + `CheckRole:admin,employee` (`routes/web.php:212-232`).

### Autentificare
- Login/Register: clase Livewire `App\Livewire\Pages\Auth\Login` / `Register` + `LoginForm`/`RegisterForm`. Rate-limit 5/IP+email doar pe login.
- Forgot/Reset/Verify/Confirm: componente **Volt**.
- Social: `SocialAuthController` (`/login/{provider}` + callback). **Provideri configurați real în `config/services.php`: facebook, github, tiktok — NU Google** (un raport intermediar spusese „Google"; fals la inspecție). `Str::random` folosit fără `use Str` → bug latent.
- Verificare email obligatorie (`CustomVerifyEmail`, signed + throttle 6/min).

### Gardă rol (enum pe user)
- Enum `App\Enums\UserType`: `admin, manager, employee, client, guest` (manager/guest definite dar neutilizate în rute).
- `users.type` (cast la `UserType`), default `client`. Metode `isAdmin()`, `isClient()`, `isRole()`.
- Middleware `App\Http\Middleware\CheckRole` — verifică `type->value` în lista de roluri; fără logare a accesului refuzat.
- Guard unic `web` (session), provider Eloquent `User`. Fără multi-guard.

### Componente admin (Livewire) și entitățile gestionate
| Rută | Componentă | Entitate / operații |
|---|---|---|
| `GET /admin` | `AdminDashboardController` | Dashboard (view) |
| `/admin/categories` | `Categories\Crud` | Categorii — CRUD ierarhic, soft delete |
| `/admin/products` | `Products\ProductList` | Produse — listă/filtre, archive/restore (`status`) |
| `/admin/products-emga` | `Products\ProductListEmag` | Produse cu EAN (typo „emga") |
| `/admin/products/create` | `Products\ProductCreate` | Creare produs + imagini + atribute + variații |
| `/admin/products/{id}/edit` | `Products\ProductEdit` | Editare produs, ștergere imagini, recreare variații |
| `/admin/products/filter` | `Products\ProductFilter` | UI filtre (helper) |
| `/admin/orders` | `Orders\Crud` | Comenzi — listă, update status/notes, **hard delete** |
| `/admin/change-password` | `Account\ChangePassword` | Parolă (scrie și `raw_password` în clar — vezi §5) |

**Cont client:** `/account/*` (`auth`+`verified`+`CheckRole:client`): profil, schimbare parolă, comenzi, favorite.

> Cod mort de admin: `app/Livewire/Products/Crud_old.php` (duplicat al `Crud.php`).

---

## 3. Modele + schema

### Modele (`app/Models/` — 30 fișiere). Entități core de business:
| Model | Tabel | Note relații |
|---|---|---|
| **Product** | `products` | `$guarded=[]` (fără `$fillable`). belongsTo Category/FamilyType/Vat; hasMany ProductStock, ProductVariation, Discount, SafirExcel |
| **ProductVariation** | `product_variations` | belongsTo Product; belongsToMany AttributeValue (pivot `product_variation_attribute_values`); cast `attribute_value_ids => array` |
| **Category** | `categories` | self parent/children/descendants; hasMany Characteristic, FamilyType, Product |
| **Order** | `orders` | belongsToMany Product (`order_product`); belongsTo User/Address/InvoiceAddress; hasMany Invoice, AwbLog; hasOne VoucherUsage. status enum: pending/placed/processing/completed/canceled |
| **Attribute / AttributeValue** | `attributes` / `attribute_values` | sistemul de variații |
| **Cart** | `carts` | belongsTo Product, ManufactoringType; câmpuri `length, height, manufactoring_type_id, pieces` (config custom perdele) |
| **OrderProduct** (pivot) | `order_product` | are `meta` (JSON) pentru dimensiuni custom |
| **User** | `users` | hasMany Address/InvoiceAddress/Order; cast `type` enum |
| Suport | — | Discount, Voucher, VoucherUsage, Invoice, AwbLog, ProductStock, FamilyType, Characteristic, ManufactoringType, Color, ColorGroup, Vat, Favorite, NewsletterSubscriber, SafirExcel, SafirProduct, EmagCategory, EmagApiResponse |

**Logica produs custom / variații (din `variations_logic.md` + cod):** Product → many ProductVariation (SKU/price/stock); fiecare variație leagă mai multe AttributeValue prin pivot `product_variation_attribute_values`. Attribute = tip (ex. Culoare, Material), AttributeValue = valoare concretă. Pentru perdele custom: dimensiuni (length/height) + tip manoperă (`ManufactoringType`) se rețin pe `carts` și în `order_product.meta` (JSON).

### Dump SQL (`texturra_baza.sql`, ~193KB, generat 2026-06-20)
| Atribut | Valoare |
|---|---|
| DB | `texturra_baza`, MySQL 8.0.46 |
| Engine | InnoDB (toate) |
| Charset / collation | `utf8mb4` / `utf8mb4_unicode_ci` |
| Tabele | **41** |

Tabele framework (fără model, normal): cache, cache_locks, failed_jobs, jobs, job_batches, migrations, password_reset_tokens, sessions.
Tabele notabile suplimentare în dump: **`products_old`** (tabel vechi rămas), **`whatsapp_logs`**, **`payments`** (vezi mai jos), `manufactoring_types`.

### Drift dump ↔ migrări
- Migrări: **78 fișiere** în `database/migrations/` (Jul 2024 → May 2025). Dump-ul are 41 de tabele. Discrepanță numerică normală (multe migrări sunt `alter`/add-column), dar **dump prod e mai nou decât migrările** (dump 2026 vs ultima migrare May 2025) → posibile coloane în prod neacoperite de migrări. De verificat la Faza 1 după `migrate` pe dump.
- `products_old` în dump nu are migrare/model → rest de migrare manuală în prod.

### Mismatch model ↔ coloană (fatale latente — de escaladat)
| Model | Câmp model | Coloană SQL | Severitate |
|---|---|---|---|
| **Color** | `css_code` (în `$fillable`) | coloana reală e **`cod_css`** | 🔴 FATAL — scrierea eșuează silențios |
| AwbLog | `awb_number`, `courier_name` | inexistente; SQL are `courier_type` | 🟡 date nepersistate |
| Category | (lipsesc din `$fillable`) | `name_seo, description_seo, image, source, source_link, source_status` | 🟡 mass-assign imposibil |
| Product | n/a (`$guarded=[]`) | coloane extra: `buc_set, set_bax, buc_bax, safir_excel_*, description_plain` | 🟠 informativ |

---

## 4. Integrări & infra

### Integrări externe
| Integrare | Pachet/metodă | Config | Folosit în |
|---|---|---|---|
| OpenAI GPT-4 / DALL-E 3 | **Guzzle brut** (SDK `openai-php` NEfolosit) | `config/app.php` `open_api_Key` ← `OPENAI_API_KEY` | `app/Jobs/GenerateTattoo{Prompt,Image}Job.php` — **moștenit din booking, nedispecerizat nicăieri** |
| WhatsApp (Meta Graph) | Guzzle brut | `WHATSAPP_*` inline | `app/Jobs/SendWhatsAppNotification.php` — **nedispecerizat** |
| Socialite | `laravel/socialite` | facebook/github/tiktok | `SocialAuthController` |
| Mail | Laravel built-in | `config/mail.php` default mailer `log`; SMTP `127.0.0.1:2525` | Mailables: OrderConfirmation, Booking*, UserAccountCreated; Notification CustomVerifyEmail |
| PDF facturi | dompdf | `config/pdf.php` | `GenerateInvoiceJob` |
| **Plăți** | **NICIUNA** | — | `payment_method` = string liber; `payments`/`Payments\Crud` = stub gol; **nu se încasează card** |
| SMS | NICIUNA | — | doar linie comentată în HorizonServiceProvider |
| eMAG marketplace | Guzzle/controllere | — | `EmagMarketplaceController`, `EmagProductsMarketplaceController` (rute neautentificate — §5) |
| Safir (furnizor) | scraping + Excel | — | 10 comenzi `app/Console/Commands/*Safir*` (rulate manual) |

### Queue / Jobs / Scheduling
- **Horizon** instalat, supervizor pe conexiune **redis**, queue `default`. Gate `viewHorizon` cu allow-list goală.
- **Mismatch:** `config/queue.php` default = **`database`**, dar Horizon procesează **redis** → fără `QUEUE_CONNECTION=redis` în `.env`, joburile merg în DB și Horizon nu le ia.
- **Scheduling: NICIUNUL.** `routes/console.php` gol (6 bytes); fără `->withSchedule()` în `bootstrap/app.php`; comenzile Safir/reminders rulate manual.
- `bootstrap/app.php` referă `routes/channels.php` care **NU există** → posibilă eroare la boot (de verificat la Faza 1).
- Anti-pattern: `GenerateTattooPromptJob::handle()` apelează `Artisan::call('queue:work ...')` din interiorul jobului.

### Infra / deployment
- **Prod real = cPanel/Apache shared hosting** (NU Docker): `php.ini` cu header „cPanel-generated", `ea-php83`, `session.save_path` cPanel; `htaccess_prod` rescrie totul în `public/` (hack Laravel-în-home cPanel); `cgi-bin/` (gol); symlink `public/storage -> /home/texturra/dev1.texturra.ro/...`.
- `docker-compose.yml` = Laravel **Sail** pt. local (app sail-8.2, mysql 8.0, redis, **mailhog** nu mailpit). **Fără nginx/node/horizon containers.** **Parole hardcodate: `MYSQL_ROOT_PASSWORD: <redacted-dev-pw>`, `MYSQL_PASSWORD: <redacted-dev-pw>`, `MYSQL_ALLOW_EMPTY_PASSWORD: 1`** (§5).
- `docker-compose-entrypoint.sh` (nelegat în compose): `artisan migrate` + `apache2-foreground`.
- **CI/CD: niciunul** (fără `.github/`, `.gitlab-ci.yml`, deploy scripts).

### Teste
- `phpunit.xml`: skeleton standard; `DB_CONNECTION`/`DB_DATABASE` **comentate** → testele lovesc DB-ul default (nu sqlite izolat). `tests/TestCase.php` gol.
- **10 fișiere / 26 metode — toate scaffolding Breeze (auth/profile) + 2 ExampleTest.** **Acoperire business = ZERO** (nimic pe comenzi/coș/produse/variații/vouchere/facturi).

---

## 5. Riscuri de escaladat (CRITIC)

### Securitate — INCIDENTE

| # | Sev. | Finding | Locație | Dovadă |
|---|---|---|---|---|
| S1 | 🔴 CRITIC | **Execuție de comenzi Artisan din web** cu gardă slabă | `app/Http/Controllers/CommandController.php`; rute `routes/web.php:30-40`; `app/Http/Middleware/VerifySecretKey.php` | `/commands/clear-cache`, `/create-storage-link`, `/start-queue-worker/{queue?}`, `/generate-images` → `Artisan::call(...)`. Garda = `?key=` (GET, ajunge în log-uri) cu `!==` (non-constant-time). Dacă `APP_SECRET_KEY` e gol → default `''` (`config/app.php:8`) → cheie goală autentifică = **practic neautentificat**. State-changing pe GET (CSRF). |
| S2 | 🔴 CRITIC | **Injectare argument în queue worker** | `CommandController.php:53,172`; param `{queue?}` din `routes/web.php:31` | `"queue:work --queue={$queue} ..."` cu `$queue` nesanitizat interpolat în comandă. |
| S3 | 🔴 CRITIC | **Rute eMAG complet neautentificate** care modifică date în masă | `routes/web.php:238-252` (middleware doar `web`) | `/emag/update-vat-rates` (modifică TVA/prețuri), `/emag/process-all-products`, `/emag/refetch-images`, `/emag/fix-category-parents` etc. — fără auth, fără rate-limit. |
| S4 | 🟠 HIGH | **Rute `/utilities/*` neautentificate** | `routes/web.php:44-94` | `/utilities/generate-invoice/{orderId}`, `/send-email/{orderId}` → enumerare comenzi + dispecerizare joburi/emailuri pe orice ID. |
| S5 | 🟠 HIGH | **Parolă în clar în DB** | `app/Livewire/Account/ChangePassword.php:35`; `users.raw_password` | Se scrie parola plaintext lângă hash → PII expus la breșă DB. |
| S6 | 🟠 HIGH | **Parole DB hardcodate + empty password permis** | `docker-compose.yml` | `<redacted-dev-pw>` x2 + `MYSQL_ALLOW_EMPTY_PASSWORD: 1`. |
| S7 | 🟡 MED | **Dump prod cu date clienți în arbore** | `texturra_baza.sql` | schemă+date reale, 1 hash bcrypt. Ar fi servit web dacă docroot greșit (proiect în loc de `public/`). Nu e în `.gitignore`. |
| S8 | 🟡 MED | **Cale prod scursă prin symlink** | `public/storage -> /home/texturra/dev1.texturra.ro/...` | divulgă user/cale server; rupt în alt mediu (regenerează cu `storage:link`). |
| S9 | 🟡 MED | **Resturi arhivă `__MACOSX/`** (cu `.git` nested + `._.env`) | `__MACOSX/` | stub-uri AppleDouble 163B (inerte). Confirmă că arhiva-sursă **conținea un `.env`** (acum absent pe disk). De șters integral. |
| S10 | 🟢 LOW | `cgi-bin/` (gol), `public/.htaccess2`, `public/.DS_Store` | root/public | curățenie; `cgi-bin` = loc clasic de drop webshell. |

> ✅ **Curat (verificat explicit):** NU există webshell, cod ofuscat/împachetat, `eval`/`gzinflate`/`system`/`shell_exec`. NU există `.env` comis sau pe disk; NU există secrete hardcodate în `app/`/`config/`/`routes/` (toate `env(...)`). `vendor/` **nemodificat** malițios. `public/` conține doar `index.php` stock. Singurele `base64_decode` sunt handler legitim de upload data-URI (`Products/Crud.php:537`). Git are **0 commits** → nimic comis încă (dar `.gitignore` insuficient — §0).

### Cod mort / deprecat care va bloca upgrade-ul

| Tip | Detaliu |
|---|---|
| **Domeniu străin (tatuaje/booking)** | Joburi `GenerateTattooPromptJob(.Old/.OLD2)`, `GenerateTattooImageJob`, `SplitTattooImageJob`, `SendBookingReminderEmail(+Employee)`, `SendWhatsAppNotification`; comenzi `tattoo:*`, `reminders:*`; Mailables Booking*; FullCalendar în front. Toate **nedispecerizate** în fluxul perdele. Candidate la ștergere (Faza 2/6). |
| **Volt** | Folosit în auth(forgot/reset/verify/confirm) + profile. Țintă: scoatere → SFC nativ. |
| **`$guarded = []` pe Product** | mass-assignment deschis — risc + sursă de drift. |
| **Mismatch Color `css_code`→`cod_css`** | 🔴 bug care corupe tăcut (vezi §3). |
| **Intervention Image v3** | `SplitTattooImageJob` folosește `Intervention\Image\Facades\Image` — **clasă eliminată în v3** → fatal dacă s-ar rula. |
| **`@tailwindcss/line-clamp`** | plugin deprecat (nativ în TW v3.3+); blochează TW v4. |
| **`routes/channels.php` lipsă** | referit în `bootstrap/app.php` → posibil boot error. |
| **`products_old`, `Crud_old.php`, `GenerateTattooPromptJobOld/OLD2`** | reziduuri de curățat. |
| **Queue default `database` vs Horizon `redis`** | misconfigurare funcțională. |

---

## Recomandare de ordine (de confirmat la planificare)

1. **Faza 1 — Dockerizare app neatins pe dump.** Înainte: reparat `.gitignore` (S7/S9), confirmat docroot `public/`, regenerat symlink storage (S8), `APP_SECRET_KEY` non-gol. Verificat boot (`channels.php` lipsă), `migrate` pe dump și drift coloane prod.
2. **Faza 2 — Securitate + curățenie + plasă teste business.** Închis/eliminat `/commands` `/emag` `/utilities` (S1-S4); eliminat `raw_password` (S5); fix Color (`cod_css`); șters cod tatuaje/booking + `*_old`; teste pe comenzi/coș/variații.
3. **Fazele 3-6** (upgrade Laravel → Livewire/Volt-out → Filament `/admin` → curățenie) — după ce baza e verde.

**STOP.** Aștept planul de faze cu prompturi atomice. Nu încep Faza 1.
