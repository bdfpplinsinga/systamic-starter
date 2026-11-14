---
name: nieuwe-module
description: Add a new content collection to a Systamic site, built like the news, jobs, services, team and menu modules (collection, blueprint, overview page, views, search, structured data); in the dev site also as an optional kit module. Use when the user wants a new collection or section such as projects, cases, events or locations, e.g. "maak een collectie voor ...", "nieuwe module".
---

# New collection module

Follow the conventions in `CLAUDE.md`. Statamic 6 docs: github.com/statamic/docs (`content/collections`). Check them, don't rely on memory.

Pick the closest existing module as an example and copy its structure:

| Example | Use it for |
|---|---|
| `news` | dated, newest first, taxonomy, feed |
| `jobs` | dated, with a query scope that filters entries (`app/Scopes/OpenJobs.php`) |
| `services` | structured (drag to order), detail page with page builder, `icon` per entry |
| `team` | no route; entries only appear on an overview or in a block |
| `menus` | no route; one entry holds nested grids, so editors fill everything on one screen |

Settle with the user (ask only what is unclear): handle (English, plural: `projects`), Dutch title and URL (`/projecten/{slug}`), dated or structured, own detail page yes/no, which fields, and whether a block should show the entries.

## Files

1. **Collection** `content/collections/<handle>.yaml`: `title` (Dutch), `route` (Dutch slug) or none, `mount: <page>` when there is an overview page, `date`/`sort_dir` or `structure`, `template: <handle>/show`, `layout: layout`. Without a route add `inject: { seo: false }`. A structured collection also needs `content/trees/collections/<handle>.yaml`.
2. **Blueprint** `resources/blueprints/collections/<handle>/<singular>.yaml`: Dutch labels. Reuse fieldsets (`image`, `text`, `buttons`, or `page_builder` for a free-form detail page). Use the `icon` fieldtype with set `site` for icons.
3. **Overview page** `content/collections/pages/<dutch-slug>.md` with `template: <handle>/index` and its own `id` (the slug). Hang it in `content/trees/collections/pages.yaml` and `content/trees/navigation/main.yaml` (in the dev site the post-install script does that for new projects, see below).
4. **Views** `resources/views/<handle>/`: `index.antlers.html` (overview, with pagination when the list can grow), `show.antlers.html` (detail), and partials prefixed with `_` (`_meta`, `_card`). Use components (`x-section`, `x-container`, `x-heading`, `x-picture`, `x-entry-card`, `x-breadcrumbs`, `x-share`) and design tokens only. Add a component (use the `nieuw-component` skill) when a card is used in more than one place, like `<x-team-member>`.
5. **Example entries** in `content/collections/<handle>/`, with realistic Dutch content, so the overview is never empty after installation.
6. **Structured data** when Google has a type for it (JobPosting, Event, Restaurant/Menu, ...): JSON-LD in the view, like `jobs/show` and `menus/index`. Encode values with `| to_json` and remember the `??` gotcha.
7. **Query scope** for filtering logic (expired, archived) in `app/Scopes/`, used as `query_scopes="..."`. Compare dates as Carbon; a date string fails in the Stache.
8. **Search**: `collection:*` indexes everything. A collection without a route stays out through `App\Search\HasUrl`. Run `php please search:update --all`.
9. **Block**: if entries should appear on other pages, first check whether `latest_entries` is enough (it takes any collection). Only otherwise make a block with the `nieuw-blok` skill. Blocks must keep working when the module is not installed.
10. **CP**: the collection appears in the control panel automatically. Check labels, the list columns and the sort order.

## Dev site only: make it a kit module

When `package/starter-kit.yaml` exists:

- Add a module under `modules:` with a Dutch `prompt` (what it does and where it lives), `default: false`, and `export_paths` listing every file above (collection yaml, entries folder, page `.md`, tree, blueprints, views, scopes). Shared files such as components or blocks go in the root `export_paths`.
- Add the page slug to `$modulePages` in `package/StarterKitPostInstall.php`, so the installer hangs the page in the pages tree and main nav.
- Nothing outside the module may depend on it. Conditional code checks whether files exist (see the RSS route in `routes/web.php`).
- Add the overview and one detail URL to `.pa11yci.json`.
- Add a row to the module table in `package/README.md` and a line to the modules paragraph in `CLAUDE.md`.
- Test a fresh install with the `kit-export` skill.

## Check

- Overview and detail pages at mobile and desktop widths, empty state included.
- `npx --yes pa11y@8 --runner axe --standard WCAG2AA <url>` on the overview and a detail page.
- Structured data: check it with the Rich Results Test or schema.org validator (ask the user, it needs a public URL).
- `npx prettier --write resources/views/<handle>`
