# Developing Nadine

## Architecture

Nadine is a child theme of `theme_boost`. Moodle resolves the parent first,
then asks the child for overrides, so anything the theme does not touch behaves
exactly like Boost.

```
theme/nadine/
├── ajax/drawer.php           # stores the course index preference
├── ajax/search.php           # POST-only JSON endpoint for the live search
├── amd/                      # password visibility toggle
├── classes/output/           # core_renderer subclass
├── db/install.php            # enables Boost colour modes on first install
├── docs/                     # this documentation
├── js/drawer.js              # course index state, plain ES5, no AMD
├── js/search.js              # live search panel, plain ES5, no AMD
├── js/scroll-reveal.js       # homepage reveal, plain ES5, no AMD
├── lang/en, lang/ar          # 163 strings each
├── layout/                   # drawers.php, login.php (PHP context builders)
├── pix/                      # built-in brand mark
├── scss/nadine.scss      # design system appended after the preset
├── templates/                # Mustache templates
├── config.php                # layouts, SCSS callbacks, feature flags
├── lib.php                   # SCSS variables, file serving, page context
├── settings.php              # admin settings
└── version.php
```

### Request flow

1. `config.php` registers the layouts and the three SCSS callbacks.
2. Moodle builds the page. For most pages the `drawers` layout is used.
3. `layout/drawers.php` runs first. It prepares the same context Boost prepares
   (blocks, course index, secondary navigation, add-block button, header
   content) and then calls `render_from_template('theme_nadine/drawers', …)`.
4. `templates/drawers.mustache` renders the page shell, the skip link, the
   navbar partial, the optional hero/welcome/catalogue bands and the footer.
5. `classes/output/core_renderer.php` supplies the markup for those bands
   through small public methods (`nadine_hero()`, `nadine_brand()`, …)
   that the templates call with `{{#output.nadine_hero}}`.

The renderer memoises each fragment in `$nlcache`, so calling a method twice in
one request costs nothing.

### Why the layouts are PHP

Moodle 5.x requires PHP layouts to produce the main content placeholder through
`render_from_template()`. Putting Mustache markup directly in `layout/*.php`
fails with *"page layout file … does not contain the main content placeholder"*.
The PHP files therefore only build context; all markup lives in `templates/`.

## SCSS pipeline

Three callbacks in `lib.php`:

| Callback | Purpose |
| --- | --- |
| `theme_nadine_get_main_scss_content()` | Returns the Boost preset, so the Moodle Design System is the base. |
| `theme_nadine_get_pre_scss()` | Variables that must exist **before** Bootstrap compiles: palette, radii, typography, navbar height, dark mode flag. |
| `theme_nadine_get_extra_scss()` | Appends `scss/nadine.scss`, the header style variables and the administrator's custom SCSS. |

Because the preset is compiled from source, a full rebuild is expensive. On the
reference machine (PHP 8.4, no opcache tuning, built-in server) a cold compile
of the theme took roughly eight minutes; warm requests are served from
`moodledata/cache/theme` and cost milliseconds. Plan for this in CI:

```bash
php admin/cli/build_theme_css.php --themes=nadine --direction=ltr
php admin/cli/build_theme_css.php --themes=nadine --direction=rtl
```

### Beating core's utility classes

Several core templates put Bootstrap utilities on the very element the theme
wants to style — the calendar toolbar is `header d-flex flex-wrap p-1`.
Utilities are `!important`, so a theme rule loses to them whatever its
specificity, and the declaration reads as if it were never written. The failure
is quiet: the page renders, just not the way the stylesheet says.

Re-declare the property with `!important` inside the same rule and leave a
comment saying why, or the next person will "tidy" the `!important` away and
bring the bug back. Properties the utilities do not set — `gap`,
`align-items` — need no help.

### One component, two mounts

`/message/index.php` and the message drawer render the same template, so the
messaging component is written once and pointed at both:

```scss
$nl-msg: ':is(#page-message-index .message-app, .message-app[data-region="message-drawer"])';
```

The `:is()` is load-bearing. A plain comma list (`'#page-message-index
.message-app, .message-app[data-region="message-drawer"]'`) interpolates the
suffix onto its **last** item only:

```scss
#{$nl-msg} .simplesearchform { ... }
// becomes
// #page-message-index .message-app,
// .message-app[data-region="message-drawer"] .simplesearchform { ... }
```

