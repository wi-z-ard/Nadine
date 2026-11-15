# Configuring Nadine

Everything is configured from **Site administration → Appearance → Themes →
Nadine**. The page is split into five tabs. All settings belong to the
`theme_nadine` plugin and are removed when the theme is uninstalled.

Only users with `moodle/site:config` can change these settings. The custom SCSS
field additionally requires the `moodle/site:config` capability and is validated
as SCSS by Moodle before it is stored.

## Branding

| Setting | Default | Notes |
| --- | --- | --- |
| Logo | none | PNG, JPEG, SVG or WebP, up to 2 MB. Shown in the header, footer and login panel. Falls back to the site logo, then to the built-in mark. |
| Favicon | none | ICO, PNG or SVG, up to 512 KB. |
| Colour theme | Nadine | A grid of swatch cards: Nadine, Forest, Ocean, Plum, Clay, Slate, Rose, Amber and `Custom`. Each card previews its accent/primary pair; `Custom` shows a spectrum and reveals the two pickers below. |
| Accent colour | `#ef5e00` | Only shown when the colour theme is `Custom`. Highlights and the theme's own accent surfaces; dark mode derives its own accent from the same value. |
| Primary colour | `#01275a` | Only shown when the colour theme is `Custom`. The main brand colour: buttons, links, active navigation, pagination, progress bars and focus rings. Empty inherits the accent colour. |
| Header style | Translucent | `Translucent` keeps the page visible under the bar; `Solid` uses an opaque surface. |
| Login background image | none | PNG, JPEG or WebP, up to 5 MB. Sits behind the branding panel on the login page. |
| Login panel eyebrow | empty | Small label above the panel heading. Empty uses the translated default. |
| Login panel heading | empty | Empty uses the translated default. |
| Login panel introduction | empty | Short paragraph. Basic HTML is allowed and sanitised. |
| Login image description | empty | Alternative text for the illustration. Empty uses the translated default. |

Changing the colour theme, logo, favicon, accent or primary colour, header style
or login image resets the theme caches automatically.

## Homepage

| Setting | Default | Notes |
| --- | --- | --- |
| Show homepage hero | On | The editorial introduction on the public site home. |
| Show course search in the hero | On | The form posts to Moodle's real `/course/search.php`. |
| Hero eyebrow | empty | Small label above the title. |
| Hero title | empty | Empty uses the translated default. |
| Hero subtitle | empty | The introductory paragraph below the title. Basic HTML is allowed and sanitised. |
| Hero image | none | Replaces the illustrated panel. The description is used as alternative text. |
| Main button label | empty | Text on the primary hero button, which links to the course catalogue. Empty uses "Browse courses". |
| Side panel title | empty | Heading inside the illustrated panel, shown only when no hero image is uploaded. |
| Side panel text | empty | One or two sentences below the side panel title. |
| Catalogue eyebrow | empty | Small label above the catalogue band title. |
| Catalogue title | empty | Heading above the course listing on the course index page. Empty uses the translated default. |
| Catalogue introduction | empty | Paragraph below the catalogue title. Basic HTML is allowed and sanitised. |
| Features eyebrow | empty | Small label above the features title. |
| Features title | empty | Heading above the three feature cards. |
| Feature 1–3 title | empty | Heading on each feature card. |
| Feature 1–3 text | empty | One sentence on each feature card. |
| Call-to-action title | empty | Heading in the closing band at the foot of the site home. |
| Call-to-action text | empty | One sentence under the closing heading. |
| Catalogue button label | empty | Button that opens the course catalogue. Empty uses "Browse the catalogue". |
| Join button label | empty | Second button, shown to signed-out visitors when self-registration is enabled. |
| Show continue learning on the homepage | On | The viewer's own courses under the counters, ordered by last access. Guests never see it. |
| Homepage scroll animation | None | `None`, `Reveal` or `AOS-style`. Animates the sections below the hero as the visitor scrolls. Site home only; the hero is never animated. `prefers-reduced-motion: reduce` always wins. |

The hero is only rendered on the site home for visitors who are not signed in,
or when the front page is configured to show it. It never replaces course
content or blocks.

## Dashboard

| Setting | Default | Notes |
| --- | --- | --- |
| Show dashboard welcome | On | A welcome band with the user's picture, name and links to My courses, Calendar, Messages and the course catalogue. Blocks and course content below it are untouched. |
| Welcome eyebrow | empty | Small label above the greeting. |
| Welcome heading | empty | Use `{name}` to insert the signed-in user's full name. Empty uses "Welcome back, {name}". |
| Welcome introduction | empty | Paragraph below the greeting. Basic HTML is allowed and sanitised. |

## Appearance

| Setting | Default | Notes |
| --- | --- | --- |
| Enable light and dark appearance | On | Turns on Moodle 5.3's colour mode feature and shows the Light/Dark/System switch in the navigation for signed-in users. |
| Default appearance | System | Used for visitors who have not chosen a mode. `System` follows the operating-system preference. |

The theme mirrors these two values into `theme_boost`'s colour mode settings so
that the core switch and the theme agree. The visitor's choice is stored as a
user preference and applied in an inline script before the first paint, so there
is no flash of the wrong theme.

## Advanced

| Setting | Default | Notes |
| --- | --- | --- |
| Custom SCSS | empty | Compiled after the theme's own stylesheet, so it can override any rule. Use the tokens documented in `docs/DESIGN.md` rather than hard-coded colours. |

## What is not configurable

The following are deliberate design decisions, not missing settings:

- The type scale, spacing rhythm, radii and shadow set.
- The layout of the course page and course index (inherited from Boost).
- The login split layout (it degrades to a single column below 992 px).
- The footer link groups: they are generated from real Moodle pages, so they
  always point at pages that exist.

## Site-level settings that affect the theme

| Setting | Where | Effect |
| --- | --- | --- |
| Site full name and short name | General → Site administration | Brand name in the header, footer and page titles. |
| Front page summary | Front page settings | Used as the introductory text on the site home. |
| Enable completion | Advanced features | Course progress indicators. |
| Language menu | Language settings → Language settings | Shows the language selector in the navigation. |
| Guest login button | Plugins → Authentication → Manage authentication | Shows the guest sign-in option on the login page. |
