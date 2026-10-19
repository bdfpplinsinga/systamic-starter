# Systamic starter kit

Statamic 6 starter kit van Buro Toast: Antlers componenten, Tailwind 4, Alpine.js, een page builder met veertien blokken, globals, een contactformulier en cookietoestemming.

> Deze repo wordt gegenereerd. Pas niets hier aan, maar werk in [`systamic-starter-site`](https://github.com/bdfpplinsinga/systamic-starter-site) en exporteer opnieuw.

## Nieuw project starten

```bash
statamic new mijn-project bdfpplinsinga/systamic-starter
```

Of in een bestaande, verse Statamic installatie:

```bash
php please starter-kit:install bdfpplinsinga/systamic-starter
```

Tijdens de installatie krijg je drie vragen:

| Module | Standaard | Wat het doet |
|---|---|---|
| SEO Pro | ja | Installeert `statamic/seo-pro` (betaalde addon). Voegt een SEO-tab toe aan elke pagina. Zonder SEO Pro valt de site terug op titel + meta description. |
| Nieuws | nee | Collectie `news` met overzicht op `/nieuws`, berichtpagina's en drie voorbeeldberichten. |
| Demo | nee | Voegt de pagina `/blokken` toe met alle blokken en voorbeeldafbeeldingen. |

Daarna:

```bash
php please make:user
npm install
composer dev
```

Vul in het control panel de globals in (Bedrijfsgegevens, Social media, Algemeen), zet `MAIL_*` in `.env` voor het contactformulier en vul de privacyverklaring en algemene voorwaarden in.

## Wat zit erin

- **Componenten** (`resources/views/components`): button, heading, prose, icon, picture, video, modal, section, container, content, breadcrumbs, social-links, entry-card en slider (Swiper, toegankelijk, met slides via de slot). Allemaal met `tw_merge`, zodat een `class` van buitenaf wint.
- **Page builder** met de blokken hero, tekst, media & tekst, call to action, kaarten, FAQ, testimonials, team, afbeelding, galerij (raster of slider), video, logo's, formulier en laatste berichten.
- **Globals**: Bedrijfsgegevens (adres, KvK, openingstijden), Social media, Algemeen (logo, favicon, deelafbeelding, headerbuttons, footertekst) en Scripts (Google Tag Manager-ID en overige trackingcode per cookiegroep).
- **Contactpagina** met formulier: honeypot, Nederlandse foutmeldingen en een e-mail naar het adres uit Bedrijfsgegevens.
- **Cookietoestemming** met [Cookie Dialog](https://statamic.com/addons/emplify-software/cookie-dialog). Scripts uit de global Scripts laden pas na toestemming. Google Tag Manager draait met Consent Mode v2: Statistieken geeft analytics vrij, Marketing de advertentiesignalen.
- **SEO-basis**: favicon, Open Graph met fallback-afbeelding, JSON-LD voor de organisatie (en per nieuwsbericht).
- **Juridische pagina's** (privacyverklaring, algemene voorwaarden) in een eigen menu onderaan de footer, plus kruimelpad.
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
# Een GitHub Action zet bij elke push een nieuwe patch-tag (v0.1.1, v0.1.2, ...).
# Voor een grotere stap tag je zelf, bijv. v0.2.0; daarna telt de action verder.
```