The page rule then styles the app element itself and every page rule silently
stops matching — the drawer looks right, the page looks untouched. `:is()`
keeps the list in one compound selector, so the suffix lands on both. It also
carries the highest specificity of its arguments, which is what the page needs
to outrank core.

The second half of the same trap is `.visually-hidden`: since Bootstrap 5.3 it
no longer sets `position: absolute`, so a hidden `<label>` still occupies a
grid or flex cell. Any layout that puts a core bar into a grid has to position
those labels itself.

### Core's block-region sheet

Core also ships a stylesheet aimed at blocks specifically, and it is written at
seven to ten classes:

```css
[data-blockregion]:not([data-blockregion="content"]) .block.block_calendar_month
  .maincalendar .calendartable.calendarmonth td.hasevent .day-number:before { … }
```

That is where the mini calendar's `width: 30%` arrows, `width: 40%` heading and
`height: auto` cells come from. A rule written as `.block_calendar_month …`
loses to it at any position in the file, and again the failure is silent: the
property simply keeps core's value.

The calendar block section keeps the prefix in `$nl-cal-block` and interpolates
it (`#{$nl-cal-block} …`) for the handful of properties core contests. Check the
CSSOM before adding a plain rule to that section — `getComputedStyle` on the
element, plus a walk of `document.styleSheets` filtered by `el.matches(selector)`,
shows which rule actually won.

## Adding a setting

1. Add the field to the right tab in `settings.php`.
2. Add the string to **both** `lang/en/theme_nadine.php` and
   `lang/ar/theme_nadine.php`.
3. Read it with `get_config('theme_nadine', 'name')` in `lib.php` or the
   renderer.
4. If the value affects CSS, call `$setting->set_updatedcallback('theme_reset_all_caches')`.
5. Escape output. Rich text from settings is stored as `PARAM_CLEANHTML` and is
   the only place where `{{{ }}}` is used; everything else goes through
   `html_writer` or `{{ }}`.

## Adding a template override

1. Copy the core template you want to change into `templates/` keeping the same
   relative path (for example `templates/core/login_panel.mustache`).
2. Edit it.
3. Purge caches.

Overriding a core template means taking responsibility for its data contract.
Prefer the renderer methods and the settings-driven bands where possible.

## Adding an icon

Icons the theme draws itself live in `pix/lucide/` as plain SVGs from the
Lucide set (ISC, see `pix/lucide/LICENSE`). Drop the file in, then call the
helper from PHP:

```php
theme_nadine_icon('chevron-right', 'nl-directional', 14)
```

The second argument is appended to `nl-icon`, the third is the pixel size
written to `width`/`height`. `.nl-icon` overrides both with `width: 1em`, so the
container's `font-size` is what actually sizes the glyph. The markup is
`aria-hidden`; if the icon is the only label, put the text in a
`visually-hidden` span as the drawer controls do.

The fourth argument picks the set: `lucide` (the default) or `social`. The
social set lives in `pix/social/` and holds the filled brand marks the footer
uses; they are the networks' own logos, so they are filled rather than stroked
and are not part of the Lucide licence.

```php
theme_nadine_icon('linkedin', 'nl-footer__social-icon', 18, 'social')
```

Mustache escapes values, so a template renders it with `{{{ icon }}}`, not
`{{ icon }}`. Pass the pre-rendered string in the context - templates cannot
call PHP.

Do not add Font Awesome classes to theme markup. Core and plugin icons still
resolve through Font Awesome, but anything the theme owns should use this
helper so the stroke weight stays consistent.

### Icons on markup the theme does not render

Some surfaces cannot take an inlined SVG: the secondary navigation is a React
island and the admin settings tree is built from navigation nodes. Those get a
glyph in CSS instead, from the same files:

```scss
.pagelayout-admin .secondary-navigation .mds-nav-pill[href$="#linkusers"]::before {
    mask-image: url([[pix:theme|lucide/users]]);
}
```

The `::before` box is filled with `currentColor` and masked by the SVG, so the
icon inherits hover, selected and dark-mode colours. `[[pix:theme|…]]` is
resolved by `theme_config::post_process()`, which rewrites it to a
`theme/image.php` URL carrying the site's own theme revision — never hardcode a
path. Key the selector on something core already renders (a `href` fragment, a
category slug) rather than adding a template override.

