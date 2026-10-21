# Systamic

This project uses the **Systamic** Statamic starter kit. Its source is the development site `bdfpplinsinga/systamic-starter-site`. There you build and test, then run `composer export` (`php please starter-kit:export ../systamic-starter --clear`, then restores `.github` in the kit repo, which `--clear` deletes) to update the kit repo `bdfpplinsinga/systamic-starter`. A GitHub Action in the kit repo tags every push to main with the next patch version. New projects install the kit with `statamic new <name> bdfpplinsinga/systamic-starter`. Kit configuration lives in `package/` (`starter-kit.yaml`, `composer.json`, `StarterKitPostInstall.php`, `README.md`), which only exists in the dev site.

Stack: Statamic 6, Laravel 13, Antlers, Tailwind 4 (Vite), Alpine.js (with the collapse and focus plugins), `marcorieser/tailwind-merge-statamic`.

Statamic docs (6.x) are public at github.com/statamic/docs (`content/collections`). Check them; don't rely on memory.

## Conventions

- **Components** live in `resources/views/components/*.antlers.html` and are called with `<x-name prop="..." />`. They are isolated: pass data in as props, binding objects such as assets with `:image="image"`.
  - Each component documents its props in an Antlers comment at the top.
  - Classes are always combined with `[base, variant_class, class] | tw_merge`, so a `class` passed in from outside wins.
  - Other HTML attributes are passed through with `{{ attributes:except([...props]) }}`.
  - Multi-word props are camelCased by Blade: `per_view="3"` in the caller is `{{ perView }}` inside the component.
  - Named slots: `<x-slot:trigger>` in the caller becomes the `{{ trigger }}` variable in the component.
  - Components do not see globals or page data. Pass them in: `<x-social-links :links="social:social_links" />`. Partials (`partials/logo`) do see the cascade.
- **Design tokens** live only in `@theme` in `resources/css/site.css` (`primary`, `secondary`, `surface`, `ink`, `accent`, …). Components use these tokens, never hard-coded Tailwind colours. Use `accent` (not `primary`) for eyebrows and links: a `background="primary"` section remaps `accent` so it stays readable.
- **Fieldsets come in three layers.** Each layer only imports from the layer below it:
  1. **Fields**, one responsibility each: `heading` (heading + heading_level, default h2), `text`, `image`, `images`, `button`, `buttons`, `video`.
  2. **Groups**: `content` (eyebrow, heading, text, buttons) and `block_settings` (background, anchor).
  3. **Blocks**: `block_*.yaml`, made of fields and groups only.
- **Page builder**: `page_builder.yaml` is a replicator. Each set has a preview image (`image: <set>.jpg`, from `public/assets/set-previews`, see `set_preview_images` in `config/statamic/assets.php`); a new block gets a screenshot there too. In `block_settings`, a revealer toggle (`show_settings`, not saved) shows background and anchor only on demand. Each set imports `block_<name>` plus `block_settings`, so block fieldsets stay free of settings and can be reused elsewhere. `partials/page_builder` renders each set through `resources/views/blocks/{type}.antlers.html` with `scope="block"`, and block views read everything from `block:*`. A new block means a `block_<name>.yaml`, a set in `page_builder.yaml`, a view in `blocks/` and an example on the `/blokken` page.
- **Spacing is set by the design, not the editor.** Every block view wraps itself in `<x-section :background="block:background" :id="block:anchor">`. Only the developer picks `spacing` (the hero uses `lg`).
- **Heading level vs size**: the editor picks the semantic level (`heading_level`). The block view picks the visual size (`<x-heading :as="block:heading_level" size="xl">`).
- `<x-content :block="block" />` renders the `content` group, so blocks never repeat that markup.
- **Antlers gotchas**:
  - A ternary inside an array literal breaks `tw_merge` (`[a, x ? 'b' : '', class]` drops classes). Assign the ternary to a variable first.
  - A modifier binds tighter than `??`: `{{ a ?? b | to_json }}` only encodes `b`. Write `{{ (a ?? b) | to_json }}`.
