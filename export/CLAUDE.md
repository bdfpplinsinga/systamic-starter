# Systamic

This project uses the **Systamic** Statamic starter kit. Its source is the development site `bdfpplinsinga/systamic-starter-site`. There you build and test, then run `composer export` (that is, `php please starter-kit:export ../systamic-starter --clear`) to update the kit repo `bdfpplinsinga/systamic-starter`. New projects install the kit with `statamic new <name> bdfpplinsinga/systamic-starter`. Kit configuration lives in `package/` (`starter-kit.yaml`, `composer.json`, `StarterKitPostInstall.php`, `README.md`), which only exists in the dev site.

Stack: Statamic 6, Laravel 13, Antlers, Tailwind 4 (Vite), Alpine.js (with the collapse and focus plugins), `marcorieser/tailwind-merge-statamic`.

Statamic docs (6.x) are public at github.com/statamic/docs (`content/collections`). Check them; don't rely on memory.

## Conventions

- **Components** live in `resources/views/components/*.antlers.html` and are called with `<x-name prop="..." />`. They are isolated: pass data in as props, binding objects such as assets with `:image="image"`.
  - Each component documents its props in an Antlers comment at the top.
  - Classes are always combined with `[base, variant_class, class] | tw_merge`, so a `class` passed in from outside wins.
  - Other HTML attributes are passed through with `{{ attributes:except([...props]) }}`.
  - Named slots: `<x-slot:trigger>` in the caller becomes the `{{ trigger }}` variable in the component.
  - Components do not see globals or page data. Pass them in: `<x-social-links :links="social:social_links" />`. Partials (`partials/logo`) do see the cascade.
- **Design tokens** live only in `@theme` in `resources/css/site.css` (`primary`, `secondary`, `surface`, `ink`, `accent`, …). Components use these tokens, never hard-coded Tailwind colours. Use `accent` (not `primary`) for eyebrows and links: a `background="primary"` section remaps `accent` so it stays readable.
- **Fieldsets come in three layers.** Each layer only imports from the layer below it:
  1. **Fields**, one responsibility each: `heading` (heading + heading_level, default h2), `text`, `image`, `images`, `button`, `buttons`, `video`.
  2. **Groups**: `content` (eyebrow, heading, text, buttons) and `block_settings` (background, anchor).
  3. **Blocks**: `block_*.yaml`, made of fields and groups only.
- **Page builder**: `page_builder.yaml` is a replicator. Each set imports `block_<name>` plus `block_settings`, so block fieldsets stay free of settings and can be reused elsewhere. `partials/page_builder` renders each set through `resources/views/blocks/{type}.antlers.html` with `scope="block"`, and block views read everything from `block:*`. A new block means a `block_<name>.yaml`, a set in `page_builder.yaml`, a view in `blocks/` and an example on the `/blokken` page.
- **Spacing is set by the design, not the editor.** Every block view wraps itself in `<x-section :background="block:background" :id="block:anchor">`. Only the developer picks `spacing` (the hero uses `lg`).
- **Heading level vs size**: the editor picks the semantic level (`heading_level`). The block view picks the visual size (`<x-heading :as="block:heading_level" size="xl">`).
- `<x-content :block="block" />` renders the `content` group, so blocks never repeat that markup.
- **Antlers gotchas**:
  - A ternary inside an array literal breaks `tw_merge` (`[a, x ? 'b' : '', class]` drops classes). Assign the ternary to a variable first.
  - A modifier binds tighter than `??`: `{{ a ?? b | to_json }}` only encodes `b`. Write `{{ (a ?? b) | to_json }}`.
- **Globals** (Dutch labels): `company` (Bedrijfsgegevens: name, address, KvK, opening hours), `social` (Social media), `site` (Algemeen: logo, favicon, share image, header buttons, footer text), `scripts` (tracking code per cookie group). Header, footer, `partials/head_meta` and `partials/structured_data` read them; add new site-wide settings there, not to page blueprints.
- **Cookies and scripts**: the Cookie Dialog addon (`emplify-software/statamic-cookie-dialog`) shows the consent dialog; its texts live in `content/cookie-dialog`. Third-party code goes in the `scripts` global, never in templates: `partials/scripts` only outputs a script outside the `essential` group inside `{{ cookie:allowed }}`.
- **Forms**: config in `resources/forms`, fields in `resources/blueprints/forms`. The `form` block renders any form via `{{ form:fields }}`; field styling sits in the `form-theme` utility in `site.css`. Every form needs a `honeypot`. Validation messages are in `lang/nl/validation.php`.
- **Legal pages and menus**: `main` (header), `footer` and `legal` (bottom bar: Privacyverklaring, Algemene voorwaarden, plus the cookie settings button).
- **News** is an optional kit module (`news` collection, `/nieuws/{slug}`, views in `resources/views/news`). Blocks must not depend on it: `latest_entries` takes any collection.
- **Navigation**: always use `{{ nav:handle as="links" }}{{ links }}…{{ /links }}{{ /nav:handle }}`. Without `as`, an empty nav renders the current page's context.
- **SEO**: `partials/head_meta` uses SEO Pro when it is installed (`{{ installed:statamic/seo-pro }}`), otherwise a plain fallback that uses `site:share_image` for og:image. With SEO Pro, set the default share image in its site defaults. Nothing else may assume SEO Pro is installed.
- **Assets**: one container, `assets` (`public/assets`). Glide presets for `<x-picture>` are in `config/statamic/assets.php`.
- `/styleguide` (local env only) shows every component. Add a new component there too.
- Labels in the control panel and front end are in Dutch.
