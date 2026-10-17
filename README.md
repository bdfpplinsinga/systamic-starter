# Systamic starter kit

Statamic 6 starter kit van Buro Toast: Antlers componenten, Tailwind 4, Alpine.js en een page builder met negen blokken.

> Deze repo wordt gegenereerd. Pas niets hier aan, maar werk in [`systamic-starter-site`](https://github.com/bdfpplinsinga/systamic-starter-site) en exporteer opnieuw.

## Nieuw project starten

```bash
statamic new mijn-project bdfpplinsinga/systamic-starter
```

Of in een bestaande, verse Statamic installatie:

```bash
php please starter-kit:install bdfpplinsinga/systamic-starter
```

Tijdens de installatie krijg je twee vragen:

| Module | Standaard | Wat het doet |
|---|---|---|
| SEO Pro | ja | Installeert `statamic/seo-pro` (betaalde addon). Voegt een SEO-tab toe aan elke pagina. Zonder SEO Pro valt de site terug op titel + meta description. |
| Demo | nee | Voegt de pagina `/blokken` toe met alle blokken en voorbeeldafbeeldingen. |

Daarna:

```bash
php please make:user
npm install
composer dev
```

## Wat zit erin

- **Componenten** (`resources/views/components`): button, heading, prose, icon, picture, video, modal, section, container, content. Allemaal met `tw_merge`, zodat een `class` van buitenaf wint.
- **Page builder** met de blokken hero, tekst, media & tekst, call to action, afbeelding, galerij, video, FAQ en kaarten.
- **Fieldsets in drie lagen**: velden → groepen (`content`, `block_settings`) → blokken.
- **Design tokens** in `resources/css/site.css` (`@theme`). Pas die per project aan.
- **`/styleguide`** (alleen lokaal) met alle componenten.

De conventies staan in `CLAUDE.md`.

## Kit bijwerken (voor beheerders)

In `systamic-starter-site`:

```bash
composer export          # schrijft naar ../systamic-starter
cd ../systamic-starter
git diff                 # controleren
git commit -am "..." && git push
```
