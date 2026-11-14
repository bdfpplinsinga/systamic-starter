---
name: nieuw-blok
description: Add a new page builder block to a Systamic site (fieldset, replicator set, preview image, Antlers view, demo example). Use when the user asks for a new block, section or "blok" in the page builder, e.g. "maak een blok voor ...", "nieuw blok", "add a block".
---

# New page builder block

Follow the conventions in `CLAUDE.md`. Look at an existing block of similar shape first (`steps` for a grid of items, `media_text` for content + image, `faq` for an accordion) and copy its structure.

Settle these before writing anything; ask only when the request leaves them open:

- **Handle**: snake_case, English (`steps`, `media_text`). It is used for the set, the view (`blocks/<handle>.antlers.html`), the preview (`<handle>.jpg`) and usually the fieldset (`block_<handle>.yaml`).
- **Fields**: what the editor fills in, and which existing fields or groups cover that.
- **Set group** in `page_builder.yaml` (content, media, ...): pick the group that fits best.

## 1. Fieldset: `resources/fieldsets/block_<handle>.yaml`

- `title: 'Blok - <Dutch name>'`.
- Reuse the layers: `import: content` for eyebrow/heading/text/buttons, and the fields `heading`, `text`, `image`, `images`, `button`, `buttons`, `video`. Only define a field inline when it belongs to this block alone.
- Never import `block_settings` here. The set does that.
- Dutch `display`, `instructions` and `add_row` labels. Set `localizable: true` on content fields, and `validate: [required]` where the block breaks without the value.
- Repeating items: `type: grid` with `mode: stacked` (see `block_steps.yaml`).

## 2. Set: `resources/fieldsets/page_builder.yaml`

Add the set to the right group, in the same shape as the other sets:

```yaml
<handle>:
  display: '<Dutch name>'
  instructions: '<one short Dutch sentence>'
  image: <handle>.jpg
  fields:
    -
      import: block_<handle>
    -
      import: block_settings
```

## 3. View: `resources/views/blocks/<handle>.antlers.html`

- Wrap the block in `<x-section :background="block:background" :id="block:anchor">` and `<x-container>`. Only change `spacing` when the design calls for it (the hero uses `lg`). The editor never picks spacing.
- Read everything from `block:*`.
- Render the content group with `<x-content :block="block" />`. Don't repeat that markup.
- Headings: `<x-heading :as="block:heading_level" size="...">`. The view picks the size. Item headings below the block heading are one level lower (usually `as="h3"`).
- Use components (`x-button`, `x-picture`, `x-icon`, `x-heading`, `x-prose`, ...) and design tokens only. No hard-coded Tailwind colours. Use `accent` for eyebrows and links, so a `background="primary"` block stays readable.
- Mind the Antlers gotchas in `CLAUDE.md` (ternary inside a `tw_merge` array, `??` with modifiers).
- Accessibility: semantic lists (`<ol>`/`<ul>`) for repeating items, `aria-hidden="true"` on decorative elements, `sr-only` text where an item depends on its visual position, and visible focus states on everything interactive.
- Interactivity uses Alpine.js. A slider uses `<x-slider>`/`<x-slide>`. External embeds go behind `{{ cookie:allowed group="external_media" }}` inside `{{ nocache }}`.
- Add a short Dutch Antlers comment (`{{# ... #}}`) where the markup is not obvious.

## 4. Demo and preview

- If `content/collections/pages/blokken.md` exists (demo module or dev site), add an example entry to its `page_builder` in the same inline YAML style: a unique `id` such as `b-<handle>`, `type: <handle>`, `enabled: true`, realistic Dutch content, demo images `demo-1.jpg` to `demo-4.jpg`. Alternate `background: muted` with the neighbouring blocks.
- Preview image: `public/assets/set-previews/<handle>.jpg`, **640 × 216 px** JPEG, a screenshot of the block on `/blokken` (or the page where it is used). If you can't take the screenshot yourself, tell the user it still needs to be made. Statamic writes the `.meta` file itself.

## 5. Check

- Open the page with the block, at mobile and desktop widths, in all three backgrounds (`none`, `muted`, `primary`).
- Edit the block in the control panel: labels are in Dutch, the preview image appears under "Blok toevoegen", block settings stay hidden until the toggle is switched on.
- Run pa11y on the page (start the site with `php artisan serve` or use the Herd URL):
  `npx --yes pa11y@8 --runner axe --standard WCAG2AA <url>`. In the dev site, `npx --yes pa11y-ci@5 --config .pa11yci.json` against `php artisan serve --port=8000` runs the full set.
- Format the view with Prettier: `npx prettier --write resources/views/blocks/<handle>.antlers.html`.
- If the block adds front-end text that should be searchable, check that `App\Search\PageBuilderText` picks it up, then run `php please search:update --all`.
- Dev site: add the block to the list in `package/README.md` (it names every block).