- **Globals** (Dutch labels): `company` (Bedrijfsgegevens: name, address, KvK, opening hours), `social` (Social media), `general` (Algemeen: logo, favicon, share image, header buttons, footer text; not `site`, which would shadow Statamic's own `site` variable), `scripts` (tracking code per cookie group). Header, footer, `partials/head_meta` and `partials/structured_data` read them; add new site-wide settings there, not to page blueprints.
- **Cookies and scripts**: the Cookie Dialog addon (`emplify-software/statamic-cookie-dialog`) shows the consent dialog; its texts live in `content/cookie-dialog`. Third-party code goes in the `scripts` global, never in templates: `partials/scripts` only outputs a script outside the `essential` group inside `{{ cookie:allowed }}`. Google Tag Manager has its own field (`scripts:gtm_id`): `partials/gtm` loads it with Consent Mode v2 (all denied by default; `statistics` grants analytics_storage, `marketing` grants the ad_* signals).
- **Forms**: config in `resources/forms`, fields in `resources/blueprints/forms`. The `form` block renders any form via `{{ form:fields }}`; field styling sits in the `form-theme` utility in `site.css`. Every form needs a `honeypot`. Validation messages are in `lang/nl/validation.php`.
- **Legal pages and menus**: `main` (header), `footer` and `legal` (bottom bar: Privacyverklaring, Algemene voorwaarden, plus the cookie settings button).
- **News** is an optional kit module (`news` collection, `/nieuws/{slug}`, mounted on the `nieuws` page, views in `resources/views/news`). It has the taxonomy `categorie` (`/nieuws/categorie/{slug}`, view `news/categorie/show`), an RSS feed on `/nieuws/feed` (route only exists when `news/feed.antlers.html` does) and `<x-share>` on each article. Blocks must not depend on it: `latest_entries` takes any collection.
- **Jobs, services and team** are optional kit modules too, built like news (collection yaml + entries, page in `pages` with template `{handle}/index`, blueprints, views in `resources/views/{handle}`; the post-install script hangs the page in the pages tree and main nav). `jobs` (`/vacatures/{slug}`, dated) hides entries past `closing_date` with the query scope `open_jobs` (`app/Scopes/OpenJobs.php`; compare dates as Carbon, a date string fails in the Stache) and outputs JobPosting JSON-LD. `services` (`/diensten/{slug}`) is a structured collection with an `icon` per entry. `team` has no route; members render with `<x-team-member>`, which the team block uses as well. The search index filter `App\Search\HasUrl` keeps routeless entries out of results.
- **Navigation**: always use `{{ nav:handle as="links" }}{{ links }}…{{ /links }}{{ /nav:handle }}`. Without `as`, an empty nav renders the current page's context.
- **Environments**: `robots.txt` (route + `resources/views/robots.antlers.html`) and a `noindex` meta tag in `partials/head_meta` block indexing outside `production`. Static caching (half measure) is on by default only in production; `/zoeken` is excluded, and anything per visitor (forms, cookie code) goes in `{{ nocache }}`.
- **Search**: index `default` in `config/statamic/search.php` covers all collections; `App\Search\PageBuilderText` turns the page builder into plain text. Results on `/zoeken` (route + `resources/views/search.antlers.html`).
- **Icons**: Heroicons in `resources/svg/icons/{outline,solid,socials}`, used with `<x-icon>`. The outline set is also registered as icon set `site` (AppServiceProvider) for the `icon` fieldtype; add an SVG there to make it pickable.
- **External embeds** (maps, etc.) load only after consent for the cookie group `external_media`: wrap them in `{{ cookie:allowed group="external_media" }}` with a `{{ cookie:denied }}` placeholder, inside `{{ nocache }}`.
- **Fonts** are self-hosted with Fontsource (`@import "@fontsource-variable/…"` in `site.css`). Never load Google Fonts.
- **SEO**: `partials/head_meta` uses SEO Pro when it is installed (`{{ installed:statamic/seo-pro }}`), otherwise a plain fallback that uses `general:share_image` for og:image. With SEO Pro, set the default share image in its site defaults. Nothing else may assume SEO Pro is installed.
- **Assets**: one container, `assets` (`public/assets`). Glide presets for `<x-picture>` are in `config/statamic/assets.php`.
- **Slider**: `<x-slider label="…" per_view="3">` with `<x-slide>` children. Swiper (with A11y, Keyboard, Navigation, Pagination) is loaded on demand from `resources/js/slider.js`, only on pages that have a slider. The gallery block has a `layout` option (grid or slider).
- **Dashboard**: widget `images_missing_alt` (`app/Widgets/ImagesMissingAlt.php`, Blade view in `resources/views/widgets`) lists images without alt text; configured in `config/statamic/cp.php` (`container`, `limit`, `exclude` folders, default `set-previews`).
- `/styleguide` (local env only) shows every component. Add a new component there too.
- **Quality**: `.prettierrc` (Antlers + Tailwind plugins; `npm run format` covers JS and CSS). The dev site runs pa11y (axe, WCAG 2 AA) on the URLs in `.pa11yci.json` in a GitHub Action; keep it green.
- Labels in the control panel and front end are in Dutch.