## JavaScript

The theme ships no AMD modules. The login page uses core's own
`core/togglesensitive` control for the password field and only restyles it, so
there is nothing to rebuild and no second control to keep in step. The
visibility glyph is drawn by CSS from `pix/lucide/eye.svg` and
`pix/lucide/eye-off.svg`, keyed on the field's `type`, which is why the icon
follows the field even if core changes its own pix image.

`js/search.js` is deliberately not an AMD module. It runs on the public front
page, where the theme cannot rely on the module loader, so it is a plain IIFE
loaded with `$PAGE->requires->js()` from `layout/drawers.php` on the home and
course discovery pages only. It upgrades every `form[data-nl-search]` it finds
and does nothing if the field is absent.

`js/scroll-reveal.js` is also a plain IIFE, loaded from `layout/drawers.php` on
the site home only, and only while the *Homepage scroll animation* setting is
on. It reads no setting itself: the mode is a body class (`nl-scroll--reveal`
or `nl-scroll--aos`) and every visual decision lives in the stylesheet, so the
file only decides *when* an element counts as on screen. Two details are worth
keeping if you change it. The hidden state is gated on `nl-reveal-ready`, a
class the script adds to the root element as soon as it runs — which is at the
top of the body, before any content is parsed — so a script that never loads
leaves the page visible instead of blank. And visibility is measured on scroll
rather than with `IntersectionObserver`, because an instant jump (an anchor,
End, a restored scroll position) can carry an element from below the fold to
above it between two frames without it ever intersecting, which would leave it
hidden for good.

Templates opt in with `data-nl-reveal`, optionally carrying `up`, `left`,
`right` or `zoom`; the direction is only honoured in the AOS mode. The value is
removed once the element has finished animating so that the element's own
hover transitions take over again.

### The search endpoint

`js/drawer.js` watches the `show-drawer-left` class on `#page` and posts the
state to `ajax/drawer.php`. It exists because core writes `drawer-open-index`
through a REST route that returns 500 on this build; the theme writes the same
preference name, so the PHP context and core both read it. It is loaded only
when `core_course_drawer()` returns an index.

### The drawer endpoint

`ajax/drawer.php` accepts one field:

| Rule | Implementation |
| --- | --- |
| Method | POST only, everything else returns 405 |
| CSRF | `require_sesskey()`, key read from `M.cfg.sesskey` |
| Guests | the sesskey check rejects an anonymous request; a signed-in guest account gets 403 |
| Field | `open`, `PARAM_BOOL` |
| Effect | `set_user_preference('drawer-open-index', 0 or 1)` |

### The search endpoint

`ajax/search.php` is the other theme-owned endpoint. Its contract:

| Rule | Implementation |
| --- | --- |
| Method | POST only, everything else returns 405 |
| CSRF | `require_sesskey()`, key shipped in `data-nl-sesskey` |
| Guests | allowed unless `$CFG->forcelogin` is set, then 403 |
| Throttle | 40 requests per session per rolling minute, then 429 |
| Query | 64 characters, tags stripped, whitespace collapsed, 2 characters minimum |
| Visibility | `get_courses_search()`, the same helper `/course/search.php` uses |
| Output | JSON of plain text; the client writes it with `textContent` |

Adding a field to the response means adding it to `render()` in `js/search.js`
as a `textContent` assignment, never as HTML.

## Testing

The development instance used four scripts outside the plugin (they are not part
of the ZIP):

| Script | Purpose |
| --- | --- |
| `makedata.php` | Creates the demo course, sections, activities and accounts. |
| `makequiz.php` | Adds the true/false questions and links them to quiz slots. |
| `testpages.sh` | Logs in as a role and fetches key pages, reporting status and size. |
| `functional_test.py` | End-to-end quiz attempt, assignment submission, forum post, teacher and admin flows. |
| `audit.py` | Structural and accessibility checks over saved HTML. |
| `bench.sh` | Median response time over several runs. |

Run them against a disposable site only: they write real content.

## Coding standards

- Moodle coding style, GPL header on every PHP file, `php -l` clean.
- No `echo` of unescaped data; use `html_writer` or Mustache escaping.
- No hard-coded credentials, URLs or site names.
- No CDN or third-party runtime dependency.
- Logical CSS properties for anything directional.
- Every user-facing string through `get_string()`.
