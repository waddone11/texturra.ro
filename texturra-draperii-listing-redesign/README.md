# Texturra — Listare Draperii (concept implementabil)

## Conținut

- `index.html` — prototip static responsive, cu layout, CSS și micro-interacțiuni de bază.
- `DESIGN-SPEC-RO.md` — explicația completă a deciziilor de design.
- `AI-IMPLEMENTATION-PROMPT-RO.md` — prompt complet pentru implementare într-un agent AI / Codex / developer.
- `assets/` — imagini de concept utilizate de prototip.

## Rulare locală

Deschide `index.html` în browser. Pentru a evita eventuale limitări la încărcarea fișierelor locale, rulează din folder:

```bash
python3 -m http.server 8080
```

Apoi deschide `http://localhost:8080`.

## Observații importante

- Datele de produs, numărul de produse, contactele și metodele de plată sunt de demonstrație și trebuie mapate la datele reale Texturra.
- Logo-ul este un placeholder tipografic; în producție se va înlocui cu SVG-ul oficial.
- CSS-ul este standalone pentru review rapid. În aplicația reală, separă-l pe componente și adaptează-l la Tailwind / design tokens, dacă proiectul folosește Tailwind.
- Imaginile sunt concept visuals, nu fotografii de produs cu SKU-uri validate. Le înlocuiești cu imagini reale sau cu seturile finale de campanie.
