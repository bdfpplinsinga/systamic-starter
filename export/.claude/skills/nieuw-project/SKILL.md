---
name: nieuw-project
description: Set up a freshly installed Systamic site for a client project (brand tokens, fonts, logo and favicon, globals, menus, legal pages, forms and mail, SEO, cookies and tracking, go-live checks). Use right after "statamic new ... bdfpplinsinga/systamic-starter", or when the user says "nieuw project", "zet de site op voor <klant>" or asks for a go-live check.
---

# Set up a new Systamic project

Follow the conventions in `CLAUDE.md`. Work through the steps in order. Ask for the client's details (name, brand colours, font, logo, contact details, socials) at the start, and list what is still missing at the end. Never make up company details, KvK numbers or legal text.

## 1. Check the installation

- Which modules are installed? Check `content/collections/*.yaml` and `content/collections/pages/`. Remove nothing without asking.
- `composer install`, `npm install`, `php please make:user` (the user does this themselves, it needs a password), then `composer dev` or Herd.
- `.env`: `APP_NAME`, `APP_URL`, `APP_ENV=local`. Leave `MAIL_*` for step 6.

## 2. Brand: `resources/css/site.css`

- Change only the `@theme` tokens: `--color-primary` (+ `-contrast`), `--color-secondary` (+ `-contrast`), `surface`, `surface-muted`, `ink`, `ink-muted`, and `accent` if it should differ from primary. Use `oklch()`, like the existing tokens.
- Check contrast: `primary-contrast` on `primary`, and `ink-muted` on `surface` and `surface-muted`, must be at least 4.5:1 (WCAG AA). Report any pair that fails.
- Fonts: `npm i @fontsource-variable/<font>` (or `@fontsource/<font>` if there is no variable version), swap the `@import` and set `--font-sans` and/or `--font-heading`. Never Google Fonts. Remove `@fontsource-variable/inter` if it is no longer used.
- Don't change components to match the brand. If the design needs it, adjust the component's base classes or add a variant, and update `/styleguide`.

## 3. Globals (control panel, or `content/globals/*.yaml`)

- **Bedrijfsgegevens** (`company`): name, address, KvK, phone, e-mail, opening hours. These feed the footer, the contact form e-mail and the JSON-LD.
- **Social media** (`social`).
- **Algemeen** (`general`): logo (SVG preferred), favicon, share image (1200 × 630), header buttons, footer text.
- **Scripts** (`scripts`): GTM ID in `gtm_id`. Other tracking code goes in the matching cookie group, never in templates.

## 4. Pages and menus

- Home: build it with the page builder. Remove the `/blokken` demo page before go-live (entry, tree, nav).
- Menus: `main` (header), `footer`, `legal` (Privacyverklaring, Algemene voorwaarden). Check that every menu item points to an existing page.
- Privacyverklaring and Algemene voorwaarden: the user supplies the text. Flag them as a go-live blocker as long as they contain placeholder text.
- Remove module example entries (news, jobs, team, ...) or replace them with real content, in agreement with the user.

## 5. Cookies

- Texts in `content/cookie-dialog`: check them against the scripts that are actually used (groups `essential`, `statistics`, `marketing`, `external_media`).

## 6. Forms and mail

- `MAIL_*` in `.env` (production values go on the server, never in git). The contact form e-mails the address from Bedrijfsgegevens. Sending goes through the shared Mailgun account (`mg.burotoast.nl`, one SMTP user per site): follow `docs/mail.md`.
- New form fields: never use the handle of a global (`company`, `social`, `general`, `scripts`), or the mail loses its recipient. Run `php artisan test` (`FormEmailTest`) after adding a form.
- Test sending, including validation messages (`lang/nl/validation.php`) and the honeypot. A filled honeypot still shows the thank-you message but stores and sends nothing; that is intended. With `MAIL_MAILER=log` the mail ends up in `storage/logs/laravel.log`.

## 7. SEO

- With SEO Pro (`{{ installed:statamic/seo-pro }}`): site defaults (title format, default share image) in the control panel. Redirects for an old site go in SEO Pro.
- Without SEO Pro: the share image comes from `general:share_image`.
- Fill in a meta description for each page.

## 8. Go-live checklist

Go through this list and report per item: OK, open, or for the user to do.

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL` (otherwise `robots.txt` and `noindex` block indexing).
- [ ] Static caching works in production; forms and cookie code sit in `{{ nocache }}`.
- [ ] `npm run build` has been run; the server serves `public/build`.
- [ ] `php please search:update --all` and `php please stache:warm` in the deploy script.
- [ ] Every image has an alt text (dashboard widget "Afbeeldingen zonder alt-tekst").
- [ ] pa11y on home, contact and each module overview: `npx --yes pa11y@10 --runner axe --standard WCAG2AA <url>`.
- [ ] Favicon, share image and logo are shown correctly (check og:image with a link preview).
- [ ] Contact form tested on the production mail setup.
- [ ] Cookie dialog: tracking only loads after consent (check the network tab).
- [ ] The `/blokken` demo page and example content are gone; legal pages are final.
- [ ] Redirects from the old site are in place (SEO Pro or `routes/web.php`).
