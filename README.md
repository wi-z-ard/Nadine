# Nadine

Nadine is a production-oriented child theme for **Moodle 5.3** (branch 503, `2026100500` or later). It builds on Boost and the Moodle Design System, so navigation, blocks, activities, forms, grading, permissions and JavaScript modules keep working exactly as Moodle intends.

The design is deliberately quiet: warm paper-like surfaces, deep evergreen accents, editorial serif headings and a clear reading hierarchy. It is a learning interface, not a dashboard template.

## Requirements

| Requirement | Value |
| --- | --- |
| Moodle | 5.3 (2026100500) or later within branch 503 |
| Parent theme | `theme_boost` (shipped with Moodle) |
| PHP | The version required by your Moodle release (8.2+ for Moodle 5.3) |
| Database | Any database supported by Moodle 5.3 |
| Browser | Current Chrome, Edge, Firefox or Safari; progressive enhancement for older browsers |

The theme has no Composer, npm or CDN dependency at runtime. SCSS is compiled by Moodle's own compiler and cached in `moodledata`.

## Installation

1. Copy the `nadine` directory to `moodle/public/theme/nadine` (or install the ZIP through **Site administration → Plugins → Install plugins**).
2. Visit **Site administration → Notifications** and complete the theme installation.
3. Go to **Site administration → Appearance → Themes → Theme selector** and choose **Nadine**.
4. Purge caches if you changed files directly: `php admin/cli/purge_caches.php`.

The theme does not modify Moodle core. Uninstalling it restores Boost; no core files or database tables are owned by the theme.

## Configuration

All options are on **Site administration → Appearance → Themes → Nadine**.

### Branding

- **Logo** – header, footer and login panel. Falls back to the site logo, then to the built-in Nadine mark and site name.
- **Favicon** – browser tab icon.
- **Colour theme** – eight curated palettes: Nadine (default), Forest, Ocean, Plum, Clay, Slate, Rose and Amber, plus **Custom**.
- **Accent colour** – highlights and the theme's own accent surfaces. Shown when the colour theme is **Custom**; the default is `#ef5e00`.
- **Primary colour** – buttons, links, active navigation and focus rings. Shown when the colour theme is **Custom**; empty inherits the accent colour.
- **Header style** – translucent (default) or solid.
- **Login background image**, **login panel eyebrow**, **login panel heading**,
  **login panel introduction** and **login image description**.

### Footer

- **Footer tagline** and **footer description** – the two lines under the site identity.
- **Social links** – one address per network: Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp and GitHub. Only the networks with an address appear, in that order, as round hairline buttons under the description.

### Homepage

- **Show homepage hero** – the editorial introduction on the public site home.
- **Show course search in the hero** – a live search field. Typing shows matching courses in a panel with their category and summary; the form still posts to Moodle's real `/course/search.php` when JavaScript is off.
- **Hero eyebrow, title and subtitle** – leave empty to use the translated defaults.
- **Hero image** – replaces the illustrated panel; a description is used as the alternative text.
- **Main button label** – the primary hero button, which links to the catalogue.
- **Hero side panel title and text** – the copy inside the illustrated panel, shown only when no hero image is uploaded.
- **Catalogue eyebrow, title and introduction** – the band above Moodle's own course listing.
- **Features eyebrow, title and cards** – the three-column section below the hero: a heading and a title plus one sentence per card.
- **Closing call to action** – the band at the foot of the site home: title, text and both button labels.
- **Show continue strip** – the signed-in learner's own courses under the counters, ordered by last access, with progress and a resume action. It is not a second catalogue: core already lists every available course further down the page. Guests never see it.
- **Homepage scroll animation** – *None* (default), *Reveal* or *AOS-style*. Brings the sections below the hero in as the visitor scrolls. It applies to the site home only, the hero is never animated, and visitors who ask their system to reduce motion get the static page.

### Dashboard

- **Show dashboard welcome** – a welcome band with the real user picture, name and links to My courses, Calendar, Messages and the course catalogue. The right-hand column answers "what do I do now?" with the one course to pick up next, its completion in a ring and a resume action; a learner with no enrolments gets the catalogue prompt instead. Course content and blocks below it are untouched.
- **Welcome eyebrow, heading and introduction** – the copy in the band. Use `{name}` in the heading to insert the user's full name.

### Appearance

- **Enable light and dark appearance** – turns on Moodle 5.3's colour mode feature. The switcher appears in the navigation for signed-in users and offers Light, Dark and System. The choice is stored as a user preference and mirrored to a cookie so the correct mode is present on the first paint.
- **Default appearance** – Light, Dark or System for visitors who have not chosen.

### Advanced

- **Custom SCSS** – compiled with the theme. Restricted to site administrators by Moodle's settings permission.

## Documentation

