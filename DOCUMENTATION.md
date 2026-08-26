# Estatein — Development Notes

A custom WordPress theme built from the [Estatein Figma template](https://www.figma.com/community/file/1314076616839640516).
No page builder, no starter theme, no framework — hand-written PHP, CSS and JavaScript.

**Live demo:** https://claude.ai/code/artifact/0328871e-a194-4c2e-b49c-88099f194099
**Theme source:** `estatein/`

---

## 1. Approach

I started by extracting a design system from the Figma file rather than building
page by page. The template is consistent — one neutral ramp, one purple ramp,
one typeface (Urbanist), a 10/12/16px radius family, and a small set of repeating
components (card, pill, stat, icon-button, accordion row). Pulling those into CSS
custom properties first meant every page afterwards was assembly rather than
re-styling, and it makes the palette retunable from one block at the top of
`assets/css/main.css`.

Pages were then composed from section partials (`template-parts/sections/`), so
the FAQ block on the home page, the services page and the contact page is one
file, not three copies.

## 2. Why a custom theme, and how it's organised

`functions.php` contains only wiring. Each concern is a separate file under
`inc/`:

| File | Responsibility |
|---|---|
| `setup.php` | Theme supports, menus, image sizes, editor palette, head cleanup |
| `enqueue.php` | Assets, `filemtime` cache-busting, deferred script, font preload |
| `post-types.php` | Properties, Services, Testimonials, Team, FAQs + taxonomies |
| `fields.php` | ACF field groups **and** a native meta-box fallback |
| `customizer.php` | Site-wide options (contact, social, hero) without ACF |
| `template-tags.php` | Shared render helpers (price, breadcrumbs, pagination, specs) |
| `icons.php` | Inline SVG icon set |
| `nav-walker.php` | Accessible menu walker + fallback navigation |
| `contact-form.php` | Form handling, validation, storage, rate limiting |
| `seo.php` | Meta, Open Graph, JSON-LD, sitemap additions |
| `content-defaults.php` | Design-accurate fallback content |

### Three decisions worth explaining

**ACF is optional, not required.** Templates never call `get_field()` directly —
they call `estatein_field()`, which reads through ACF when it's installed and
falls back to `get_post_meta()` when it isn't. When ACF is absent the theme
registers its own meta boxes supplying the same keys. The result: the theme works
on a bare WordPress install, and gains a nicer editing UI if ACF is added. Field
groups are registered in PHP (`acf_add_local_field_group`) rather than imported
from JSON, so they're version-controlled with the theme.

**A fresh install renders the design, not empty boxes.** Every section resolves
its content through a function like `estatein_get_properties()`, which returns
published posts when they exist and a curated seed set when they don't. This is
why the demo looks complete before any content has been entered. The seed data is
filterable and disappears the moment real content is published — it never
overrides anything.

**One properties index, two routes.** Registering the `property` post type with
`rewrite => 'properties'` and `has_archive => true` collides with a page of the
same slug, and WordPress resolves that in favour of the archive — so the page
template silently never runs. Rather than fight the router or rename the URL,
the filter bar and results grid live in one shared part
(`template-parts/sections/property-index.php`) that both `archive-property.php`
and the page template render. The visitor gets the same page either way, and it
works whether or not a Properties page has been created.

**The markup works without JavaScript.** The mobile menu, accordion, carousels
and forms all have a functioning no-JS baseline; `main.js` upgrades them. The
contact form posts normally and redirects when `fetch` is unavailable, and
submits over AJAX when it isn't. Accordion panels are open by default and closed
by script, so a JS failure leaves content readable rather than hidden.

## 3. Accessibility

- Semantic landmarks, skip link, one `<h1>` per page, no heading-level skips
  (verified across all seven templates).
- Every image has an `alt`; decorative SVG is `aria-hidden`; icon-only controls
  carry visually-hidden text.
- The accordion uses `aria-expanded` / `aria-controls` on real `<button>`
  elements; the mobile nav manages `aria-expanded`, closes on `Escape` and
  returns focus to the toggle.
- Form errors are announced inline, tied to fields with `aria-invalid`, and the
  status region is `role="status"`.
- Visible `:focus-visible` rings throughout; `prefers-reduced-motion` disables
  all transitions and reveal animations; `prefers-contrast: more` lifts muted
  text and borders.
- Contrast checked against WCAG AA — input placeholders were lifted from `#666`
  (3.0:1) to `#8c8c8c` (5.2:1) after measuring.

## 4. Performance

- **Two requests** for the whole front end: one stylesheet, one deferred script
  (~5KB). No jQuery, no framework, no icon font.
- **Self-hosted Urbanist** as a single variable-font file (28KB, preloaded)
  covering weights 400–700 — no third-party connection on first paint.
- Icons are inlined SVG, so they cost no requests and inherit `currentColor`.
- Registered image sizes (`estatein-card`, `estatein-hero`, …) stop WordPress
  serving a full-size original into a card slot; `loading="lazy"` and
  `decoding="async"` everywhere except the LCP hero, which gets
  `fetchpriority="high"`.
- Emoji scripts, generator tags and oEmbed discovery removed; the block library
  stylesheet is dequeued on views that don't use blocks.
- `filemtime()` versioning means a deploy busts the cache without a manual bump.

## 5. SEO

Native `title-tag` support, plus a meta description, canonical, Open Graph and
Twitter card built from the queried object. JSON-LD emits a `RealEstateAgent`
graph, `Residence` + `Offer` on a listing, and `FAQPage` where the accordion
appears. Properties and Services are added to the core sitemap. All of it is
skipped automatically if Yoast, Rank Math, AIOSEO or SEOPress is detected, so
nothing is emitted twice.

## 6. Security

- Every output escaped at the point of echo (`esc_html`, `esc_attr`, `esc_url`,
  `wp_kses_post`); every input sanitised on the way in.
- Forms carry a nonce, a honeypot field, and a per-IP rate limit (5 per 10
  minutes via transient). Enquiries are stored as a private CPT so a bounced
  email doesn't lose a lead.
- Meta-box saves check nonce, autosave and `current_user_can`.
- New-window links get `rel="noopener noreferrer"`.

## 7. Tools used

| Tool | Purpose |
|---|---|
| **No plugins required** | The theme ships everything it needs |
| ACF (optional) | Nicer editing UI; the theme degrades cleanly without it |
| PHP 8.2 CLI | Linting every file, and running the static renderer below |
| Playwright | Cross-viewport screenshots, interaction and accessibility checks |
| Unsplash | Demo photography (free licence) |
| Urbanist | Typeface, SIL Open Font License 1.1 |

### On the static renderer

To review the real templates visually without a WordPress install, I wrote a
small shim that stubs the WordPress functions the theme calls and renders each
template to HTML. Screenshots and the live demo therefore come from the **actual
theme files**, not a separate hand-built mock — which removes the usual risk of
the preview and the theme drifting apart. The shim is a development tool and is
not part of the theme.

## 8. Installation

1. Copy `estatein/` into `wp-content/themes/`.
2. Activate **Estatein** in Appearance → Themes. Post types and rewrite rules
   register on activation.
3. Settings → Permalinks → Save once (flushes rewrites for `/properties/`).
4. Create pages **About**, **Properties**, **Services**, **Contact** and assign
   the matching page template to each. Set a static front page under
   Settings → Reading.
5. Optional: assign a menu to *Primary*. Without one, the theme falls back to a
   navigation built from those pages.

Everything renders correctly before any content is added; publish a Property to
see seed data replaced by real listings.

## 9. Known scope

Built against a 4-hour target. Included: home, about, properties (with working
filters), services, contact, blog index, single post, single property, property
archive, search and 404. Not included: a map integration on the contact page and
a saved-properties feature — both were in scope creep territory rather than the
Figma reference, and I preferred to finish what's here properly.
