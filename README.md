# Estatein — Custom WordPress Theme

A hand-built WordPress theme converted from the
[Estatein Figma template](https://www.figma.com/community/file/1314076616839640516).
Dark UI, Urbanist type, no page builder and no front-end dependencies.

**Live demo** → https://estatein-gr.infinityfreeapp.com
**Development notes** → [DOCUMENTATION.md](DOCUMENTATION.md)

---

## Install

```bash
cp -r estatein /path/to/wp-content/themes/
```

Then: activate the theme → Settings → Permalinks → **Save** → create the four
pages below and assign their templates.

| Page | Template to assign |
|---|---|
| About | About Us |
| Properties | Properties |
| Services | Services |
| Contact | Contact Us |

Set a static front page under **Settings → Reading**. The theme renders the full
design before any content exists, so you can check the install immediately.

## What's included

**Pages** — Home, About, Properties (filterable), Services, Contact, Blog index,
single post, single property, property archive, taxonomy, search, 404.

**Post types** — Properties (with Type / Location / Status taxonomies), Services,
Testimonials, Team Members, FAQs, and a read-only Enquiries inbox.

**Front end** — one 30KB stylesheet and one 5KB deferred script. No jQuery, no
framework, no icon font. Urbanist is self-hosted as a single variable font.

## Structure

```
estatein/
├── style.css               Theme header + metadata only
├── functions.php           Bootstrap; requires inc/*
├── header.php  footer.php  index.php  page.php  single.php
├── single-property.php  archive-property.php  search.php  404.php
├── front-page.php  comments.php  searchform.php
├── inc/
│   ├── setup.php           Supports, menus, image sizes, head cleanup
│   ├── enqueue.php         Assets, cache-busting, font preload
│   ├── post-types.php      CPTs + taxonomies
│   ├── fields.php          ACF groups + native meta-box fallback
│   ├── template-tags.php   Shared render helpers
│   ├── icons.php           Inline SVG icons
│   ├── nav-walker.php      Accessible menu walker + fallback nav
│   ├── contact-form.php    Validation, storage, rate limiting
│   ├── seo.php             Meta, Open Graph, JSON-LD
│   └── content-defaults.php  Seed content for a fresh install
├── template-parts/
│   ├── sections/           hero, featured-properties, services, testimonials,
│   │                       faq, clients, cta, page-hero
│   └── cards/              property, testimonial, team
├── templates/              Page templates: about, properties, services, contact
└── assets/  css/  js/  fonts/  img/
```

## Editing content

Templates read fields through `estatein_field()`, which uses **ACF** when it's
installed and falls back to post meta when it isn't — so ACF is optional. With
ACF active you also get an *Estatein Settings* options page for contact details
and social links.

Customising the palette means editing the token block at the top of
`assets/css/main.css`; everything else derives from it.

## Requirements

WordPress 6.0+, PHP 7.4+. Tested against WordPress 6.7.

## Credits

Design: Estatein by Produce UI (CC BY 4.0) · Type: Urbanist (SIL OFL 1.1) ·
Photography: Unsplash
