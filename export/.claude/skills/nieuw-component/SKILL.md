---
name: nieuw-component
description: Add a new Antlers component (<x-name>) to a Systamic site, with documented props, tw_merge classes, pass-through attributes and a styleguide example. Use when the user asks for a new component, a reusable UI element, or "maak een component voor ...".
---

# New component

Follow the conventions in `CLAUDE.md`. Check `resources/views/components` first: maybe an existing component can get a new prop or variant instead. Use `button`, `heading` and `section` as examples of the pattern.

## 1. File: `resources/views/components/<name>.antlers.html`

Name in kebab-case (`team-member`, `entry-card`). Called as `<x-<name> />`.

Use this structure:

```antlers
{{#
    <What it is, one or two Dutch sentences.>
    Props: <prop> (<option>|<option>), <prop>, class
    <Notes on slots and pass-through attributes, if any.>
#}}
{{ base = "..." }}

{{ variant_class = switch(
    (variant == "secondary") => "...",
    ()                       => "..."
) }}

<div class="{{ [base, variant_class, class] | tw_merge }}" {{ attributes:except(['variant', 'class']) }}>
    {{ slot }}
</div>
```

Rules:

- **Props comment** at the top lists every prop and its options.
- **Classes** are always combined as `[base, variant_class, ..., class] | tw_merge`, so a `class` from outside wins. Never put a ternary inside that array: assign it to a variable first.
- **Attributes**: `{{ attributes:except([...]) }}` lists *every* prop, including `class`, so props don't end up as HTML attributes.
- **Isolated**: a component sees only its props and slots, no globals or page data. The caller passes them in (`:links="social:social_links"`, `:image="image"`). Multi-word props arrive camelCased (`per_view` becomes `{{ perView }}`). Named slots (`<x-slot:trigger>`) arrive as `{{ trigger }}`.
- **Tokens only**: colours from `@theme` in `resources/css/site.css` (`primary`, `secondary`, `surface`, `ink`, `accent`, ...), never hard-coded Tailwind colours. Use `accent` for links and eyebrows.
- **Semantics and accessibility**: the right element (`<a>` with `href`, otherwise `<button type="button">`), visible `focus-visible` styles, `aria-*` for state (Alpine: `:aria-expanded`), `aria-hidden` on decorative icons, and alt texts from the asset (`<x-picture>` handles that).
- **Images** through `<x-picture>` with a Glide preset from `config/statamic/assets.php`. **Icons** through `<x-icon>`.
- **JS**: Alpine.js inline (`x-data`). Larger code goes in `resources/js` and loads only where it is needed (see `slider.js`).

## 2. Styleguide

Add a section to `resources/views/styleguide.antlers.html` (`/styleguide`, local only) in the same style as the others: `<x-heading size="lg">Name</x-heading>`, then every variant/size, one example with a `class` override and, if relevant, one example with a pass-through attribute. Demo data the component needs goes at the top of the styleguide as a variable (see `demo_image` and `demo_content`).

## 3. Check

- Open `/styleguide` and check every variant at mobile and desktop widths.
- Check that a `class` passed from outside overrides the base classes.
- Use a keyboard: tab order, focus visible, Escape/Enter where relevant.
- `npx --yes pa11y@10 --runner axe --standard WCAG2AA <site-url>/styleguide`
- `npx prettier --write resources/views/components/<name>.antlers.html`
- Dev site: add the component to the list in `package/README.md` ("Componenten").