| Document | Contents |
| --- | --- |
| [docs/INSTALLATION.md](docs/INSTALLATION.md) | Requirements, install, upgrade, uninstall, troubleshooting |
| [docs/CONFIGURATION.md](docs/CONFIGURATION.md) | Every setting, its default and what it changes |
| [docs/DESIGN.md](docs/DESIGN.md) | Design principles, tokens, components, RTL and dark mode |
| [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) | Architecture, SCSS pipeline, extending the theme |
| [TESTING.md](TESTING.md) | What was tested, measured results and known gaps |
| [CHANGELOG.md](CHANGELOG.md) | Release history |

## Design system

Tokens are CSS custom properties in `scss/nadine.scss`:

- Canvas, surface, ink, muted ink and border colours for light and dark modes.
- Accent and primary colours, each with a derived dark-mode variant.
- Radii, shadows, content width, motion durations and type stacks.
- All spacing and alignment use logical properties (`margin-inline`, `padding-inline`, `inset-inline`, `border-inline`) so Arabic and other right-to-left languages are supported by the same stylesheet.

Typography uses locally available families (Iowan Old Style/Palatino/Georgia for display, the platform UI stack for body, Noto Naskh Arabic and Noto Kufi Arabic as Arabic fallbacks). No web font is fetched from a third-party service.

The administration area follows the same tokens. The tab bar and every settings category on `/admin/search.php` take a Lucide glyph, drawn as a mask filled with `currentColor` so it follows hover, selected and dark-mode colours. The glyph is keyed on the href core already renders, so the React tab bar, the server-rendered fallback and every plugin's category are covered without a template override.

## Internationalisation

- All user-facing strings are in `lang/en` and `lang/ar`.
- The theme uses Moodle's language API, language menu and document direction.
- Directional icons (arrows) are mirrored in RTL; non-directional icons are not.
- Additional language packs can be installed without changing theme files.

## What is overridden

| File | Purpose |
| --- | --- |
| `config.php` | Theme name, parent, layouts, SCSS callbacks, course index |
| `lib.php` | SCSS variables, theme file serving, context for hero/dashboard/catalogue/footer/login |
| `classes/output/core_renderer.php` | Logo, favicon, brand, homepage hero, dashboard welcome, catalogue intro, course hero, footer, login panel, guest auth actions |
| `layout/drawers.php` | Boost drawer layout plus skip link, main landmark and theme sections |
| `layout/login.php` | Split login layout with brand bar, language selector and footer |
| `templates/navbar.mustache` | Brand and guest sign-in actions; all Boost controls preserved |
| `templates/footer.mustache` | Theme footer, including the social row |
| `templates/hero.mustache` | Public homepage hero |
| `templates/dashboard_welcome.mustache` | Dashboard welcome band and continue panel |
| `templates/frontpage_continue.mustache` | Signed-in learner's courses on the site home |
| `templates/course_card.mustache` | Course tile shared by the theme's course surfaces |
| `templates/catalogue_intro.mustache` | Course discovery band |
| `templates/course_hero.mustache` | Course page hero: category, title, summary, metadata, progress panel |
| `templates/search_form.mustache` | Live search field shared by the hero and the catalogue |
| `templates/login_panel.mustache` | Login branding panel and illustration |
| `templates/profile_meta.mustache` | Facts strip under the name on a public profile |
| `templates/core/login_panel.mustache` | Replaces the core panel that contains placeholder statistics |
| `ajax/drawer.php` | Stores the course index open/closed preference |
| `ajax/search.php` | POST-only JSON endpoint behind the live search |
| `js/drawer.js` | Persists the course index state, plain ES5 |
| `js/search.js` | Live search behaviour, plain ES5 |

Everything else is inherited from Boost.

## Development

The theme is plain PHP, Mustache and SCSS.

- Edit SCSS in `scss/nadine.scss`; Moodle compiles it on the next request after a cache purge.
- `theme_nadine_get_pre_scss()` contains the SCSS variables that must exist before Bootstrap is compiled.
- The password visibility control is core's `toggle-sensitive` button, restyled in SCSS; the theme ships no AMD modules.
- `pix/login-art.png` is the default illustration for the login panel. An image uploaded in *Login image* replaces it.
- `pix/lucide/` holds the interface glyphs, taken from Lucide under the ISC License (see `pix/lucide/LICENSE`); `pix/social/` holds the networks' marks used by the footer (see `pix/social/LICENSE`). Neither set is original artwork.
- Run `php admin/cli/purge_caches.php` after changing templates, language files or SCSS.
- Follow Moodle coding style; `php -l` every PHP file before packaging. `phpcs.xml` ships with the theme, so `phpcs` run from this directory uses the Moodle standard without arguments. The last run reports 0 errors and 6 expected warnings; see `TESTING.md`.

## Compatibility

Tested with Moodle **5.3+ (Build: 20261021), version 2026100500.06** on PHP 8.4 and MariaDB/MySQL. See `TESTING.md` in the project workspace for the full test log, performance measurements and known limitations.

## License

GNU GPL v3 or later. See `LICENSE`.
