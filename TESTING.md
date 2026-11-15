# Testing Nadine

This document records what was actually tested on the reference installation and
what was not. Every number below was measured on the reference machine; nothing
is estimated.

## Reference environment

| Item | Value |
| --- | --- |
| Moodle | 5.3+ (Build 20261021), version `2026100500.06`, branch `503` |
| PHP | 8.4.14 |
| Database | MySQL-compatible (Aurora MySQL), database `moodle_edupro` |
| Web server | Apache 2.4 + mod_php 8.4, `http://localhost/edupro/moodle` |
| Theme | `theme_nadine` 1.3.0 (`2026103010`), parent `theme_boost` |
| Languages | English (default) and Arabic (`ar` language pack installed) |
| Operating system | Ubuntu 24.04 |

The site is served by the stock Ubuntu Apache (`/var/www/html` docroot) with
`mod_php`. Because Moodle 5 keeps its web root in `public/`, a `.htaccess` in
`moodle/` rewrites every request into `public/`, so the canonical URL is
`http://localhost/edupro/moodle` rather than `.../moodle/public`.

## Test accounts

| Role | Username | Password | Notes |
| --- | --- | --- | --- |
| Administrator | `admin` | `Adm!0ef99786e3e84b47#2026` | Site administrator |
| Teacher | `teacher1` | `Teacher!2026` | Editing teacher in the demo course |
| Student | `student1` | `Student!2026` | Enrolled student |
| Student | `student2` | `Student!2026` | Enrolled student, no submissions |
| Guest | `guest` | — | Via the guest login button |

Demo course: **Designing Learning Experiences** (`NORTHLIGHT101`, course id 2)
with four sections, a page, a forum, two file resources, an assignment, a URL, a
choice and a three-question true/false quiz.

The 1.1.0 preview adds five more demo courses so the front page has a real
catalogue to show: *Assessment for Learning*, *Inclusive Classroom Practice*,
*Digital Skills for Educators*, *Data Literacy for Teachers* and *Leading
Curriculum Change*, in the categories Teaching Practice, Digital Skills and
Leadership. Each has a generated 1200×675 cover in the course overview files.
They are ordinary courses and can be deleted from Site administration.

## Page matrix

Every page was requested over HTTP with a real session for each role. Status
codes and sizes are from the final run.

| Page | Student | Teacher | Admin |
| --- | --- | --- | --- |
| Site home `/` | 200 · 133,930 B | 200 · 133,936 B | 200 · 148,373 B |
| Dashboard `/my/` | 200 · 193,614 B | 200 · 193,636 B | 200 · 195,588 B |
| Course catalogue `/course/index.php` | 200 · 136,269 B | 200 · 136,273 B | 200 · 149,156 B |
| Course page `/course/view.php?id=2` | 200 · 207,077 B | 200 · 221,897 B | 200 · 225,118 B |
| Quiz `/mod/quiz/view.php?id=8` | 200 · 143,386 B | 200 · 153,652 B | 200 · 154,651 B |
| Assignment `/mod/assign/view.php?id=1` | 200 · 142,708 B | 200 · 151,935 B | 200 · 152,934 B |
| Forum `/mod/forum/view.php?id=5` | 200 · 158,740 B | 200 · 174,547 B | 200 · 175,545 B |
| Grades `/grade/report/overview/index.php` | 200 · 135,679 B | 200 · 130,203 B | 200 · 133,519 B |
| Profile `/user/profile.php` | 200 · 135,268 B | 200 · 135,295 B | 200 · 137,407 B |
| Calendar `/calendar/view.php?view=month` | 200 · 177,847 B | 200 · 177,851 B | 200 · 181,164 B |
| Theme settings | 404 (denied) | 404 (denied) | 200 · 306,435 B |

The 404 for students and teachers is Moodle's `error/admin` page: the theme
settings require `moodle/site:config`, which neither role has. This confirms the
theme does not bypass core capability checks.

Guest access was also checked. Guests can read the site home, the login page,
the forgot-password page and the course catalogue. The demo course is not open to
guests, so `/course/view.php?id=2` redirects to the enrolment page — that is
core Moodle behaviour, not a theme decision.

## Functional flows

Run with `functional_test.py` against the live site; all checks pass.

| Flow | Result |
| --- | --- |
| Start a quiz attempt | Pass — attempt created, three questions rendered |
| Answer and save | Pass — answers persisted |
| Submit and finish | Pass — redirected to the review page |
| Score | Pass — 3/3 correct recorded, grade 10.0 (highest grade) |
| Assignment submission form | Pass — online text editor rendered |
| Submit assignment | Pass — "Submitted for grading" |
| Post a forum discussion | Pass — discussion created and displayed |
| Teacher: course edit mode | Pass — editing controls rendered |
| Teacher: add activity form | Pass — page module form loads |
| Teacher: grader report | Pass — gradebook renders |
| Admin: theme settings | Pass — all five tabs and every field render |

No PHP fatal errors, warnings or notices appeared in any of these responses.

## Installation and upgrade

The theme was installed twice on the reference site.

| Step | Result |
| --- | --- |
| Uninstall `theme_nadine` with `admin/cli/uninstall_plugins.php` | Pass — plugin removed, site fell back to Boost |
| Install from the distributed ZIP (extracted to `public/theme`) | Pass |
| `admin/cli/upgrade.php --non-interactive` | Pass — theme registered, all settings created |
| Default values after install | Pass — accent `#0d5c4d`, appearance `auto`, hero on, dashboard welcome on, header `translucent` |
| `db/install.php` effect | Pass — `theme_boost/enablecolourmodes` is `1` |
| First page render after install | Pass — 200, hero, brand and footer present |
| Version bump upgrade (`2026102701` → `2026102702`) | Pass — `Success (1.44 seconds)` |
| Version bump upgrade (`2026102702` → `2026102800`) | Pass — `Success (1.37 seconds)` |
| Version bump upgrade (`2026103000` → `2026103010`) | Pass — `New setting: theme_nadine/primarycolor` |

The upgrade needs `max_input_vars` of at least 5000, which Moodle's environment
check enforces. On this machine the CLI runs with the default value of 1000, so
the upgrade was run as `php -d max_input_vars=5000 admin/cli/upgrade.php
--non-interactive`. A production host must set the value in `php.ini`.

The ZIP contains a single top-level `nadine/` directory with 48 entries,
including `version.php`, `config.php`, `lib.php`, `settings.php`, both language
packs, layouts, templates, SCSS, the AMD module, the bundled favicon and all
documentation. Extracting it and diffing against the installed directory
produces no differences.

## Layout geometry

Measured in a headless Firefox (Playwright) at the end of 1.0.1 by reading the
rendered box of each region. "x/w" is the content box (element box minus its
horizontal padding).

| Page | Viewport | Navbar | Page content | Hero | Footer |
| --- | --- | --- | --- | --- | --- |
| Site home | 1440 px | 148 / 1144 | 148 / 1144 | 148 / 1144 | 148 / 1144 |
| Site home | 390 px | 16 / 358 | 16 / 358 | 16 / 358 | 16 / 358 |
| Course catalogue | 1440 px | 148 / 1144 | 148 / 1144 | — | 148 / 1144 |
| Course catalogue | 390 px | 16 / 358 | 16 / 358 | — | 16 / 358 |
| Dashboard | 1440 px | 148 / 1144 | 148 / 1144 | — | 148 / 1144 |
| Dashboard | 390 px | 16 / 358 | 16 / 358 | — | 16 / 358 |
| Course page | 1440 px | 148 / 1144 | 496 / 734 | — | 148 / 1144 |
| Course page | 390 px | 16 / 358 | 16 / 358 | — | 16 / 358 |

Every region on every page starts and ends on the same edge, so the navbar,
the content, the hero and the footer read as one grid. The course page is the
one exception, and deliberately so: Moodle docks the course index drawer in
the left margin and shifts the page to the right when it is open, which is core
behaviour. The theme keeps the reading measure Moodle chooses for course
pages and keeps the footer aligned with the navbar.

### The two-sided frame (1.4.0)

From 1200px up the navbar and the page are inset between two 18rem columns.
Measured in headless Firefox at 1440×900 on `/my/` with blocks present:

| Region | x / width | Notes |
| --- | --- | --- |
| Navigation rail | 0 / 288 | full height, header 72px |
| Navbar | 288 / 864 | ends where the blocks column starts |
| Page content | 288 / 864 | `#page` box |
| Blocks column | 1152 / 288 | full height, header 72px |

Collapsing the left column: rail moves to x −291, navbar and page widen to
0 / 1152, the glass handle appears at x 14. Collapsing the right column:
navbar and page widen to 288 / 1152, the handle appears at x 1387. Both
handles are `opacity: 0` with `pointer-events: none` while their column is
open, so the page never carries a button it does not need. At 390px the rail
is `display: none`, the navbar keeps its brand and primary links, and the page
runs the full 390px.

Also verified on every page in the table:

| Check | Result |
| --- | --- |
| Horizontal scrollbar | None at 1440 px or 390 px |
| Canvas colour below the fold | `html` and `body` both `rgb(246,244,239)` |
| `h1` count | Exactly one |
| Skip link and `#region-main` | Present |
| JavaScript console errors | None (see the note below) |

The only console messages seen during the runs are occasional RequireJS
"Script error" lines for a core module (`core/menu_navigation`,
`jqueryprivate`). They are not reproducible: three consecutive loads of the
same page produced two clean loads and one error, and no `r.php` response was
ever 4xx or 5xx. The pattern points at the PHP built-in server's module cache
being regenerated under concurrent requests, not at the theme; on a normal
web server the modules are served from a warmed cache.

### The JavaScript loader

1.0.0 printed neither `standard_end_of_body_html` nor a favicon. The first
means RequireJS was never loaded, so every AMD call failed with
"requirejs is not defined", the dashboard's course overview and timeline
blocks stayed as skeletons, and the drawer JavaScript never initialised. The
second meant every page requested a favicon that did not exist and logged a
404.

Both were found by loading a page in a headless browser and reading the
console and the network log, and both are fixed in 1.0.1:

| Check | 1.0.0 | 1.0.1 |
| --- | --- | --- |
| `require.min.js` in the page | absent | present |
| `require([...])` calls in the page | 0 | 11 |
| `requirejs is not defined` console errors | 1–5 per page | 0 |
| Course overview block on `/my/` | skeleton placeholders | renders |
| Favicon request | 404 | 200 `image/vnd.microsoft.icon` |

### A/B defects against Boost

Three further defects were found by loading the same pages under Boost and
Nadine and comparing computed styles and boxes, which separates theme bugs
from core or environment behaviour:

| Probe | Boost | Nadine before | After the fix |
| --- | --- | --- | --- |
| Login username input width | 576 px | 196 px, placeholder clipped | 358 px (full form width) |
| Catalogue card padding | 16 px | 0 px, title on the border | 16–22 px (theme clamp) |
| Secondary navigation on site home | tab chrome | bare grey links on the canvas | card surface with pill links |

Root causes and fixes:

- **Input clipping.** Boost empties `$THEME->yuicssmodules` in its config;
  the child theme did not, so YUI's `cssbase-min.css` loaded on top of
  Bootstrap and its `input[type=text] { width: 12.25em }` (attribute selector,
  higher specificity than a class) beat `.form-control { width: 100% }`. Fixed
  by emptying the same list in the Nadine config.
- **Catalogue card.** Core pads the front-page and search course cards but
  leaves the catalogue browse cards at zero. The theme's card padding rule now
  covers `.courses .coursebox` as well.
- **Secondary navigation.** The strip is server-rendered as a plain link list
  and the moremenu drops the `nav-tabs` class at runtime, so neither core's
  tab CSS nor anything else styles it. The theme renders it as a bordered card
  (`--nl-surface`, `--nl-border`, `--nl-radius`) with pill links and an accent
  active state; tokens keep it correct in dark mode.

Two symptoms that looked like theme defects but are not:

- **Dashboard skeletons.** Present in screenshots taken before hydration
  finished; the same probe after 15 s shows the course overview and timeline
  blocks fully rendered (filter tabs, search, entries) with zero skeleton
  elements and zero console errors, and `lib/ajax/service.php` answers
  correctly when called directly.
- **Missing course navigation tree.** No Navigation block is placed on course
  pages in this dev site (`requiredblocks` is empty), identically under Boost.
  Site configuration, not a theme defect.

### The premium layer (1.1.0)

Measured in headless Firefox on the reference site, which carries six demo
courses in four categories.

| Element | 1440 px | 390 px |
| --- | --- | --- |
| Stats strip | 4 columns, surface card, 148 / 1144 | 1 column, 16 / 358 |
| Feature grid | 3 columns, 148 / 1144 | 1 column, 16 / 358 |
| CTA band | ink `rgb(20,32,31)` on canvas `rgb(246,244,239)` | same |
| CTA band (dark mode) | canvas `rgb(238,243,240)` on ink `rgb(15,21,19)` | same |

The band swaps foreground and background with the colour mode instead of using
a fixed dark colour, so it stays legible in both.

Block headers: all four dashboard blocks (calendar, recently accessed,
course overview, timeline) render a 33.6×33.6 tile — a solid gradient in the
block's own colour with a white glyph — `\f073` amber, `\f017` teal, `\f0db`
blue, `\f1da` purple. The map covers 45 block types with a neutral fallback,
and the same accent drives the tinted header band behind the title.

Administration:

| Check | Result |
| --- | --- |
| Settings form surface | white card, 16 px radius, rows padded 16.8 / 14.4 px |
| Settings section heading | 24.8 px display face with a 1 px rule and accent underline |
| Admin index rows | 14 category rows rendered as cards, 37 link chips at 999 px radius |
| Admin index rules | hidden (`display: none`), replaced by card spacing |
| Horizontal scroll, home | none at 1440 px or 390 px |
| Horizontal scroll, admin index | none at 390 px |
| Horizontal scroll, admin settings | none at 390 px after the 1.1.0 mobile padding fix |

### The docked sidebar rail (1.2.0; icons 1.2.2–1.2.3; handle and seam 1.3.0)

Core renders the `side-pre` blocks into a fixed overlay drawer that is only
visible once its JavaScript has run. 1.2.0 docks the same markup as a right rail
from 1200 px up, so the sidebar no longer depends on the drawer module at all.
1.2.1 adds the missing control: a handle that straddles the rail's inner edge
and collapses it, driven by a checkbox in `theme_nadine/drawers` rather than
by JavaScript.

| Check | 1440 px | 1280 px |
| --- | --- | --- |
| Rail box, open | x 1096, 344 wide, starts below the 72 px navbar | x 936, 344 wide |
| Main column, open | 1096 wide, content 1000 | 936 wide |
| Block width inside the rail | 314 | 314 |
| Rail handle, open | x 1078, 35 px, straddles the rail edge | x 918 |
| Rail box, collapsed | x 1443 (off-canvas), `visibility: hidden` | x 1283 |
| Rail handle, collapsed | x 1391, docked at the viewport edge | x 1231 |
| Page gutter, open → collapsed | 344 px → 0 px | 344 px → 0 px |
| Horizontal scroll, either state | none (`scrollWidth` 1440 = `clientWidth`) | none (`scrollWidth` 1280) |
| Course page (no side blocks) | no rail, no handle, gutter 0 px | unchanged |
| Admin settings page | rail carries the Admin bookmarks block | same |

The gutter is applied with `#page-wrapper:has(> #theme_boost-drawers-blocks)`, so
a page without side blocks keeps its full width. The collapsed rule repeats that
selector before `:has(.nl-rail-toggle-input:checked)` because a bare
`#page-wrapper:has(.nl-rail-toggle-input:checked)` loses to the id inside the
first `:has()`.

Block items: the Recently accessed items block renders 3 rows and 3 bare glyphs
at both widths. The glyphs are 21.6 px (1.35 rem) images tinted per purpose and
carry no background — `background-image` computes to `none` and the container is
`rgba(0,0,0,0)`. Sampled pixels land on the palette: `collaboration` →
`rgb(124,58,237)`, `assessment` → `rgb(234,88,12)`.

The tint has to be `!important`: core writes an inline
`filter: url("data:image/svg+xml;...#recolor")` `feColorMatrix` on each monologo
`<img>`, which recolours it to Moodle's own purpose palette (that is where the
`rgb(249,0,134)` pink came from). A plain declaration loses the cascade and the
theme colour never applies.

The Administration block on the front page renders the settings tree, so the
tree rules were checked against real markup rather than a mock: 16 navicons, 15
with their own Font Awesome glyph and one core icon without a glyph class
(Question banks) that the theme fills with a fallback. Branch rows use a caret
glyph that rotates when the group is open. Every row glyph is bare and coloured
by node type — measured `rgb(71,85,105)` for the admin rows, `background` `none`.

Calendar: weekday labels render at 10.4 px uppercase, day targets are 29.6 px
circles, and today is filled with the block accent (`rgb(217,119,6)`).

Dark mode (`[data-bs-theme="dark"]`): the tree and link glyphs mix 55% of their
accent with white before painting, and the monologo chain gets a trailing
`brightness(1.35)`, because the light-mode hues sit too close to a dark surface.
Sampled on `/my/`, the assessment glyph goes from `rgb(234,88,12)` on light to a
blend that reads `rgb(168,86,20)` against the dark card — visible without a tile
behind it.

Per-glyph colours in the Administration block (1.2.3), measured on `/`:

| Row | Glyph | Colour |
| --- | --- | --- |
| Settings | `fa-gear` | `rgb(71,85,105)` |
| Enrolled users | `fa-user-plus` | `rgb(13,148,136)` |
| Groups | `fa-users` | `rgb(22,163,74)` |
| Permissions / Assigned roles | `fa-user-tag` | `rgb(124,58,237)` |
| Check permissions | `fa-user-lock` | `rgb(219,39,119)` |
| Filters | `fa-filter` | `rgb(2,132,199)` |
| Reports / Logs | `fa-chart-column` | `rgb(234,88,12)` |
| Question banks | fallback | `rgb(217,119,6)` |

Row geometry is shared across block types: icon column `1.25rem`, gap `0.6rem`.
On `/my/` the activity icon starts at x 1141 and its text at x 1171
(20 + 9.6 px), and on `/` the tree glyph starts at x 1160 with its link at the
same x — the 32 px offset from the block edge is the depth-2 indent, not a
layout gap.

### Rail handle and seam (1.3.0)

At 1440 px the rail occupies x 1096–1440, so the seam sits at x 1096. The handle
measures 38 × 38 px at x 1043, i.e. it ends 14 px short of the seam, and it
carries `z-index: 4` against the seam's `3` — the divider can never paint over
the control. Background resolves to `color(srgb 1 1 1 / 0.8)` with
`backdrop-filter: blur(10px) saturate(1.5)`.

The seam is a 1 px gradient that is transparent at 0 % and 100 % and
`--nl-border-strong` between 5 % and 95 %, so it fades out at the navbar and at
the viewport bottom instead of ending on a hard cut. Collapsing the rail sets it
to `opacity: 0` and slides the handle to `inset-inline-end: 0.9rem`.

### Homepage counters (1.3.0)

The four figures ship as their final text, so they are correct with JavaScript
off. With JavaScript the script observes each `[data-nl-counter]` at a 0.35
threshold and animates from 0 over 1.5 s with a cubic ease-out.

Measured on `/` with a 900 px viewport: before scrolling the strip is below the
fold and reads `6, 6, 4, 20`; after `scrollIntoView` the values reset to
`0, 0, 0, 0`, and by 1.2 s they are back to `6, 6, 4, 20`. With
`prefers-reduced-motion: reduce` the script writes the target immediately.

### Primary colour setting (1.3.0)

`theme_nadine/primarycolor` is compiled into `$primary`, so it reaches
Bootstrap's own tokens rather than a theme-only variable. Verified by setting
`#1d4ed8` and recompiling: the served CSS contains `--nl-primary:#1d4ed8`,
`--bs-primary:#1d4ed8`, and the dark-mode derivation `--nl-primary:#7391e7`.
Unset, the value falls back to the accent colour (`#ef5e00` on this site), which
is what the theme did before the setting existed.

Changing the setting only takes effect after the theme CSS is recompiled, and
`theme_reset_all_caches()` alone is not enough — `moodledata/temp/theme/<theme>/all.css`
survives it. Clear `localcache/theme`, `temp/theme` and the
`core_postprocessedcss` store, then rebuild.

### Block header chip (1.5.0, radius changed in 1.11.0)

Measured on `.block[data-block] .card-title::before` with the built stylesheet:

| Mode | Radius | Border | Fill | Glyph |
| --- | --- | --- | --- | --- |
| Light | `32px` | `1px solid rgb(213, 211, 211)` (`#D5D3D3`) | 7% accent over the surface | the block accent |
| Dark | `32px` | 1px, `#D5D3D3` at 22% | 18% accent over the surface | 55% accent mixed with white |

The chip is 2rem square and the glyph is 0.82rem, so the icon column inside a
block header stays narrower than the 1.25rem row column below it. Since 1.11.0
the radius equals the box, which makes the chip a disc rather than a rounded
square.

### Live search (1.5.0)

Driven in headless Firefox against the real endpoint. Each case was asserted on
the DOM, not on a screenshot.

| Case | Expected | Result |
| --- | --- | --- |
| Type `learn` (6 courses on the site) | Panel opens, 3 rows, status "3 matching courses" | Pass |
| ArrowDown | First row gets `.is-active`, `aria-selected="true"`, `aria-activedescendant` set | Pass |
| Escape | Panel closes, input keeps its value | Pass |
| Type `zzzzq` | Panel stays open with "No courses match …", no rows, no footer link | Pass |
| Clear button | Input empty, panel closed, button hidden again | Pass |
| `/` outside a field | Focus moves to the hero input | Pass |
| Arabic (`?lang=ar`) | Status and empty state in Arabic, panel mirrors to RTL | Pass |
| Catalogue field | Same behaviour, one row for `digit` | Pass |
| POST without a session key | `{"errorcode":"missingparam"}` | Pass |
| POST with a wrong session key | `{"errorcode":"invalidsesskey"}` | Pass |
| GET | 405 `{"error":"methodnotallowed"}` | Pass |
| `<script>alert(1)</script>` as the query | Tags stripped, returns `alert(1)`, no markup | Pass |
| 41 requests in one minute | The 40th onward return 429 | Pass |

The endpoint's answer for `lead` was compared with `/course/search.php?search=lead`
and matched: one course, the same id.

### Course page (1.6.0)

Measured on `/course/view.php?id=2` at 1440×1100 with the built stylesheet, in
light and dark, English and Arabic.

| Case | Expected | Result |
| --- | --- | --- |
| Hero present on `course-view` | `.nl-course-hero` rendered, stock `#page-header` `display: none` | Pass — band 830px wide, bleeds 48px through the content gutter |
| Section numbering | Five headers carry `01`–`05` | Pass — the counter is on `li.section[data-number]`, so nested `.section` wrappers no longer shift it |
| Empty section body | `General` renders its header only | Pass — `.section-item > .content` is `display: none` when it has no `.activity` and no `.summarytext` |
| Activity chip | 41.6×41.6 with the purpose colour | Pass — content `#0d9488`, collaboration purple, both modes |
| Section title decoration | No underline | Pass — `text-decoration-line: none` in light and dark |
| Secondary navigation | Pill card sized to its content | Pass — 662px once the React island hydrates; hidden entirely while the island has no links |
| RTL | `dir="rtl"`, Arabic meta and tabs, Latin summary keeps its full stop | Pass — "4 أقسام", "9 أنشطة", "Get oriented: … arrives." |
| Dark mode | Surfaces, hairline and glyphs from the dark token set | Pass |

The hero data is read from the course, not hardcoded: 4 sections, 9 activities,
"Maya Hassan, Admin User", call to action "Start learning" and "Up next: How
this course works". The completion ring only appears when
`get_course_progress_percentage()` returns a number; this course has completion
enabled but no activity tracking, so the panel falls back to the overview
eyebrow.

### Course index drawer (1.7.0)

Measured on `/course/view.php?id=2` at 1440×1100 with the built stylesheet, in
light and dark, English and Arabic.

| Case | Expected | Result |
| --- | --- | --- |
| Default state | `#page` has no `show-drawer-left`, the panel is `visibility: hidden` | Pass — `drawers drag-container stateready` |
| Toggle geometry | 40×40 at the rail edge, level with the page card | Pass — `[294, 108, 40, 40]`, radius 12px |
| Toggle styling | Surface fill, hairline border, accent edge, focus ring on hover | Pass — `rgba(255,255,255,.88)` / `rgb(222,217,207)` light, `rgba(21,29,26,.88)` / `rgb(42,53,49)` dark |
| Rail collapsed | Tab steps below the rail handle instead of stacking on it | Pass — moves to `[6, 139, 40, 40]`, clear of the handle at `[14, 88, 38, 38]` |
| RTL | Tab docks to the left of the rail, accent edge on the right | Pass — `[1106, 108, 40, 40]` with `dir="rtl"` |
| Open drawer | Panel at `x 288`, close chip 32×32 at the header's trailing edge | Pass — `[288, 108, 285, 442]`, close `[533, 117, 32, 32]` |
| Toggle while open | Hidden, `aria-expanded` reads `false` when visible again | Pass — `display: none` while `show-drawer-left` |
| Persistence | Opening the index survives a reload for that user | Pass — `js/drawer.js` posts to `ajax/drawer.php`, which writes `drawer-open-index`; open → reload stays open, close → reload stays closed |
| Endpoint guards | GET 405, missing or invalid sesskey rejected, guests rejected | Pass — `405`; `{"errorcode":"missingparam"}`; `{"errorcode":"invalidsesskey"}` for a guest and for a wrong key |
| Activity page | Tab sits clear of the breadcrumb | Pass — tab `[294, 108]`, breadcrumb `[512, 132]` |

### Activity page (1.6.0)

Measured on `/mod/page/view.php?id=2`:

| Case | Expected | Result |
| --- | --- | --- |
| Module body | Reading card with real padding | Pass — `padding-block: 32px`, `padding-inline: 36px`, 16px radius |
| Body copy | Comfortable measure and rhythm | Pass — 1.02rem / 1.75 line-height, `unicode-bidi: plaintext` |
| Modified line | Muted meta under the card | Pass — 13.12px, `rgb(95, 106, 102)` light / `rgb(166, 179, 173)` dark |
| Prev/next navigation | Two equal cards | Pass |

### Icons (1.8.0)

The theme's own icons moved from Font Awesome to inlined Lucide SVGs. Checked
with Playwright on `/course/view.php?id=2`, `/my/` and `/` at 1440×1100.

| Case | Expected | Result |
| --- | --- | --- |
| Icons rendered | Every `.nl-icon` has a non-zero box | Pass — 19 / 21 / 33 per page; the only zero-size nodes are inside closed drawers, the hidden blocks toggle and the empty search field's clear button |
| Stroke | `stroke="currentColor"` on every SVG | Pass |
| Colour inheritance | Icon takes the container colour | Pass — rail glyphs `rgb(13,148,136)`, `rgb(2,132,199)`; collapse control `rgb(123,133,128)` |
| Size | Follows container `font-size` | Pass — 15px rail glyphs, 14px chevrons, 1.1em statistics tiles |
| Drawer handles | No bitmap pix fallback remains | Pass — the `e/sidebar_*` `<img>` tags are gone from the rendered HTML |
| Core icons | Unchanged | Pass — 98 `fa` elements still render on the course page (activity, block and admin glyphs) |
| Console | No new errors | Pass — only the pre-existing navigation aborts |

### Dashboard and homepage continue (1.9.0)

Checked with Playwright at 1440×1100 and 390×844, in English and Arabic, as
`admin`, as a guest, and as a user with no enrolments.

| Case | Expected | Result |
| --- | --- | --- |
| Dashboard aside | Second column of the welcome band, not empty | Pass — 768px main column, 312px aside, 283px continue tile |
| Continue ring | Real completion percentage | Pass — `75%` after three of four tracked modules completed |
| Home strip, signed in | Learner's own courses, not the catalogue | Pass — `.nl-courses` present, heading "Continue where you left off", 1 card with a 75 meter |
| Home strip, guest | Absent | Pass — `.nl-courses` not in the DOM |
| Home strip, no enrolments | Absent | Pass — falls back to core's "Available courses" only |
| Single course | Wide banner, media at 34% | Pass — 768×262 card, 260px media, no empty columns |
| Mobile | Stacked, no horizontal scroll | Pass — 358×333 card, `scrollWidth - clientWidth` = 0 |
| RTL | Mirrors, Arabic copy | Pass — `dir="rtl"`, heading "تابع من حيث توقفت", CTA "متابعة" |
| Card images | Load, lazily | Pass — `naturalWidth > 0` for every card after scrolling into view |
| Setting | Strip can be switched off | Pass — `showfrontpagecontinue` in *Homepage* settings |

### Site administration (1.10.0)

Checked with Playwright on `/admin/search.php` and
`/admin/settings.php?section=themesettingnadine`, at 1600×1100 and
390×844, in English and Arabic, in light and dark mode, and once with
JavaScript disabled.

| Case | Expected | Result |
| --- | --- | --- |
| Tab glyphs | One distinct icon per section | Pass — settings, users, book-open, chart-bar, puzzle, palette, server, chart-column, code, ellipsis for *More* |
| Glyph box | Masked box, not a font glyph | Pass — `::before` is 16.8px with `mask-image` pointing at `theme/image.php/nadine/theme/<rev>/lucide/<name>` |
| Selected tab | Accent fill, white glyph | Pass — `rgb(239, 94, 0)` light, `rgb(245, 155, 97)` dark |
| Server-rendered tabs | Glyphs before the React island mounts | Pass — same icons with JavaScript disabled, active tab still accent |
| Category headings | Per-category glyph in a tinted tile | Pass — `sparkles`, `target`, `award`, `layers`, `scale`, `map-pin`, `log-in`, `languages`, `message-square-text`, `credit-card`, `shield-check`, `home`, `list` |
| Long category | Heading stays at the top of the card | Pass — *Activity modules* (53 links, 475px) keeps its heading at the top |
| Plugin categories | Each gets its own glyph | Pass — `boxes`, `wrench`, `shield`, `key-round` in the *Plugins* pane |
| Plugin folders | Headings with no link still get a glyph | Pass — `folder-open` for the six folder headings |
| Unknown category | No empty tile | Pass — the glyph falls back to `layers` and the tile to the muted token |
| Root row | Full-width toolbar, no empty column | Pass — 1167px single column, 51px tall, five chips |
| RTL | Mirrors | Pass — tiles and glyphs on the right, chips left, same 10 icons |
| Mobile | Stacks, no mid-word break in the `h1` | Pass — 358px bar with General/Users/More, cards single column |
| Dark mode | No underline on navigation links | Pass — `textDecorationLine: none` on headings and chips; colour `rgb(255, 255, 255)` |
| Tab bar width | Aligned with the cards below | Pass — 1216px bar against 1216px cards |

### Homepage scroll animation (1.11.0)

Checked with Playwright (Firefox) on `/` at 1366×900, signed in, with the
*Homepage scroll animation* setting set to each of its three values, then again
with `prefers-reduced-motion: reduce`.

| Case | Expected | Result |
| --- | --- | --- |
| Off | No body class, no script request | Pass — body has no `nl-scroll--*` class, `document.scripts` has no `scroll-reveal.js`, sections render at `opacity: 1` |
| Reveal, before scroll | Sections below the fold are hidden | Pass — `opacity: 0`, `translateY(20px)` |
| Reveal, after scroll | Sections rise into place | Pass — `opacity: 1`, `transform: none` |
| AOS, before scroll | Directional hidden states | Pass — `translateY(36px)` for `up`, `scale(0.94)` for course cards |
| AOS, stagger | Items in one row follow each other | Pass — `--nl-reveal-delay` 0ms, 70ms, 140ms across a feature row |
| AOS, jump to the bottom | Nothing is left hidden | Pass — instant `scrollTo(0, 999999)` leaves 0 tagged elements |
| Markers cleaned up | Hover transitions return | Pass — `data-nl-reveal` and `.nl-reveal-in` are removed after `transitionend` |
| Script blocked | Page stays visible | Pass — the hidden state requires `nl-reveal-ready`, which only the script adds |
| Reduced motion | No animation, no class | Pass — `nl-reveal-ready` absent, sections at `opacity: 1` |
| Other pages | Untouched | Pass — dashboard, course and admin pages have no `nl-scroll--*` class and request no script |

The hero is never tagged, so the largest paint on the page is not delayed.

### Calendar (1.12.0)

Checked with Playwright (Firefox) on `/calendar/view.php` at 1366×1050 and
390×844, in English and Arabic, in light and dark mode, with eight demo events
seeded for November 2026. The month view is measured in the browser; the other
three views are checked for the same shell in the markup.

| Case | Expected | Result |
| --- | --- | --- |
| Card | One surface around toolbar, grid and footer | Pass — 694×1014, `rgb(246, 244, 239)`, 18px radius, 1px border |
| Toolbar | One padded row | Pass — 692×82, 12.8px / 15.2px padding, `overflow: visible` |
| Core utilities | `d-flex` and `p-1` do not win | Pass — computed `display: flex` and 12.8px padding; both utilities are `!important`, so the theme re-asserts them |
| View selector | Menu escapes the bar | Pass — 160×115 menu opens below the toolbar, nothing clipped |
| Weekday band | Recessed, uppercase | Pass — 37px tall on `--nl-canvas-deep` |
| Month cell | Even hairline grid | Pass — 99×132, single 1px rules, no doubled borders |
| Weekend | Tinted columns | Pass — Sat/Sun at 70% `--nl-canvas-deep` |
| Today | Ink disc with an accent halo | Pass — 30×30 `rgb(11, 20, 18)`, `0 0 0 2px` at 45% accent, digit `rgb(246, 244, 239)` |
| Event pill | Tinted in the event type's hue | Pass — 82×38, wash `color(srgb 0.827 0.275 0 / 0.11)` = 11% of the course hue |
| Event label | Two lines, ellipsis, no mid-word break | Pass — 60×28, clamp 2, "Assignment 2 briefing" breaks at the space |
| Event dot | Solid, no ring | Pass — 6×6, `border-width: 0`, `rgb(211, 70, 0)` |
| Event hues | One hue per type | Pass — site `#2b8713`, course `#d34600`, category `#9e619f`, group `#9a6e02`, user `#4e7c91`, other `#687889` |
| Events key block | Swatches match the pills | Pass — calendar glyph in the header, one tinted swatch per type |
| Month navigation | Pills with masked chevrons | Pass — 13×13 masked boxes, `chevron-left` / `chevron-right` |
| RTL | Chevrons swap | Pass — previous → `chevron-right`, next → `chevron-left` |
| Dark mode | Card and pills follow the palette | Pass — card `rgb(21, 29, 26)`, `text-decoration-line: none` on pills |
| Mobile toolbar | Two rows | Pass — 356×133; selector and course filter share row 1, the action spans row 2 at 332px |
| Mobile day | Compact cell, dot for events | Pass — 51×53 cells, 4px accent dot, no horizontal scroll |
| Day, upcoming, week | Same card shell | Pass — `.maincalendar` with the same `header` bar in all three |

Two core defaults had to be undone rather than overridden. The toolbar ships as
`header d-flex flex-wrap p-1`, and Bootstrap's utilities are `!important`, so
the theme's `display` and `padding` silently lost until they were re-declared
the same way. And `.visually-hidden` in Bootstrap 5.3 no longer positions the
course-filter label, so on phones it took a grid cell of its own and pushed the
select onto a third row.

### Calendar width (1.15.1)

Measured in Firefox at 1920×1080, 1366×900, 1024×900 and 390×844 on
`/calendar/view.php?view=month` and `?view=upcoming`, signed in, with the block
drawer open at the two wider sizes.

| Case | Expected | Result |
| --- | --- | --- |
| Container | No 830px measure | Pass — `max-width: none` on `#page.drawers .main-inner`; the page area between rail and block drawer is 1332px at 1920 |
| Card | Fills that area minus the gutter | Pass — 1236px at 1920 (was 734px), 930px at 1024, 346px at 390 |
| Month grid | Wider cells, same 7 columns | Pass — 176×132 at 1920, 133×132 at 1024, 49×51 at 390 |
| Header | Stays on the card's edge | Pass — `#page-header` 1236px at 1920, aligned with `.maincalendar` at x=336 |
| Upcoming view | Same container | Pass — event cards 1236px wide at 1920, 682px at 1366 |
| No regressions | No horizontal scroll | Pass — `scrollWidth` 1908/1012/378 against viewports 1920/1024/390 |
| 1366 with drawer | Unchanged | Pass — the page area is 778px there, below the old cap, so the layout is identical |

The cap came from Boost, not from the theme: `.pagelayout-standard
#page.drawers .main-inner { max-width: 830px }` outranks the theme's own
container rule at one class more, and the calendar page is `pagelayout-standard`.
The fix is scoped to `.path-calendar` so the reading layouts keep the measure.

### Control borders and select arrow (1.16.4)

Checked on `/login/index.php` at 1440×900 and on the profile form, signed in.
Values read from the compiled stylesheet and the rendered page.

| Case | Expected | Result |
| --- | --- | --- |
| Control border | One neutral hairline | Pass — `1px solid #8f8a80`, was the green-grey `#79837d` |
| Boundary contrast | At least 3:1 (WCAG 1.4.11) | Pass — 3.43:1 on the white bar, 3.12:1 on the canvas |
| Radius | Crisper than the old 7.2px | Pass — `0.375rem` (6px) on inputs and selects |
| Select arrow | Drawn at its own size, not squashed | Pass — Lucide chevron, `background-size: 1rem 1rem`, right 0.8rem |
| Arrow contrast | Visible in both palettes | Pass — `#6b7570` light, `#a6b3ad` dark |
| Sign-in header select | Keeps its 12rem cap and 40px height | Pass — 136×40 at 1440, label unchanged |
| Profile selects | Same treatment, no layout shift | Pass — "Select a country" and "Timezone" keep their 253px width |

### Header sign-in action (1.16.3)

Checked on `/` signed out at 1440×900 and 390×844, and with a second button
injected to confirm the pair when self-registration is enabled. Computed styles
read in headless Firefox against the saved response.

| Case | Expected | Result |
| --- | --- | --- |
| Shape | Pill, not a rounded rectangle | Pass — 102×32, `border-radius: 800px`, matches the 32px language selector |
| Glyph | Lucide `log-in`, 16px, decorative | Pass — `16×16`, `aria-hidden="true"`, 7px from the label |
| Glyph colour | Accent, label stays ink | Pass — icon `rgb(239, 94, 0)`, label `rgb(11, 20, 18)` |
| Label contrast | At least 4.5:1 | Pass — ink on canvas ≈ 17:1, unchanged by the accent |
| Icon contrast | At least 3:1 for a graphic | Pass — 3.4:1 on the canvas |
| Hover and focus | Glyph nudges 2px towards the reading edge | Pass — `translateX(2px)`; RTL uses `translateX(-2px) scaleX(-1)` so the door keeps pointing at the start edge |
| Pair with signup | Filled *Create account* with `user-plus` | Pass — accent fill, white glyph, same 32px pill, 8px gap |
| Phone | Still fits the bar | Pass — 390×844, brand truncates, pill keeps its label |

### Sign-in form surface and panel overlay (1.16.2)

Checked on `/login/index.php` at 1440×900 and 390×844, signed out. Computed
styles read in headless Firefox against a saved copy of the response, which is
enough here because the page is server-rendered and public.

| Case | Expected | Result |
| --- | --- | --- |
| Form surface | No card | Pass — `background-color: rgba(255,255,255,0)`, `padding: 0`, `border-radius: 0` |
| Form border | None | Pass — `border-top-width: 0` |
| Form shadow | None | Pass — `box-shadow: none` |
| Form measure | Still the 26rem column, centred | Pass — 416px at 1440, centred in the right half |
| Panel overlay | Accent wash plus diagonal veil over the paper fade | Pass — 3 gradient layers on `.nl-login-panel::before` |
| Panel tone, top-left | Warmer than before | Pass — mean 225,216,202 → 224,209,192 (more red, less blue) |
| Panel tone, foot | Slightly deeper | Pass — mean 180,151,134 → 174,147,130 |
| Copy contrast | Unchanged | Pass — the overlay sits under `.nl-login-panel__content` (z-index 1 against 2) |
| Phone | Form still inset, no overflow | Pass — 390×844, form 342px inside the 1.5rem column padding |

### Secondary navigation width and report card rule (1.16.1)

Measured in headless Firefox at 1920×900, 1440×900 and 390×844 on
`/report/view.php?courseid=1`. The tab bar was hydrated from the same origin so
the React island rendered; the card grid is server-rendered.

| Case | Expected | Result |
| --- | --- | --- |
| Bar width at 1920 | Content column, not the tab text | Pass — 1144px against a 1240px `.main-inner` (the 96px is its padding), tab list 1040px |
| Bar width at 1440 | Same | Pass — 1044px against 1140px, tab list 940px |
| Bar width at 390 | Full content width, no overflow | Pass — 346px against 378px, ends at x=362 inside the 390px viewport |
| Tab list | Not centred in the 830px reading measure | Pass — `max-width: none`; the list is 1040px, wider than the old cap |
| Edge alignment | Bar left edge on the card grid's edge | Pass — both at x=336 (1440) and x=526 (1920) |
| Report rule | 20% of the card, 30% opacity at rest | Pass — 24.6px of 125px (19.6%), 28.9px of 147px (19.7%), 19.5px of 100px (19.5%); `opacity: 0.3` |
| Report rule on hover | Full opacity | Pass — the existing `:hover`/`:focus-visible` rule sets `opacity: 1` |

The bar was never held back by `secondarynav-measured` — that class only flips
`opacity` from 0 to 1 once the overflow split has been measured. The width came
from the theme's own `.secondary-navigation { width: fit-content }`, which made
the card hug its tabs.

### Course reports hub (1.16.0)

Rendered from `/report/view.php?courseid=1` as admin. Firefox was run headless
against a saved copy of the response, because the Playwright browser cache is
empty and the disk is at 99% — the CSS and markup are the live ones, only the
session-bound requests are missing. The card grid is server-rendered, so it is
not affected by that substitution.

| Case | Expected | Result |
| --- | --- | --- |
| Cards | One per report in the navigation node | Pass — 7 cards: AI usage, Competency breakdown, Logs, Live logs, Activity report, Course participation, Event monitoring rules |
| Titles and links | The plugin's own name and URL, unchanged | Pass — each `href` matches the node's action; no title rewritten |
| Summaries | One line per known report, none invented for unknown ones | Pass — 7 summaries; a report without a mapping renders without one |
| Columns at 1920 | Four, inside the 1240px container | Pass — content 528→1768 (1240px), cards 298px |
| Columns at 1440 | Three | Pass — content 1044px, cards 332px |
| Columns at 390 | One | Pass — cards 336px, chevron hidden, padding 1rem |
| Dark mode | No underline through the card | Pass — `[data-bs-theme="dark"] .nl-report-card { text-decoration: none !important }` cancels the core content-link rule |
| Fallback | Core list if the context is not enriched | Pass — `{{^hasreports}}` block keeps the original `<ul>`; not rendered on this page (0 occurrences) |

### Sidebar block cards (1.12.1)

Checked with Playwright (Firefox) on `/` at 1366×1050, signed in, with the
course-categories and calendar blocks in `#block-region-side-pre`.

| Case | Expected | Result |
| --- | --- | --- |
| Sidebar radius | `--nl-radius-sm`, not `lg` | Pass — 7.2px on both 259px-wide cards |
| Main column | Large radius kept | Pass — the rule is scoped to the two side regions; `.block` still sets `--nl-radius-lg` |
| Header fill | None at all | Pass — `background-image: none`, `background-color: rgba(0, 0, 0, 0)` |
| Header rule | Dashed, in the block's hue | Pass — `border-bottom-style: dashed`, `color(srgb 0.646 0.764 0.804)` |
| Chip ring | The glyph's colour at 60% | Pass — `color(srgb 0.0078 0.5176 0.7804 / 0.6)` around a `rgb(2, 132, 199)` glyph |
| Block links | `#485150` | Pass — `rgb(72, 81, 80)`; `--nl-link` is `#485150` and `--nl-ink` is still `#14201f` |
| CTA band | Its own tokens | Pass — `rgb(20, 32, 31)` background, `rgb(246, 244, 239)` text |
| Calendar block | No card inside the card | Pass — the inner `.maincalendar` is transparent with no border |

Boost's drawer rule for `.block-header:has(>h3)` sets a hardcoded `#d9d4ca` at
four classes, so the dashed hairline is re-declared against the sidebar region
to outrank it.

### Calendar block (1.13.0)

Checked with Playwright (Firefox) on `/` at 1366×1050 and 390×844, signed in,
with demo events in November 2026 so the event dot is exercised.

| Case | Expected | Result |
| --- | --- | --- |
| Dead toolbar band | Gone | Pass — `display: none` on `.maincalendar div.header`; the calendar starts 15px under the block header |
| Month title | One line, centred | Pass — 161×18 box, centre within 0px of the toolbar's centre, `white-space: nowrap` |
| Arrows | 26px discs | Pass — 26×26 at each end of a 225px toolbar, `border-radius: 50%` |
| Month names beside arrows | Hidden | Pass — `.arrow_text` is `display: none` |
| Day cell | 2.4rem, centred | Pass — 32×38 with `vertical-align: middle` |
| Day disc | 1.7rem | Pass — 27.2px, 12.48px semibold numerals |
| Today | Ink disc, accent halo | Pass — `rgb(11, 20, 18)` with `0 0 0 2px` at 45% accent; white disc and `rgb(15, 21, 19)` numerals in dark mode |
| Event dot | 3.2px accent under the disc | Pass — `rgb(239, 94, 0)`, 1.6px from the cell floor; core's 3em period computes to `content: none` |
| Weekend wash | Lighter than the page's | Pass — `color(srgb 0.965 0.959 0.942)`; `rgb(0.061 0.086 0.076)` in dark |
| Weekend cells on hover | Keep their wash | Pass — the cell hover is cancelled, the disc hover takes over |
| Footer rows | 29px each, no bullet | Pass — both links 30px apart, `::after` is `content: none` on core's separator, chevron mask 12px |
| Footer chevron | Lucide, in the link colour | Pass — `chevron-right` mask at 12px, `currentColor` |
| RTL | Arrows mirror | Pass — `previous` takes `chevron-right`, `next` takes `chevron-left` |
| Phone | No horizontal scroll | Pass — `scrollWidth - clientWidth` is 0 |
| Console | No errors | Pass |

Core ships a block-region sheet for this calendar
(`[data-blockregion]:not([data-blockregion="content"]) .block.block_calendar_month …`)
that sizes the arrows at 30% of the toolbar, the heading at 40% and the cells at
`auto`, all at seven to ten classes. Those three rules are re-declared with the
same prefix; a plain `.block_calendar_month` selector loses however late it is.
The toolbar itself carries `d-flex`, which is `!important`, so removing it also
needs `!important`.

### Administration block tree (1.13.0)

Checked with Playwright (Firefox) on `/` at 1366×1050 with the Administration
block in `#block-region-side-pre`.

| Case | Expected | Result |
| --- | --- | --- |
| Branch toggle | Lucide chevron, not a solid caret | Pass — `content: ""`, `mask-image` `chevron-right`, `background-color: rgb(148, 163, 184)` |
| Glyph size | Small, inside the 20px icon column | Pass — `mask-size: 11.2px 11.2px` |
| Open branch | Points down | Pass — `matrix(0, 1, -1, 0, 0, 0)`, i.e. `rotate(90deg)` |
| RTL | Mirrors and turns the other way | Pass — `chevron-left` with `rotate(-90deg)` |
| Leaf rows | Unchanged Font Awesome chevron | Pass — `content: "\f105"` still applies |

### Messaging page (1.14.0)

Checked with Playwright (Firefox) on `/message/index.php` at 1366×1050 and
390×844, signed in as Admin, light and dark. The contact list needs a moment to
arrive from the AJAX loader, so the probe waits for the first row rather than
for the frame.

| Case | Expected | Result |
| --- | --- | --- |
| Frame | Fills the viewport | Pass — 982×874 at y=116, `border-radius: 16px`, 1px `#ded9cf` |
| Dashboard welcome | Absent | Pass — the panel is limited to `my-index`; `pagelayout-mydashboard` alone no longer renders it |
| Contacts rail | Lighter than the canvas | Pass — `rgb(251, 250, 246)`, 1px inline-end divider |
| Search | One rounded box with its submit button | Pass — 250×40, `border-radius: 7.2px`, 1px `#ded9cf`, accent `:focus-within` ring |
| Settings gear | Round ghost target | Pass — 2.15rem disc, `border-radius: 50%` |
| Contacts pill | Pill, small label | Pass — 97×26, `border-radius: 999px`, 12.16px |
| Section labels | Uppercase micro-labels | Pass — 318×37 rows, 11.2px, `text-transform: uppercase`, `rgb(123, 133, 128)` |
| Section focus on load | Bar, not a box | Pass — `inset 2px 0` accent, `outline-style: none`; core's clipped 3px ring is gone |
| Contact row | Small radius, warm wash on hover | Pass — 303×77, `border-radius: 7.2px`, hover `rgb(239, 236, 228)` |
| Row focus (mouse) | No Bootstrap glow | Pass — `box-shadow: none` |
| Conversation header | Compact, hairline | Pass — 637×63, white, `8px 10.4px`, 1px bottom |
| Empty conversation | Dashed note | Pass — 416×105, `1px dashed #ded9cf` |
| Composer | Rounded field, accent send | Pass — 582×86, `rgb(251, 250, 246)`, 7.2px; send 35×35 `rgb(239, 94, 0)` |
| Sent bubble | Primary fill, inverted ink | Pass — `rgb(1, 39, 90)` on `rgb(255, 255, 255)`, 12px radius, tail `display: none` |
| Received bubble | Recessed canvas | Pass — `rgb(239, 236, 228)` on `rgb(20, 32, 31)` |
| Dark mode | Tokens follow | Pass — frame `rgb(21, 29, 26)`, rail `rgb(25, 34, 31)`, pane `rgb(15, 21, 19)`, composer `rgb(25, 34, 31)` |
| Phone, no conversation | Rail full width, conversation hidden | Pass — rail 332×666 of a 358px frame, search 264 wide, conversation `display: none` |
| Phone, conversation open | Rail hidden, conversation full width | Pass — rail `display: none`, conversation 332×666 |
| Phone, back control | Rail returns | Pass — rail `display: flex` again |
| Horizontal scroll | None | Pass — `scrollWidth - clientWidth` is 0 at both widths |
| Console | No errors | Pass |

Two utility traps bite here. The contact row and the two columns carry
`d-flex`, and Bootstrap's utilities are `!important`, so hiding a pane needs
`display: none !important`. The row is also in core's `.aabtn` group, which
paints a Bootstrap glow on `:focus`; that is switched off so a clicked row keeps
only the wash, while a keyboard focus gets the theme's ring.

### Message drawer (1.14.1)

Checked with Playwright (Firefox) at 1366×1050 on `/my/`, signed in as Admin,
light and dark. The drawer is opened from the navbar's message icon; it arrives
through an AJAX loader, so the probe waits for `data-shown="true"` and a
bounding box inside the viewport rather than for a class.

| Case | Expected | Result |
| --- | --- | --- |
| Frame | Docked, full height, hairline on the inner edge | Pass — 319×982 at x=1047, `rgb(255, 255, 255)` |
| Close | Quiet disc, muted glyph | Pass — 30×30, `border-radius: 50%`, `rgb(123, 133, 128)` |
| Header band | One padding, one hairline | Pass — 319×91, `7.2px 8.8px`, 1px `rgb(222, 217, 207)` |
| Search | One rounded box with its submit button | Pass — 259×40, `border-radius: 7.2px`; input 221×38 borderless; submit 38×38 with a 1px inline-start divider |
| Settings gear | Round ghost target | Pass — 34×34, `border-radius: 50%` |
| Contacts pill | Pill, small label | Pass — 100×26, `border-radius: 999px`, 12.16px |
| Overview list | Rail tone, sections on hairlines | Pass — body `rgb(251, 250, 246)`, 1px `rgb(222, 217, 207)` between sections, first section borderless |
| Section labels | Uppercase micro-labels, Lucide caret | Pass — 316×43, 11.2px, `uppercase`, `rgb(123, 133, 128)`; caret is a masked `chevron-right`, turned down when expanded |
| Contact row | Small radius, transparent, warm wash on hover | Pass — 305×77, `border-radius: 7.2px`, hover `rgb(239, 236, 228)` |
| Contacts / Requests | Segmented control | Pass — 301×38 on `rgb(239, 236, 228)`, `border-radius: 999px`; active tab white |
| Settings groups | Micro-labels on hairlines | Pass — uppercase 11.2px, 1px rule above each group after the first |
| See all | Hairline footer link | Pass — 319×36, 11.2px uppercase, `rgb(123, 133, 128)` |
| Dark mode | Tokens follow | Pass — header `rgb(21, 29, 26)`, hairline `rgb(42, 53, 49)`, close `rgb(166, 179, 173)`, segmented control `rgb(11, 16, 14)` |
| Horizontal scroll | None | Pass — `scrollWidth - clientWidth` is 0 |

The drawer and the page share one component: `$nl-msg` lists both mounts
(`#page-message-index .message-app, .message-app[data-region="message-drawer"]`)
and `$nl-msg-head` covers `panel-header-container` on the page and
`header-container` in the drawer. The drawer block adds only its own chrome.

Two core weights bite here as well. Each header band carries `p-1`/`px-sm-2`,
so its padding needs `!important`; each overview section carries `.border-0`,
so the hairline between sections needs `!important` too — the same fix restores
the separators on the messaging page.

### Message drawer over the docked column (1.14.2)

Checked with Playwright (Firefox) on `/my/`, signed in as Admin, at 1366×900,
1200×800 and 390×844, light and dark. The drawer is opened from the navbar's
message icon and the page is read before and after.

| Case | Expected | Result |
| --- | --- | --- |
| Header band, drawer open | Reaches the viewport's right edge | Pass — `.nl-navbar` 1078px wide at x=288 on 1366 (was 790, ending at the seam) |
| Header content | Nothing moves when the band grows | Pass — `#usernavigation` at x=591 before and after; 425 at 1200 |
| Column head | Hidden with the column | Pass — `.nl-rail__head--panel` is `visibility: hidden` while open and `visible` once the drawer closes |
| Drawer top | On the header's bottom border | Pass — y=72 at 1366, 1200 and 390; core's own constant put it at 68 and cut the border |
| Close control | 0.5rem inset on every side | Pass — widget `margin: 8px`, `padding: 0`; disc 30×30 with 8px above and 8px from the edge |
| Collapsed column | Band left alone | Pass — the compensation is limited to the docked state, so a collapsed column keeps its header at the edge |
| Phone | Unchanged | Pass — no rail, navbar 390 wide before and after |
| Horizontal scroll | None | Pass — `scrollWidth` equals `clientWidth` at every width |

The band is translucent (`--nl-header-translucent`), so hiding the column's head
is what removes the ghost of its heading; extending the band alone left "Blocks"
and its chevron showing through at 0.88 alpha.

### Hero spacing (1.14.3)

Checked with Playwright (Firefox) on `/` at 1366×900, 1024×800 and 390×844.

| Case | Expected | Result |
| --- | --- | --- |
| Hero block padding | None — `.main-inner` owns the rhythm | Pass — `padding-top`/`padding-bottom` compute to `0px` at all three widths |
| Hero top edge | Flush with `.main-inner` | Pass — hero y=108 and `.main-inner` y=108 at 1366 and 1024; 68/68 on the phone |
| Art column alignment | Top-aligned with the copy | Pass — `align-self: start`, art y=108 and copy y=108 at 1366 and 1024; art y=725 below the copy on the phone, as stacked |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

The mobile override (`.nl-hero { padding-block: 1.75rem 2rem }` under 768px) is
commented out as well, so the phone does not keep the padding the desktop lost.

### Front-page course tiles (1.14.3)

Checked with Playwright (Firefox) on `/`, signed in, at 1366×900, 1024×800 and
390×844.

| Case | Expected | Result |
| --- | --- | --- |
| Grid | Tiles fill the column width | Pass — 2 columns of 336px at 1366 (the docked column leaves 790px), 3 of 300px at 1024, 1 of 358px on the phone |
| Card anatomy | Cover, title, summary, teachers, in that order | Pass — image y is above the title in every card; `display: contents` on core's two wrappers lifts them into the card |
| Teacher mark | Person glyph before the name | Pass — `::before` 13.6px with the `lucide/user-round` mask; the "Teacher:" label is clipped to 1px and stays in the accessible name |
| Row rhythm | Cards in a row share a height | Pass — 387/387 and 383/383 at 1366; 363 and 359 at 1024 |
| Section heading | Same scale as the other front-page titles | Pass — 40px at 1366, 34.8px at 1024, 27.2px on the phone |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

The heading is a sibling of the list, not a child, so it cannot ride on the
list's selector — an early version of the rule silently matched nothing.

### Login page (1.14.3)

Checked with Playwright (Firefox) at 1366×900 in both colour modes and at
390×844.

| Case | Expected | Result |
| --- | --- | --- |
| Panel copy | Legible over the photograph | Pass — the image is dimmed and desaturated under a two-stop scrim; the small print at the foot of the panel reads |
| Brand | Printed once | Pass — the panel's copy is `display: none` while the top bar carries it; other layouts that borrow the panel keep it |
| Split and footer | No seam | Pass — `.login-layout-left` bottom and `.nl-footer` top are both 968 at 1366, 920 on the phone |
| Guest button | Glyph inline with the label | Pass — the mark is drawn on `#loginguestbtn::before`; the Font Awesome element's zero-height line box had dropped it out of the line |
| Language selector | One on the page | Pass — the in-card menu is hidden; the top bar's stays |
| Link decoration | Same in both modes | Pass — `text-decoration-line: none` in light and dark; Moodle's dark stylesheet underlines `#page-header` links at (1,5,1), so the card rule needs `!important` |
| Phone panel | Slim banner with eyebrow and headline | Pass — panel 138px tall above the form; image, points and note hidden |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

### Login page (1.14.4)

Checked with Playwright (Firefox) at 1366×900, 1024×800, 1920×1000 and
390×844, in both colour modes, plus a failed sign-in.

| Case | Expected | Result |
| --- | --- | --- |
| Panel surface | Warm paper, ink copy | Pass — `#f8f3e8` panel, `#101c1a` title, no core photograph and no "AI generated" watermark |
| Illustration | Fills the panel behind the copy | Pass — art box 683×900 at 1366 and 512×800 at 1024, at `x: 0, y: 68` on both; the overlay is opaque to 44% and clear by 80% |
| Left-block rules | No divider lines under the text | Pass — the list items and the note carry no border; only the icon discs and the copy remain |
| Card | No border | Pass — `border-width: 0` at every width; the card is held by its surface and shadow |
| Password control | Exactly one, with a Lucide glyph | Pass — `querySelectorAll('.toggle-sensitive-btn').length` is 1; core's pix image is `display: none` and `::before` masks `lucide/eye` |
| Password control state | Glyph follows the field | Pass — `type="text"` resolves the mask to `eye-off`, `type="password"` to `eye` |
| Password control size | Comfortable target | Pass — 46×50 inside the field, focus ring in the accent, no Bootstrap halo |
| First focus | Username on desktop, body on the phone | Pass — `activeElement` is `username` at 1366 and 1024, `page-login-index` at 390 |
| Dark mode | Panel dimmed, not inverted | Pass — `filter: brightness(0.92)` on the panel; the illustration keeps its paper |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

The duplicate control was the whole bug: core has shipped `toggle-sensitive`
since 4.x, and the theme's own button sat on top of it, so the field showed two
eyes at once and the theme's glyph — a Font Awesome class the page never loads —
drew nothing.

### Footer (1.14.5)

Checked with Playwright (Firefox) at 1366×900, 1024×800 and 390×844, in both
colour modes, with all eight networks configured.

| Case | Expected | Result |
| --- | --- | --- |
| Surface | Dark colophon in light mode, a lifted surface in dark | Pass — `rgb(11, 20, 18)` in light, `rgb(19, 29, 26)` in dark |
| Height | Content-sized, never collapsing | Pass — 417px at 1366, 521 at 1024, 747 on the phone |
| Social row | One row of eight | Pass — 8 buttons of 34×34, a single row at every width |
| Social order | Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp, GitHub | Pass — the same order in the markup and on screen |
| Empty network | Not rendered | Pass — clearing `footersocialgithub` drops the button and the row closes up (6 rendered with two cleared) |
| Rejected address | Not rendered | Pass — `javascript:alert(1)` in a social field is dropped by `clean_param(..., PARAM_URL)` and never reaches the markup |
| Identity | Full site name | Pass — the navbar's 42vw cap no longer applies in the footer; "Nadine Academy" fits at 390px |
| Columns | Three under the identity from 1199px down | Pass — brand spans the grid at 1024, three 290px columns below it |
| Bottom bar | Copyright, back to top, Moodle credit | Pass — one row at 1366, stacked at 390 |
| Contrast | AA | Pass — tagline 17.3:1, links 10.7:1, headings and bottom bar 6.5:1, accent hover 5.6:1 |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

The collapse was the one real defect. The footer is a flex item in the drawers
layout, and `overflow: hidden` — needed to clip the painted glows — sets a flex
item's automatic minimum size to zero, so the footer was allowed to shrink to
nothing on a page taller than the viewport. `flex: none` restores its content
height.

### Editable headlines (1.14.6)

Every new field was set to a distinctive test string, the caches were purged,
the page was loaded, and then the field was cleared to confirm the default
returns.

| Surface | Field | Result |
| --- | --- | --- |
| Site home | Main button label | Pass — button reads `TEST HERO CTA`, returns to "Browse courses" when cleared |
| Site home | Side panel title and text | Pass — context built with the configured strings; the panel is only printed when no hero image is uploaded |
| Course index | Catalogue eyebrow, title, introduction | Pass — `TEST CATALOGUE EYEBROW`, `TEST CATALOGUE TITLE`, `TEST CATALOGUE INTRO`; the introduction keeps its `<p>` markup |
| Dashboard `/my/` | Welcome eyebrow, heading, introduction | Pass — `TEST DASHBOARD EYEBROW`, `TEST HELLO Admin User` from `TEST HELLO {name}`, `TEST DASHBOARD INTRO` with its markup |
| Site home | Features eyebrow, title, three cards | Pass — `TEST FEATURES EYEBROW`, `TEST FEATURES TITLE`, `TEST FEATURE ONE`, `TEST FEATURE THREE TEXT` |
| Site home | Closing call to action | Pass — `TEST CTA TITLE`, `TEST CTA TEXT`, `TEST CTA BROWSE`; the second button reads "Sign in" here because self-registration is off |
| Login | Panel eyebrow, image description | Pass — `TEST LOGIN EYEBROW`, and the illustration's `alt` is `TEST LOGIN ALT` |
| Settings page | All 23 new fields present | Pass — 54 `s_theme_nadine_*` fields render on `admin/settings.php?section=themesettingnadine` |
| Defaults | Untouched site unchanged | Pass — cleared fields fall back to the translated strings, including "Welcome back, Admin User" |
| Seeded values | Fields show the live copy | Pass — `heroeyebrow` `A calmer way to learn`, `herotitle` `Learning that moves at your pace`, `heroprimarylabel` `Browse courses`, `dashboardgreeting` `Welcome back, {name}`; the rendered pages are unchanged |

The introductions were printing their own tags before this pass: the context
sanitises HTML but the templates escaped it. Both now render through `{{{…}}}`
inside a `<div>`, matching the hero and login panels.

### Profile page (1.15.0)

Checked with Playwright (Firefox) on `/user/profile.php` at 1366×900, 1024×800
and 390×844, in both colour modes, with and without a biography and with
department, institution, city and country filled in.

| Case | Expected | Result |
| --- | --- | --- |
| Identity band | Header and facts share one surface | Pass — 982×248 at 1366, 942×233 at 1024, 358×324 on the phone |
| Picture | Large, ringed | Pass — 80×80 at 1366 and 1024, 64×64 on the phone, initials centred |
| Name | Display size, single h1 | Pass — 35.2px at 1366, 26.6px at 1024, 25.6px at 390; one `h1` on the page |
| Facts strip | Only what the tree shows | Pass — "Learning Design · Nadine Academy", "London, United Kingdom", "Member since October 2026"; a user with no department or institution gets two chips, not empty ones |
| Message button | Lucide glyph, no Font Awesome | Pass — `::before` masks `lucide/message-square-text`, the core `.icon` is `display: none`, 999px radius |
| Page width | The full container, not the reading width | Pass — `.main-inner` is 982px of the 1078px page area at 1366 and 942px at 1024; the 830px `limitedwidth` cap no longer applies |
| Edit action | In the band, opposite the facts | Pass — pill at the band's right edge; the card's own row is removed while the band offers it |
| Card grid | Responsive columns | Pass — 2 columns of 483px at 1366, 2 of 463px at 1024, 1 of 358px at 390 |
| Detail rows | Label above value on the phone, beside it on desktop | Pass — `dt`/`dd` share a row above 576px and stack below it |
| Course list | Chips on the full card width | Pass — six course names wrap as pills; the label sits above them |
| Biography | Readable lead above the tree | Pass — bordered surface with a 3px accent edge, full content width |
| Link decoration | No underline at rest or on hover | Pass — `text-decoration-line: none` in both modes; Moodle's dark stylesheet underlines content links at rest, hence the scoped override |
| Contrast | AA | Pass — light 5.6:1 muted, 16.7:1 body, 18.7:1 name; dark 7.9:1 muted, 15.3:1 body, 17.2:1 name |
| Horizontal scroll | None | Pass — `scrollWidth` equals `innerWidth` at every width |

The band is built from the user record, not from theme copy, so it has no
settings of its own; the hidden-profile-field rules that apply to the tree are
applied to the chips as well.

The band's edit link was checked against core's own node for four viewers and
matched every time: a learner on their own profile gets `/user/edit.php`, an
administrator viewing a learner gets `/user/editadvanced.php`, and a learner
viewing an administrator or a teacher viewing a learner get no link — core
renders none for those either. When the band does carry the action the card's
own row is `display: none`, and the first detail row loses the hairline that
would otherwise sit directly under the heading.

### Link hover (1.14.0)

Checked with Playwright on `/` (27 links) and `/my/` (7 links): every visible
link computes `text-decoration-line: none` at rest **and** on hover. Bootstrap
5.3's `a:hover { text-decoration: underline }` is overridden globally, and the
theme's own three hover rules (block rows, the search "show all" link, the
footer columns) no longer add one.

### Outline button states (1.16.5)

Rendered the compiled bundle (`all_*.css`) in Firefox 157 against a page holding
a resting and an `.active` `btn-outline-secondary`, and read the computed
borders:

| State | Light | Dark |
| --- | --- | --- |
| Resting border | `rgb(207, 201, 190)` — `--nl-border-strong` | `rgb(56, 68, 63)` |
| Pressed border | `rgb(95, 107, 104)` — `--nl-border-active` | `rgb(139, 153, 147)` |
| Pressed fill | `rgb(251, 250, 246)` | `rgb(25, 34, 31)` |

Before 1.16.5 the pressed border resolved to `--nl-ink` (`#14201f` in light,
`#eef3f0` in dark) — a near-black ring on a near-white fill. In dark mode the
whole token block was also inert: Boost's `[data-bs-theme="dark"]
.btn-outline-secondary` (0,2,0) outranked the theme's bare class (0,1,0), so the
button fell back to `#9fa6a3` for the resting border, the hover fill and the
pressed border alike. The theme now names the dark palette explicitly, and keeps
the `border-color` restatement on the bare class — with the attribute attached
it tied with `.btn-outline-secondary.active` and, being later in the bundle,
pinned the pressed border back to the resting grey.

### Course browser (1.16.6)

Rendered `/course/index.php` and `/course/index.php?categoryid=2` as an admin
against the compiled bundle, with the categories expanded from the real
`course/category.ajax.php` payload, and read the computed layout in Firefox 157.

| Surface | 1440px | 390px |
| --- | --- | --- |
| Category card | 1044×63 | 346×63 |
| Header padding | 15.2px / 18.4px | same |
| Chevron | 30.4×30.4 | same |
| Chevron, open | `matrix(0,1,-1,0,0,0)` — 90°, accent | same |
| Category page grid | `514px 514px`, gap 16px | `346px`, one column |
| Core toolbar | 32px tall (menu only) | same |
| Horizontal scroll | none | none (`scrollWidth` 378 < 390) |

The chevron sits at the trailing edge because it is the control: core's expander
ignores clicks that land on the anchor, so the name has to keep looking like a
link and the chevron like a button. Both remain keyboard reachable — core adds
`tabindex` to `.categoryname` on the first Tab and binds Enter to the toggle.

The dark pass caught two regressions the light palette hides: core underlines
every `#region-main` link in dark mode, which turned the category name, the
course rows and the card's teacher line into running text, and core's
`.with_children` background rules outranked the reset that removes the 12px tree
glyph, so the old icon showed through the new chevron until the selector was
matched class for class.

### Homepage course card (1.16.7)

Rendered the continue strip from the live `/` markup against the compiled
bundle in Firefox 157 — the one-card banner and a three-card row — and read the
computed geometry:

| Surface | Value |
| --- | --- |
| Banner card | 1176×194 (was 1176×400) |
| Banner media | 399×192 |
| Progress groove | 719×8 |
| Fill at 75% | 539px — 75.0% of the groove |
| Category chip | 131×25 |
| Caption | `rgb(95, 106, 102)` — `--nl-muted` |
| Action label | `rgb(11, 20, 18)` on `rgba(239, 94, 0, 0.08)` |

The banner used to take its height from the cover: the generated fallback is a
square 320×320 SVG, so a 399px-wide media column produced a 400px-tall card with
the copy floating in the middle. The image is out of flow in the banner now, and
the body's `min-height: 12rem` sets the card.

With the dev site's `#EF5E00` accent the fill resolved to `#CD5100`
(`--nl-accent-strong`): 3.68:1 against the groove in light, 6.30:1 in dark. The
pure accent would have been 2.8:1 on the light groove. The caption is 5.61:1 on
the surface and the action label 17.0:1 on its chip, so neither relies on the
accent's own contrast. The fill's one-second grow is removed under
`prefers-reduced-motion`, and the groove fills from the inline start in RTL
because the fill is a block child, not a left-anchored box. *(1.16.8 replaced
this fill with the flat faded token described below.)*

### Dashboard course cards (1.16.8, ring re-measured in 1.29.13)

Rendered `block_myoverview/view-cards` from the theme's own progress partial
with a three-course context, against the compiled bundle at 1144px, and read the
computed geometry:

| Surface | Value |
| --- | --- |
| Card | 343×323 |
| Cover | 341×192 — 16:9 (was 341×112, core's 7rem) |
| Footer row | 341×59 |
| Progress ring | 46.4×46.4 — 3.84px stroke, 38.72px cut-out |
| Ring arc at 45% | `conic-gradient(rgb(239, 94, 0) 45%, …)` |
| Figure inside the ring | 21.6×11.2 at 45% — 11.2px Georgia 700, "%" 8.3px in `--nl-muted` |
| Caption beside the ring | 10.2px, `rgb(95, 106, 102)` |
| Row gap | 9.6px |
| Title | Iowan Old Style 16.63px |
| Category | 10.08px `rgb(95, 106, 102)` |

The override reaches the block's JavaScript: a `core_output_load_template` call
for `block_myoverview/progress-bar` with `themename=nadine` returns the
theme's source, not core's `.progress-text`.

Core's `bg-white` and `p-2` on the two footers are `!important` utilities, and
in the dark palette `--bs-white` is a dark grey — the pair painted a lighter
strip across the card with an 8px pad around the menu. Both are neutralised on
the footer row, which owns the surface and the padding instead.

The groove became a ring in 1.29.13. The ring is the same 41.6px element in the
theme's own course card, so one `nl-progress-ring` mixin serves the dashboard
card, the course card and the 7rem course hero ring; only the size, the cut-out
and the track tint are parameters. The arc is `--nl-accent`, which clears the
3:1 non-text minimum against the surface in all eight palettes — the old
`--nl-progress-fill` softened the accent 28% towards white and reached only
2.44:1, so the bar never met 1.4.11. The same partial feeds the list and summary
views, where the block is 26rem wide at most.

### Secondary navigation rail and admin tiles (1.16.9)

Rendered the served bundle against the React nav's own markup (`nav-item d-flex
align-items-center` around an `mds-nav-pill`, which is what
`lib/js/esm/build/nav/Nav.js` emits) at a 1044px card:

| Surface | Value |
| --- | --- |
| Rail strip | 1032×60 inside a 1044px card — 4.8px inset from the 0.3rem padding |
| Strip fill | `rgb(249, 249, 248)` on the white card; `rgb(18, 25, 23)` on `rgb(21, 29, 26)` in dark |
| Strip radius | 8px (`0.5rem`) |
| Pill | 105×35, centred — 12.5px of strip above and below |

The 60px height is core's (`.secondarynav-tabbar`); the pills are centred by the
`d-flex align-items-center` the React nav puts on each `nav-item`, not by the
theme. Without that class the pills sit on the top edge and the band reads
lopsided, so any future markup change has to keep it. The strip is decorative —
1.01:1 against the card in light — and carries no control boundary, so the
contrast floor does not apply.

The administration heading tile is 36.8×36.8 (2.3rem) with a 32px radius, i.e.
a circle. The radius is set on `::before` only: `::after` is the masked glyph,
and rounding it would clip the icon's corners.

### Front-page catalogue cards (1.17.0)

Rendered the front page's own `#frontpage-available-course-list` markup against
the compiled bundle at 1440px (760px content column):

| Surface | Value |
| --- | --- |
| Grid | 2 columns, 365px cards, 26px gap |
| Cover | 365×205 — 16:9 |
| Entry plate | 12px inset, 0.72rem digits on `rgba(11, 20, 18, 0.68)` |
| Open disc | 32×32 at 11px inset, 1rem arrow |
| Title | display serif 1.12rem / 1.26 |
| Summary | 0.88rem / 1.62, clamped to 3 lines |
| Footer | 1px hairline, 12px above the teacher line |
| Hover spine | 1px inset accent, card at `opacity: 0.95` |

The open disc and the teacher glyph are both masked Lucide SVGs. The arrow is
`arrow-right` rotated -45°: Lucide ships no up-right variant, and the hover
nudge rides the rotated x axis so it travels up-right. Two things had to be
verified by rendering rather than reading:

- `::after` paints after `::before` at equal `z-index`, so the white disc was
  drawn on top of the arrow and the button read as an empty circle. The arrow
  now sits at `z-index: 3`.
- The `.moreinfo` hook is only usable because core prints it empty
  (`<div class="moreinfo"></div>`); the disc is scoped to `:empty` so a plugin
  that puts a real link there is not covered by it.

## Edit profile

`/user/editadvanced.php?id=4&returnto=profile`, measured on the compiled bundle
at 1440×2200 with the save bar forced visible (the probe removes JavaScript, so
`hasstickyfooter` never runs):

| Surface | Value |
| --- | --- |
| Content column | 1031px, up from the 830px reading measure |
| Section card | 1031×…, 1px border, 1rem radius, 1rem/1.4rem padding |
| Section heading | display serif 1.3rem, index `01`–`05` in 0.78rem muted |
| Field grid | 2 × 480px columns, 28px gap |
| Controls | 480px wide; selects and inputs share one measure |
| Save bar | 65px tall, fixed, translucent surface + 14px blur, buttons 154/145px |
| Duplicate heading | core's `<h2>` hidden; the page header's `<h1>` remains |

What the render caught:

- The first pass left the buttons at the leading edge of the bar: core gives
  `[data-groupname="buttonar"]` a `width: 100%`, so the group filled the row and
  its inner flex row started at the left. The group now shrinks to its content
  and the content box is `justify-content-end`.
- `#sticky-footer` carries `bg-white`, which is a dark grey in the dark palette.
  The bar takes `--nl-header-translucent` with `!important` instead.
- The page prints the user's name twice; the second `<h2>` is a direct child of
  `[role="main"]`, not the first child (a notification span precedes it), so the
  selector must not use `:first-child`.

Full-width fields are matched with `:has(textarea)`, `:has(.editor)`,
`:has(.filemanager)` and `:has(.form-autocomplete)` rather than field ids, so a
site that enables extra profile fields still lays out correctly.

## Course index drawer

Measured on `/course/view.php?id=2` with the drawer open, at 1440×900
(viewport height 814px in the headless browser):

| Check | Result |
| --- | --- |
| Panel rect | 285 × 814px at x=288, y=0 |
| `top` / `height` | `0px` / `100vh` (`814px`) |
| `border-radius` | `0px` (was `16px`) |
| Surface | `rgb(255, 255, 255)` (was `rgb(233, 236, 239)`) |
| Header band | `rgb(251, 250, 246)`, 1px `rgb(222, 217, 207)` bottom rule |
| Page seam | 1px `rgb(222, 217, 207)`, painted only while `.show` |
| Header padding | 72px (`--nl-navbar-height`) |
| Stacking | navbar `1030`, drawer `2` — the navbar stays above the panel |
| Done marker | 8 × 8px, `rgb(46, 111, 78)` |
| To-do marker | 8 × 8px, 1px `rgb(143, 138, 128)` ring, transparent centre |
| Failed marker | 8 × 8px filled in the core danger colour |
| Selected item | 14% accent tint, 1px 36% accent border, link `rgb(11, 20, 18)` |
| Selected item contrast | 15.8:1 (ink on the tint) |
| Selected item hover | 18% tint `rgb(252, 226, 209)`, 45% border, label stays ink (15.1:1) |
| Selected item hover on the link | `rgb(157, 66, 4)`, 5.3:1 — no longer core's `#011734` / `#e6e6e6` |
| Selected item focus-within | Same 18% tint as hover |
| Before 1.22.1 | 8% accent wash with a white link — effectively 1:1 |
| Before 1.22.2 | Hover and focus reverted to core's navy `#011734` with `#e6e6e6` text |
| Section page | The same pill on `.courseindex-section-title.pageitem` |
| Narrow (1100px) | Unchanged card: top 108px, 541px tall, 8.8px radius |
| Closed | `border-inline-end: 0px`, no stray seam |

Dark mode resolves the panel to `rgb(21, 29, 26)`, the header to
`rgb(25, 34, 31)`, the done marker to `rgb(140, 176, 158)` and the to-do ring to
`rgb(107, 118, 113)`. The selected pill hovers to `rgb(61, 52, 39)` with a white
label (12.2:1) and a `rgb(249, 192, 155)` label on direct hover (7.6:1). The
marker is centred with `inset: 0; margin: auto`, so it
does not depend on direction; the seam uses `border-inline-end` and mirrors in
RTL.

## Course layout width

Measured at 1440×900 on `/course/view.php?id=2` and
`/mod/page/view.php?id=2`, course index closed (page area 288–1428, 1140px):

| Check | Result |
| --- | --- |
| `#page.drawers .main-inner` | 1140px at x=288, `max-width: 1240px` (was 830px) |
| `#page-header` | 1044px at x=336 (was 734px at 491) |
| `.activity-header` | 1044px at x=336 (was 830px at 443) |
| `.generalbox` | 1044px at x=336 (was 734px at 491) |
| `.no-overflow` | 970px at x=373 |
| Course section card | 1044px at x=336 |
| Sticky bar `.sticky-footer-content` | 1044px at x=336, aligned with the card |

With the course index open the page area shrinks to 573–1428 (855px) and every
one of those blocks moves to 621–1380 (759px) together, including the
previous/next bar. Below 1200px the rail is gone, the bar keeps core's centred
830px measure, and the container still uses the full page area.

## Colour themes

Each palette was resolved through `theme_nadine_get_accent_color()` and
`theme_nadine_get_primary_color()` with a stubbed `theme_config`, then
measured. Ratios are WCAG 2.1, computed from relative luminance; the dark accent
is `mix(accent, #fff, 38%)`, exactly what the compiler receives.

| Theme | White on accent | Accent on canvas | White on primary | Dark accent on `#07110d` |
| --- | --- | --- | --- | --- |
| Nadine | 3.35:1 | 3.05:1 | 14.59:1 | 8.90:1 |
| Forest | 5.04:1 | 4.58:1 | 12.61:1 | 7.63:1 |
| Ocean | 5.36:1 | 4.87:1 | 13.96:1 | 7.30:1 |
| Plum | 7.03:1 | 6.39:1 | 17.15:1 | 6.34:1 |
| Clay | 5.44:1 | 4.95:1 | 14.65:1 | 7.16:1 |
| Slate | 6.22:1 | 5.66:1 | 15.60:1 | 6.92:1 |
| Rose | 5.75:1 | 5.24:1 | 17.01:1 | 6.82:1 |
| Amber | 4.51:1 | 4.10:1 | 13.78:1 | 8.05:1 |

Nadine's 3.35:1 is the palette the theme shipped with and is the floor the
other seven beat; its accent carries large text, icons and fills, with body copy
on `--nl-ink`. A preset is only added when white on the accent clears 4.5:1.

Resolution checks, all passing: `default` and an unknown key both resolve to
`#ef5e00`/`#01275a`; `custom` reads the pickers; `custom` with empty pickers
falls back to the default accent, and an empty primary follows the accent.

The setting is `theme_nadine/colortheme`. The two colour pickers are hidden
unless `Custom` is selected, via `$settings->hide_if('…/accentcolor',
'…/colortheme', 'eq', 'custom')` on the tab container — the condition has to sit
on the container because `theme_boost_admin_settingspage_tabs::add_tab()` copies
a tab's settings but not its dependencies, so a condition set on the tab never
reaches the page JavaScript.

The control is a grid of nine swatch cards rendered by
`theme_nadine\setting\colourtheme`, a `configselect` subclass. Verified on the
served settings page:

| Check | Result |
| --- | --- |
| Cards rendered | 9 labels, one per option, each with a `--nl-ct-a`/`--nl-ct-b` strip |
| Native control | `<input type="radio" name="s_theme_nadine_colortheme">`, the stored value checked |
| Group semantics | `role="radiogroup"`, `aria-label="Colour theme"`, cards are `<label>`s |
| Title markup | `<p>` not `<label for>`, via `has_custom_form_control()` |
| Dependency payload | `colortheme eq custom → accentcolor, primarycolor` |

The selected card is marked by a 1px ring in its own accent **and** a tick, so the
state does not rest on colour alone. The tick icon is `pix/lucide/check.svg`,
added for this control.

## Loading indicator

The `core/loading` template override was rendered through Moodle's template
engine and confirmed on the served pages:

| Check | Result |
| --- | --- |
| Override resolved | `$OUTPUT->render_from_template('core/loading', …)` returns `.nl-spinner`, not `i/loading` |
| Markup on a real page | 31–32 instances on `/my/` and `/course/view.php?id=2`, all with the `nl-spinner` child |
| Visible at rest | 0 visible loading icons on the dashboard (hidden placeholders stay hidden) |
| Inline size | 18.4 × 18.4px (1.15em at 16px), ring 2.24px |
| Overlay size | 40 × 40px (2.5em) |
| Fullscreen size | 64 × 64px (4em) |
| Notification header | 12.8 × 12.8px, inside core's 12px slot |
| Light accent | `#ef5e00` |
| Dark accent | `#f59b61` |
| Button ring | `currentColor` gradient, so it inverts with the label |

### Legacy spinners and the notification card (1.24.0)

Checked with Firefox (marionette) on `/my/`, `/my/courses.php` and
`/report/status/index.php` at 1440×900, signed in as Admin, light and dark.

| Case | Expected | Result |
| --- | --- | --- |
| Notification card height | Hug its content, not core's 500px | Pass — 246px with the empty state: header 57, body 140, footer 46 |
| Popover header | Title and actions share one baseline | Pass — `display: flex`, 57px tall, 12.8/13.6/12.8/16px padding |
| Body cap | 22rem, scrolls past it | Pass — `max-height: 352px` |
| Mark-all-read glyph | One check, no unmasked square | Pass — `::before` computes `content: none` |
| Header button focus | Theme ring, not Firefox's black ring | Pass — `outline: 2px solid` the active accent, offset 2px |
| Footer strip, dark mode | Card surface, not core's white | Pass — `background: var(--nl-surface)`, no core hairline |
| Administration branch, rest | Lucide caret, no animation | Pass — `mask-image` chevron-right, `animation-name: none` |
| Administration branch, loading | Caret chip becomes the ring | Pass — `p.loading` background `none`; `::before` 20×20, `border-radius: 50%`, conic gradient, radial mask, `nl-spin` |
| `pix_icon('i/loading')` | Ring, glyph suppressed | Pass — `i.icon.fa-spinner` 18.4×18.4, conic gradient, radius 50%, `::before` `none` |

### Notification bell and notifications page (1.25.0–1.25.1)

Checked with Firefox (marionette) at 1440×1000, signed in as Admin for the
navbar and as teacher1 for the page (the account has one notification).

| Case | Expected | Result |
| --- | --- | --- |
| Bell toggle | Sits on the navbar baseline, not below it | Pass — toggle 35.2×71 at `y=0`; `.icon` 19.2×19.2 masked with the Lucide bell, centre 35.5px, same as the user menu |
| Pointer triangle | Core's `::before` and `::after` removed | Pass — both `display: none` |
| Notification row | Stays inside the card | Pass — 369.2×62.7 at 0.4rem margins; right edge 7.4px inside the card |
| "View full notification" | Aligned with the message, not clipped | Pass — link right edge 885.4px, same as the message body |
| Popover drop | Starts at the header's border | Pass — container `top: 72px`, navbar bottom 72 |
| Page card | One surface, not a plain box | Pass — 1044×640, `border-radius: 16px`, `1px solid #ded9cf`, grid `320px 722px` |
| Rail | Warm surface with padded rows | Pass — `#fbfaf6`, row 319×67 with 12.8/16px padding |
| Unread row | Dot plus tint | Pass — 6.7px accent dot hanging outside the text, 6% accent tint |
| Selected row | Surface plus accent edge | Pass — `inset 3px 0 0` the accent, no focus ring on the opened row |
| Detail pane | Header band, body, footer | Pass — header 74px, body 513px at 20px padding, footer 51px |
| Empty pane | Centred bell and message | Pass — flex-centred, muted, 2rem bell |
| Below 980px | Core's sliding panes | Pass — block layout at a 500px viewport, control full width, no horizontal overflow |

### User menu icon chips (1.26.0)

Checked with Firefox (marionette) at 1440×900, signed in as Admin.

| Case | Expected | Result |
| --- | --- | --- |
| Chip | 1.6rem tinted square behind every glyph | Pass — 25.6×25.6, `border-radius: 7.2px`, fill `rgba(14,116,144,0.11)` (11% of the site accent) |
| Glyph | Accent, 1.1rem, centred on the chip | Pass — 17.6×17.6 at `rgb(14,116,144)`, Lucide mask per destination |
| Hover | Chip deepens to 18%, glyph to accent-strong | Pass — `rgba(14,116,144,0.18)` and `rgb(12,100,124)` |
| Label | Starts after the chip, never under it | Pass — `padding-inline-start: 2.8rem` (44.8px) |
| Fallback item | Unmatched path keeps the dot | Pass — dot mask 0.5rem on the same chip |
| Dark mode | Panel, chip and glyph follow the dark tokens | Pass — panel `rgb(21,29,26)`, glyph `rgb(106,169,186)`, label `rgb(238,243,240)` |

### Appearance segments (1.28.0)

Checked with Firefox (marionette) at 1440×900, signed in as Admin, clicking
System → Dark → Auto → Light.

| Case | Expected | Result |
| --- | --- | --- |
| Track | One compact pill in the navbar row | Pass — 63.7×24 at `y=23.5`, centred in the 72px bar, `border-radius: 999px`, fill `color-mix(ink 8%, surface)` |
| Segments | Three equal hit areas | Pass — 18.9×18.9 each at 19.9px pitch, `border-radius: 999px` |
| Active chip | Raised surface, accent glyph | Pass — `#fff` in light, `color-mix(ink 14%, surface)` in dark, glyph `rgb(14,116,144)` / `rgb(106,169,186)` |
| Glyphs | Sun, moon, contrast | Pass — 12.17px masks per `data-colourmode`; inactive `rgb(123,133,128)` |
| One click | Sets the mode without a menu | Pass — `data-colourmode` and `data-bs-theme` update, `aria-checked` moves, no dropdown in the DOM |
| Labels | Named for assistive tech | Pass — `aria-label` and `title` "Light" / "Dark" / "System"; hidden `colourmode-current-label` follows |
| Template | Core's module still drives it | Pass — `theme_boost/colourmode` reads the same `set-colourmode` options and updates the hidden icon |

### Header hairlines (1.28.1)

| Case | Expected | Result |
| --- | --- | --- |
| Height | Shorter than the bar | Pass — 36px in a 72px bar, centred at `y=17.5` |
| Ends | Fade to transparent | Pass — `linear-gradient(transparent, border 22%, border 78%, transparent)` |
| Border | No hard 1px rule left | Pass — `border: 0`, `width: 1px` |
| Fill | No grey behind the gradient | Pass — `background-color: transparent` overrides core's `--bs-gray-300` |
| Dark mode | Follows the dark border token | Pass — `rgb(42,53,49)` |

### Course cards and link focus (1.28.2–1.28.5)

Checked with Firefox (marionette) at 1440×900 as Admin, on the front page and
on `/course/view.php?id=2`, toggling Light and Dark.

| Case | Expected | Result |
| --- | --- | --- |
| Card border | Follows the mode, not the compiled `#d9d4ca` | Pass — light `rgb(222,217,207)`, dark `rgb(42,53,49)` on six cards |
| Card radius | 0.7rem on catalogue cards | Pass — 11.2px on `#frontpage-*` and search results |
| Card arrow | Dark glyph on its always-white disc | Pass — `rgb(11,20,18)` in both modes, disc unchanged |
| Divider fill | No grey stub behind the gradient | Pass — `background-color: rgba(255,255,255,0)` |
| Link focus | Text colour only | Pass — `.aalink` and `a:not([class])` report the strong accent, no fill, no bars, no outline |
| Focus, dark | Follows the dark accent | Pass — `rgb(215,153,169)` |

### Course management titles (1.28.6)

Checked with Firefox (marionette) at 1440×900 as Admin on
`/course/management.php?categoryid=1&courseid=2`, in Light and Dark.

| Case | Expected | Result |
| --- | --- | --- |
| Title size | 1.3rem on all three columns | Pass — 20.8px on `#category-listing-title`, `#course-listing-title`, `#course-detail-title` |
| Weight and tracking | List label, not page heading | Pass — 700, line height 27.03px, `-0.2496px` |
| Cap inset | 0.9rem × 1.15rem | Pass — `14.4px 18.4px`, margin 0 |
| Accent tick | 3px pill before each title | Pass — 3.52×21.83px, radius 999px, `rgb(178,58,91)` / `rgb(207,133,153)` |
| Cap rule | One hairline under each cap | Pass — `1px solid rgb(222,217,207)` / `rgb(42,53,49)`, fill transparent |
| Detail title | Aligned to the cap inset | Pass — padding 0 inside the cap, tick present |

### Select type size (1.28.7)

| Case | Expected | Result |
| --- | --- | --- |
| `.form-select` | 0.875rem, matching the small control size | Pass — 14px computed on the management page selects and the site-wide field |

### Brand logo (1.29.1)

Checked with Firefox at 1440×900 on `/`, `/login/index.php` and the footer, in
Light and Dark.

| Case | Expected | Result |
| --- | --- | --- |
| Source | Theme ships the mark | Pass — `.nl-brand__mark` resolves to `theme/image.php/nadine/theme_nadine/<rev>/logo` |
| Size | 34px header and login, 38px footer | Pass — 34×34 and 38×38, `object-fit: contain` |
| Colours | Orange gradient on a 10% wash | Pass — `#FFB46B` → `#F97316` → `#D2470C`; plate `#F97316` at 0.12 → 0.04 alpha |
| Dark mode | Reads on the dark bar | Pass — plate reads as a warm square, monogram keeps its gradient |
| Upload override | An uploaded logo still wins | Pass — `theme_nadine_get_logo_url()` is checked first |
| Alt text | Decorative, name follows | Pass — `alt=""`, `aria-hidden="true"`, site name in `.nl-brand__name` |

### Edit mode switch (1.29.2)

Checked with Firefox at 1440×900 as Admin on `/course/view.php?id=2`, reading
the switch in both modes with the input set to `:checked`.

| Case | Expected | Result |
| --- | --- | --- |
| Checked track, light | Theme accent | Pass — `rgb(239,94,0)`, the same value as `--nl-accent` |
| Checked track, dark | Dark accent | Pass — `rgb(245,155,97)` |
| Thumb | Contrasts the track | Pass — `#fff` in light, `rgb(7,17,13)` in dark |
| Hover | Accent-strong | Pass — `--mds-bg-interactive-primary-hover` resolves to `#ce5100` |
| Unchecked | Core's neutral track | Pass — `rgb(222,226,230)` track, `rgb(73,80,87)` thumb |
| Label | Unchanged ink | Pass — `rgb(29,33,37)`, no recolour |

### Course management panes (1.29.3)

Checked with Firefox at 1440×900 as Admin on
`/course/management.php?categoryid=1&courseid=2`, reading both listing panes
and the open action menu of the first category row.

| Case | Expected | Result |
| --- | --- | --- |
| Pane body text | 0.95rem in both panes | Pass — 15.2px on `.card-body`, the category and course names, the counts and the pagination totals |
| Bulk label wrapping | One line, no mid-word break | Pass — "Move selected courses to…" is 24.6px tall (a single line), "Move selected categories to" the same |
| Label above control | The control starts on the next line | Pass — value top sits 4.8px under the label bottom |
| Bulk select width | Fills the pane beside its button | Pass — 385.8px (courses) and 370.6px (categories), button 75px |
| Sorting selects | Full width, not clipped | Pass — 452.8px each, down from 172.9px |
| Bulk band | Hairline above the row | Pass — `1px solid rgb(222,217,207)`, 0.85rem of padding above |
| Menu panel | Surface fill, hairline, 12px radius | Pass — `rgb(255,255,255)` light / `rgb(21,29,26)` dark, `1px` `rgb(222,217,207)` / `rgb(42,53,49)` |
| Menu item | 0.9rem label, 8px radius | Pass — 14.4px, radius 8px, padding 6.72px × 8px, gap 9.6px |
| Menu chip | 1.7rem accent chip behind each glyph | Pass — 27.2 × 27.2px, radius 8px, `color-mix(accent 11%)`, glyph in `rgb(239,94,0)` light / `rgb(245,155,97)` dark |
| Delete chip | Danger tint, legible in both modes | Pass — `rgb(163,58,58)` glyph on a 12% chip in light, a lightened mix in dark |
| Narrow viewport | No horizontal overflow at 390px | Pass — panes stack to full width, labels stay on one line |

### Course page sections and menus (1.29.4)

Checked with Firefox at 1440×900 as Admin with editing on, on
`/course/view.php?id=4`.

| Case | Expected | Result |
| --- | --- | --- |
| Section card border | None | Pass — `border-top-width: 0px`, `border-style: none` |
| Section header rule | No bottom border | Pass — `0px none` |
| Header corners | Top only, matching the card radius | Pass — `16px 16px 0px 0px` |
| Section overflow | Visible | Pass — `overflow: visible`, so menus escape the card |
| Add content menu | Opens and stays visible | Pass — panel `208×96`, hit test on the item returns `section-modchooser`; the divider band is forced visible while `:has(.dropdown-menu.show)` |
| Section action menu | Opens past the card edge | Pass — the nine-item menu opens upward (`381px`, bottom `710`) beyond the section top and its last item, Delete, is hit-testable |
| Menu panel | Surface, hairline, 12px radius | Pass — `rgb(255,255,255)` light / `rgb(21,29,26)` dark |
| Menu item | 0.9rem label, 8px radius | Pass — 14.4px, radius 8px |
| Icon chips | 1.7rem square, one hue per action | Pass — 27×27px; View `#2f6fd0`, Edit settings `#a8641a`, Highlight `#b03a72`, Duplicate `#6b52c4`, Hide `#6b7280`, Move `#2f8f83`, Assign roles `#3f7d4e`, Group mode `#4a5bb5`, Permalink `#5f7d95`, Delete `#a33a3a`, Activity or resource the accent, Subsection `#6b52c4` |
| Dark mode | Lifted tones on the dark panel | Pass — View `#93bcf2`, Hide `#aab4c0`, Delete `rgb(214,166,166)` |

### Desktop `h3` step (1.29.5)

Bootstrap scales `h3` with the viewport (`calc(1.3rem + 0.6vw)`) and pins it at
`1.75rem` from 1200 px up. In the theme's display face that step runs wide, so
the theme pins it at `1.65rem` instead. Measured in Firefox on `/my/` by
appending a bare `<h3>` to the body, which carries no component class:

| Window | `innerWidth` | Computed `font-size` | Expected | Result |
| --- | --- | --- | --- | --- |
| 1440 | 1440 | 26.4px | `1.65rem` | Pass |
| 1280 | 1280 | 26.4px | `1.65rem` | Pass |
| 1200 | 1200 | 26.4px | `1.65rem` — the breakpoint is inclusive | Pass |
| 1199 | 1199 | 27.9833px | `calc(1.3rem + 0.6vw)`, untouched | Pass |
| 900 | 900 | 26.2px | `calc(1.3rem + 0.6vw)` | Pass |
| 390 | 500 | 23.8px | `calc(1.3rem + 0.6vw)` at the 500 px floor | Pass |

The step is deliberately downward: at 1199 px the heading is 27.98 px and from
1200 px it settles to 26.4 px, so the desktop column never carries an oversized
`h3`.

Component headings keep their own sizes at both widths, so the global rule does
not leak into them:

| Element | 1440 px | 900 px |
| --- | --- | --- |
| `#category-listing-title`, `#course-listing-title` | 20.8px | 20.8px |
| `#sectionid-13-title` (course section) | 17.2833px | 17.2833px |
| `#instance-15-header` (block header) | 15.6833px | 15.6833px |
| `.popover-region-header-text` | 15.2px | 15.2px |

### Calendar event card rule (1.29.6)

Checked with Firefox on `/calendar/view.php?view=upcoming` as Admin. The event
card header used to carry the event hue as a 3px bar down the inline start; it
now carries it as a dashed rule along the bottom edge.

| Case | Expected | Result |
| --- | --- | --- |
| Bottom edge | `1px dashed` in the event hue | Pass — `1px dashed`, `rgb(211,70,0)` on a course event (`--nl-event: #d34600`), `rgb(78,124,145)` on a user event, `rgb(43,135,19)` on a site event |
| Other edges | No border | Pass — top, inline-start and inline-end all `0px none` |
| Widths | Spans the card at every size | Pass — `722×57` at 1440, `782×57` at 900, `420×82` at 500 |
| Header surface | Unchanged | Pass — `rgb(251,250,246)` light, `rgb(25,34,31)` dark |
| Right-to-left | Rule does not move | Pass — with `dir="rtl"` the inline edges stay `0px`, so nothing shifts with the writing direction |
| Day view | Same rule | Pass — `1px dashed` on the day view card |

### Dropdown item inset (1.29.7)

Checked with Firefox at 1440×900 as Admin, with the course page action menu
(`#action-menu-toggle-1`), the management action menu and the user menu open.
The theme insets items by 0.3rem on each side; Bootstrap's `width: 100%` was
measured against the menu's content box and ignored those margins, so every item
hung 0.3rem past the right padding.

| Case | Before | After |
| --- | --- | --- |
| Course menu item width | 193.2px | 183.6px |
| Course menu gaps (left / right) | 12.2px / 2.6px | 12.2px / 12.2px |
| Course menu `scrollWidth` vs `clientWidth` | 216 vs 206 | 206 vs 206 |
| Management menu item | 226.6px | 217px |
| Management menu gaps | 12.2px / 2.6px | 12.2px / 12.2px |
| Management `scrollWidth` vs `clientWidth` | 249 vs 239 | 239 vs 239 |
| User menu item | 249.2px | 239.6px |
| User menu gaps | 12.2px / 2.6px | 12.2px / 12.2px |
| At 500px | — | Same 183.6px item, 12.2px both sides |

The before column was reproduced by re-injecting `.dropdown-item{width:100%}` at
the end of the cascade, so both states come from the same page and the same
fonts.

`width: auto` is what restores the symmetry: the inline margins come out of the
width instead of being added to it, so the hover chip and the focus ring sit
evenly inside the panel.

### Icon set (1.29.8)

Checked with Firefox at 1440×900 and with `curl` against the running site. The
theme previously shipped a single 32,038-byte `pix/favicon.ico`; it now ships the
full set and links each file from the head.

| Case | Expected | Result |
| --- | --- | --- |
| Head links | Shortcut icon plus SVG, 96px PNG, Apple touch icon and manifest | Pass — core's `rel="shortcut icon"` and four appended links |
| Every layout | Same links on front page, login, dashboard, course page and admin | Pass — four appended links on each |
| `favicon.ico` | The new 48/32/16 file | Pass — `image.php` serves 15,086 bytes, byte-identical to the packaged file |
| `favicon.svg` | `image/svg+xml`, decodes | Pass — 200, `image/svg+xml`, intrinsic 500×500 |
| `favicon-96x96.png` | `image/png`, 96×96 | Pass |
| `apple-touch-icon.png` | `image/png`, 180×180 | Pass |
| `web-app-manifest-*.png` | `image/png`, 192×192 and 512×512 | Pass |
| `site.webmanifest` | `application/manifest+json` | Pass — Apache serves the extension correctly |
| Manifest body | Parses, names the app, lists both icons | Pass — `Nadine Academy` / `Nadine`, 192 and 512 |
| Browser fetch | Firefox requests the SVG and the manifest | Pass — both appear in the resource timeline, and it fetches the 192px manifest icon |
| Custom favicon | Uploaded favicon suppresses the packaged set | Pass — with `theme_nadine/favicon` set the four links disappear; clearing it restores them |

### Marketplace readiness (1.29.9)

Four gaps a plugin-directory reviewer would raise, and what was done about
each. The privacy and uninstall checks ran as a CLI script against the live
site; the icon was rasterised by Firefox and compared with the bitmap it
replaced.

| Case | Expected | Result |
| --- | --- | --- |
| Lucide attribution | ISC notice shipped with the glyphs | Pass — `pix/lucide/LICENSE` already carried it; the README now points at it instead of calling the set the theme's own |
| Privacy metadata | `drawer-open-index` declared | Pass — `theme_nadine\privacy\provider` resolves via `manager::get_provider_classname_for_component()`, implements both `metadata\provider` and `user_preference_provider`, and `get_metadata()` returns the preference with a resolved string |
| Uninstall, parent configured | Boost's values restored | Pass — `0`/`auto` → theme syncs `1`/`dark` → uninstall returns `0`/`auto` |
| Uninstall, parent unset | The key is removed again | Pass — `1`/unset → theme syncs `1`/`light` → uninstall leaves `1`/unset |
| Snapshot timing | A second save does not overwrite the snapshot | Pass — `theme_nadine_backup_colour_modes()` returns early once `colourmodesbackup` exists |
| `favicon.svg` | A real vector, not a wrapped bitmap | Pass — 1,221 bytes (was 119,957), `viewBox` only, no fixed width |
| Vector fidelity | Same mark as the logo and the old bitmap | Pass — Firefox renders the same N and gradient at 192px; at 32px and 16px the counter and the diagonal stay legible |
| Served icon | Static file, correct type | Pass — 200, `image/svg+xml`, 1,221 bytes, byte-identical to the packaged file, `?rev=` bumped to the new theme revision |
| Arabic name | Not the pre-rename brand | Pass — `pluginname` reads `نادين`, not `نورثلايت` |
| Code checker | No errors against the Moodle standard | Pass — `moodlehq/moodle-cs` 3.9 with PHP_CodeSniffer: **0 errors, 7 warnings** over 4,884 lines of PHP |

The code checker was installed on its own (`composer require --dev
moodlehq/moodle-cs`) so the site did not gain a plugin, and run as
`phpcs --standard=moodle --extensions=php .`. The first pass reported 18 errors
and 42 warnings; `phpcbf` fixed 48 of them, all whitespace, brace and
multi-line-call layout. The seven that remain are the `MOODLE_INTERNAL` notice
on the three `db/` files and `lib.php` — which is how core's own `db/` files are
written — and the "expected login check" notice on the three AJAX endpoints.
Each of those guards itself: `search.php` runs `require_sesskey()` and only
demands a login when `$CFG->forcelogin` is set, so the panel works for guests;
`drawer.php` and `rail.php` call `require_sesskey()` and then return 403 unless
the user is logged in and not a guest. Re-run at 1.29.19 after the avatar and
screenshot changes, with the same result.

`ImageMagick` renders any `url(#gradient)` fill as black, so its output is not a
usable check for this file; the Firefox screenshot is.

### Unit tests (1.29.10)

`tests/lib_test.php` covers the pure helpers: the colour maths, the eight
palettes, the contrast helpers, the icon loader and the SCSS prelude. 21 test
methods.

| Area | Cases |
| --- | --- |
| `theme_nadine_mix_color()` | both endpoints, midpoint rounding, weight clamping at -40 and 400, bare hex without `#`, output format |
| `theme_nadine_get_color_themes()` | eight palettes, every pair complete and well-formed, primary at 7:1 or better on white, accent at 3:1 or better |
| `theme_nadine_action_color()` | 4.5:1 against both white and the canvas for all eight palettes, passing accents returned unchanged, bright accents darkened |
| `theme_nadine_contrast_ratio()` | reference values: black/white 21, identical colours 1, the brand orange on white 3.35 |
| `theme_nadine_icon()` | markup, classes, size clamping, unknown glyph, path traversal, social set, unknown set fallback |
| `theme_nadine_get_pre_scss()` | accent, primary, type stack and heading weight reach the compiled SCSS |
| `theme_nadine_get_accent_color()` | a custom accent is used, an invalid one falls back to `#ef5e00` |

PHPUnit is not installed on this development instance — the Moodle root has no
`vendor/`, and core's `require-dev` would need a full composer install plus a
test database. The same assertions were therefore run through a CLI harness
against `lib.php`, and every one passes. On a CI or test site:

```
php admin/tool/phpunit/cli/init.php
vendor/bin/phpunit --testsuite theme_nadine_testsuite
```

### User preferences layout (1.29.11)

Measured with a same-origin iframe harness at 1440×760, before and after, so
both states come from the same browser and the same stylesheet build.

| Element | Before | After |
| --- | --- | --- |
| `#page.drawers .main-inner` | 830px wide (Boost's `limitedwidth` cap) | **1140px**, capped by the theme's 1240px |
| `#region-main` | 734px | **1044px** |
| Group columns | 3 × 253px, fourth wraps under the first | 2 balanced columns, **512px** cards |
| Row display | `flex` | `block` with `columns: 2` |
| Content used | 64% of the available area | 92% |

| Case | Expected | Result |
| --- | --- | --- |
| 1440 | Two columns, full width | Pass — 512px cards, 20px gap |
| 900 | Two columns | Pass — the longest label still fits on one line |
| 500 | One column | Pass — full-width cards, no horizontal overflow |
| Card chrome | Hairline border, 16px radius, soft shadow | Pass — matches the theme's other cards |
| Link rows | Full-width target, wash and chevron on hover | Pass — chevron mask resolves to `image.php/nadine/theme/…/lucide/chevron-right` |
| RTL | Chevron mirrors and enters from the other edge | Pass — `scaleX(-1)` plus a positive entry offset |

The groups differ in length by a factor of four, so CSS columns are used rather
than a grid: a grid would stretch the short cards to the height of "User
account" and leave most of each one empty. The residual imbalance — the long
group alone on the left, the three short ones stacked on the right — is the
best split available for 12 + 4 + 3 + 3 links.

### Navigation rail states and dialogues (1.29.12)

The rail now has two widths instead of two positions. At 1440px, signed in:

| Case | Expected | Result |
| --- | --- | --- |
| Expanded (default) | 18rem column, labels visible | Pass — unchanged from 1.29.11 |
| Collapsed | **4.9rem** icon strip, glyphs centred | Pass — `#page-wrapper` padding and `.nl-navbar` inset both follow |
| Labels when collapsed | Clipped, not removed | Pass — `clip: rect(0,0,0,0)` keeps every icon's accessible name |
| Tooltips | `title` on every rail link and the user card | Pass |
| Seam | Follows the rail to the new width | Pass — no longer fades out |
| Transition | Rail width, gutter, bar inset animate together | Pass — shared `0.32s cubic-bezier(.2,.7,.2,1)` |
| Persistence | Choice survives the next page load | Pass — `navrail-collapsed` written by `ajax/rail.php` |

`ajax/rail.php` rejects `GET` with `405`, requires a sesskey, and refuses guests
with `403`, matching `ajax/drawer.php`. Both preferences are declared and
exported by the privacy provider; `export_user_preferences()` runs clean.

The dialogue shell was verified against the compiled stylesheet with a static
fixture: `.moodle-dialogue` clips to `0.7rem`, the wrapper inherits the radius so
no square hairline shows inside, and the header resolves to an 8% accent tint
over the surface colour. Fullscreen dialogues are excluded so the file picker
keeps its own scrolling.

### Course card progress rings (1.29.13)

Rendered the served bundle against the card markup at 0, 25, 45, 75 and 100, in
light and dark, and read the geometry:

| Case | Expected | Result |
| --- | --- | --- |
| Course card ring | 46.4×46.4, 3.84px stroke | Pass — `2.9rem` with a `0.24rem` cut-out |
| Dashboard ring | Same element | Pass — 46.4×46.4 |
| Arc | Accent at the value | Pass — `conic-gradient(rgb(239, 94, 0) 45%, …)` |
| Figure placement | Inside the ring, above the cut-out | Pass — 21.6×11.2 at 45%, 8.5px clearance each side |
| Three digits | `100%` still clears the hole | Pass — 38.72px hole, ~25.7px of text |
| Row gap | 9.6px between ring and caption | Pass |
| No progress | Ring omitted entirely | Pass — cards without completion render no row |
| RTL | Ring on the right, copy to its left, both trailing-aligned | Pass — flex row reverses on `dir="rtl"` |
| Dark | Cut-out follows `--nl-surface` | Pass — no white disc on the dark card |
| Reduced motion | Nothing animates | Pass — the ring is static; the old bar keyframes are gone |

The ring, the dashboard ring and the 7rem hero ring all come from one
`nl-progress-ring` mixin, so their shape cannot diverge. Contrast is covered by
the palette assertions in `tests/lib_test.php`: `--nl-accent` clears 3:1 against
the surface for every shipped palette, which the removed `--nl-progress-fill`
did not (2.44:1 on white).

### Ring figure (1.29.16)

The figure moved from beside the ring to inside it, so both card rings grew from
2.6rem to 2.9rem to keep three digits off the arc. Re-measured against the served
bundle: ring 46.4×46.4, cut-out inset 3.84px (38.72px hole), figure 21.6×11.2 at
45%, "%" at 8.32px in `--nl-muted`, row gap 9.6px. At 100% the text is ~25.7px
wide, leaving 6.5px of clearance on each side. The hero ring already worked this
way, so all three now read the same.

### Welcome and rail initials (1.29.17; retina source 1.29.18–1.29.19)

Core's `.userinitials` carries a width and height only for 16/30/35/50/64/100, so
the welcome band's `size-72` and the rail's `size-36` both fell through to an
unsized pill: a learner with no profile picture got a grey blob instead of a
circle, and the welcome avatar was 72px next to a 64px photo.

Both states were rendered from the real `/my/` markup against the served bundle
and measured from the screenshot, not read off the CSS:

| State | Box | Top..bottom | Notes |
|---|---|---|---|
| Welcome, initials | 36×36 | y 86..121 | 0.78rem/700, `--nl-surface-muted` ground `#FBFAF6` |
| Welcome, photo | 36×36 | y 86..121 | `width="50" height="50"` overridden by CSS, serves `f1` 100×100 |
| Rail, initials | 1.9rem | — | `.size-50` outranked, matches the 1.9rem rail photo |
| Rail, photo | 1.9rem | — | `width="50" height="50"` overridden, serves `f1` |

The two welcome states share the same vertical band, so the greeting does not
shift when a learner uploads a picture. The initials measure 17.9:1 on the light
ground (`#0B1412` on `#FBFAF6`) and 16.3:1 in dark mode, well past the 4.5:1
body-text floor.

Both avatars are requested at `size => 50` but drawn smaller (36px and 1.9rem).
`user_picture` serves `f2` (35px) for every size below 50, so a 36px request
produced a 35px file stretched over 72 device pixels on a retina screen; 50 is
the smallest request that returns `f1` (100×100, 14.8 KB against 2.6 KB for
`f2`). Two things follow: the initials arrive as `.size-50`, which core sizes at
50px, so the stylesheet names `.userinitials.size-50` explicitly to outrank it
rather than depending on source order; and the `<img>` carries `width="50"
height="50"` while the CSS box stays 36px or 1.9rem. The welcome band was
re-measured after the change and the initials are still 36×36 at y 86..121.

### Theme selector card (1.29.19)

Moodle looks for `pix/screenshot.{svg,png,gif,jpg}` and does **not** accept
`.jpeg`, so the artwork ships as `pix/screenshot.jpg` (1896×1272, 171 KB). The
served URL is `theme/image.php/nadine/theme/<rev>/screenshot` → 200,
`image/jpeg`, byte-identical to the source.

Boost crops every theme screenshot into a 7rem strip. On the selector's
three-column grid at 1440px that is a 240×112 box, 2.1:1 against the artwork's
1.49:1, so `background-size: cover` threw away 30% of the height and cut the
headline through its second line. The card image is now `height: auto;
aspect-ratio: 3 / 2`, which at 240px wide is 160px tall and shows the preview
whole. The rule targets `.theme-card`, so the Boost card beside it changes too
and the two stay level; course cards, which share Boost's original rule, are
untouched.

Measured from a render of the real selector markup against the served bundle:
both card images 240×160, no crop, card bodies starting on the same line.

### Screenshot set (1.29.19)

Eleven images in `dist/screenshots/`, packaged as `dist/nadine-screenshots.zip`:
front page, course grid, catalogue, course page, login, user preferences, theme
settings, dark mode, RTL Arabic, mobile and the dashboard. All are 1440×900
except the phone shot at 500×1000.

They are captured through Marionette rather than Playwright, because
`playwright install firefox` fails to download on this box and seeding
`cookies.sqlite` by hand does not work — Firefox drops the row on start. The
working route is a small Marionette client that logs in through the real form
and drives the browser directly:

- Marionette speaks protocol 3: length-prefixed JSON **arrays**, `[0, id, name,
  params]` in and `[1, id, error, result]` out. The server's hello frame is read
  the same way.
- `WebDriver:SetWindowRect` clamps to a 500px minimum width, and its height
  includes 86px of browser chrome on this build, so a 900px viewport is a 986px
  window.
- Every step waits on a condition, not a sleep: the login waits for the path to
  leave `/login/index.php`, and each capture waits for `readyState` plus a
  changed `location.href`. With fixed sleeps the login POST was still in flight
  when the next page loaded, which produced a "Your session has timed out"
  screen and duplicate images.
- Dark mode is captured by setting `theme_boost/defaultcolourmode` to `dark`,
  shooting, and restoring it to `auto`; the compiled CSS does not depend on the
  colour mode, so no rebuild is needed. RTL uses `?lang=ar` on the front page.

### Login panel scrim and dark mode (1.29.14)

The panel is 714×840 at a 1440×900 viewport. Backgrounds were sampled from the
rendered page in 30×14px strips beside each text run — same height, no glyphs —
and the worst pixel in each strip was used, not the mean:

| Element | Light ground | Ratio | Dark ground | Ratio | Need |
| --- | --- | --- | --- | --- | --- |
| Eyebrow (11.5px bold) | `(246, 229, 210)` | **4.95** | `(45, 43, 35)` | **6.47** | 4.5 |
| Title (41.6px bold) | `(248, 241, 229)` | 15.55 | `(23, 30, 26)` | 16.79 | 3.0 |
| Intro (15.5px) | `(247, 242, 231)` | 6.72 | `(21, 29, 26)` | 7.81 | 4.5 |
| Point labels (13.3px 600) | `(248, 243, 232)` | 15.77 | `(21, 29, 26)` | 17.00 | 4.5 |
| Note (12.5px) | `(248, 242, 232)` | 5.04 | `(21, 29, 26)` | 7.83 | 4.5 |

Two of these were failures before this release. The eyebrow was the brand orange
on paper — **2.98:1** — and is now `--nl-accent-action-strong`; the action step
alone still fell short at 4.43, so it takes the strong one. The note was the ink
at 50% alpha, **3.28:1**, and is now `--nl-muted` (5.04). Both are small text, so
4.5:1 applies rather than the 3:1 a decorative accent gets.

The scrim is an ellipse anchored at the top-left; the ground under the copy is
within two levels of the paper itself in both modes, so the illustration opening
up below it costs nothing in legibility. Dark mode was checked on the real page
with `theme_boost/defaultcolourmode` temporarily set to `dark`, then restored to
`auto`.

## Accessibility checks


Automated structural audit over 12 saved pages per role (`audit.py`):

| Check | Result |
| --- | --- |
| `lang` attribute present and correct | Pass on every page (`en`, `ar`) |
| `dir` attribute correct | Pass (`ltr` in English, `rtl` in Arabic) |
| Exactly one `h1` per page | Pass on all pages, including login and the "already logged in" variant |
| No unresolved `[[stringname]]` placeholders | Pass — every `get_string(…, 'theme_nadine')` key exists, and the English and Arabic files define the same 158 strings |
| Focus ring on unclassed links | Pass — footer link and admin index chip show a 2 px accent outline with no core blue fill and no bars; `boxShadow` computes to `none` in both |
| Skip link to main content | Pass |
| `#region-main` landmark target | Pass |
| Images without `alt` | None |
| Unlabelled form controls | None |
| PHP error text in output | None |

The login page originally had two `h1` elements (the branding panel and the
login form). The panel heading was demoted to `h2`; the page now has one `h1`.

The theme also replaces core's skip link, which targets an empty span at the top
of the body, with one that targets the main landmark. Core's duplicate link is
removed in `core_renderer::render_skip_links()`; the rendered page now contains
exactly one "Skip to main content" link.

### Contrast ratios

Calculated from the shipped token values using the WCAG 2.2 relative luminance
formula.

| Pair | Ratio | Level |
| --- | --- | --- |
| Light: body text on canvas | 15.21:1 | AAA |
| Light: body text on surface | 16.72:1 | AAA |
| Light: secondary text on canvas | 5.11:1 | AA |
| Light: secondary text on surface | 5.61:1 | AA |
| Light: accent label on canvas | 4.53:1 | AA |
| Light: white on accent button | 4.97:1 | AA |
| Light: white on accent button hover | 6.10:1 | AA |
| Light: form control border on surface | 3.43:1 | Passes 1.4.11 |
| Light: pressed outline button border on its fill | 5.54:1 | AA |
| Dark: body text on canvas | 16.46:1 | AAA |
| Dark: secondary text on canvas | 8.51:1 | AAA |
| Dark: accent label on canvas | 8.57:1 | AAA |
| Dark: ink on accent button | 8.90:1 | AAA |

The accent rows were re-measured in 1.29.10 and four of them had been wrong:
they described the palette the theme used before the orange identity, and
claimed AAA where the shipped orange reached 3.35:1. The accent now has two
roles. `--nl-accent` keeps the brand orange for decoration — dots, borders,
washes, the course card plate — where WCAG asks 3:1 for non-text and the
orange clears it at 3.21:1. `--nl-accent-action` is the accent stepped towards
black until it clears 4.5:1 both as a fill under white text and as a label on
the canvas, and every surface that carries white text uses it: primary and
outline buttons, the secondary navigation pills, the selected admin pill,
pagination, the message send button and the notification count. For the
default palette that is `#bf4b00`; six of the eight palettes already passed and
are returned unchanged, and amber steps to `#9e6218`.

`theme_nadine_action_color()` performs that step in one-percent increments, so
a custom accent gets the same treatment without anyone having to check it by
hand. `tests/lib_test.php` asserts the 4.5:1 floor for all eight palettes.
| Dark: form control border on surface | 3.45:1 | Passes 1.4.11 |
| Dark: pressed outline button border on its fill | 5.49:1 | AA |

Decorative dividers (`--nl-border`) are intentionally low contrast (1.28:1 light,
1.45:1 dark) because they are not the only indicator of a boundary. The boundary
of every form control uses `--nl-border-control`, which does meet 3:1.

The table describes the theme's default accent (`#0d5c4d`). The accent is an
admin setting (`accentcolor`); this dev site is configured with `#EF5E00`,
whose white-on-accent ratio is 3.35:1 — sufficient for WCAG 1.4.11 UI
boundaries but below AA for normal text. Light accent colours are therefore an
administrative choice the theme documents but cannot enforce.

### Keyboard and focus

- Focus visible: 2 px accent outline with 2 px offset on every focusable
  element, verified in the compiled CSS.
- Skip link is the first focusable element and moves focus to `#region-main`.
- The password toggle is a real `<button>` with `aria-pressed` and a changing
  `aria-label`, reachable by keyboard.
- Menus, drawers, modals and the course index are Moodle core components and
  keep their core keyboard behaviour.

### Reduced motion

`@media (prefers-reduced-motion: reduce)` disables the theme's transitions and
smooth scrolling. The homepage scroll animation is not started at all: the
script returns before it arms the hidden state, so the sections are simply
present, and the stylesheet also removes the effect for a visitor who turns the
preference on after the page has loaded.

## Right-to-left

- The Arabic language pack was installed and the site rendered with
  `lang="ar" dir="rtl"`.
- The homepage, login page and course catalogue were checked: Arabic strings
  render (740, 666 and 486 Arabic characters respectively), layout direction is
  RTL, and no structural issues were found.
- All 107 theme strings exist in both `lang/en` and `lang/ar`.
- Directional icons carry `nl-directional` and are mirrored under `[dir="rtl"]`.
- The layout uses logical properties, so no separate RTL stylesheet is needed.

## Dark mode

- With **Default appearance = Dark**, the site renders with
  `data-bs-theme="dark"` on the first byte.
- With **Default appearance = System**, Moodle emits an inline script that
  resolves `prefers-color-scheme` before first paint, so there is no flash of the
  wrong theme.
- The compiled stylesheet contains 144 `data-bs-theme="dark"` selector blocks
  covering Bootstrap and Moodle components.

## Performance

Measured with `curl` against warm caches, five runs per page, median reported.

| Page | Median | Min | Max |
| --- | --- | --- | --- |
| Site home (signed in) | 0.955 s | 0.891 s | 0.993 s |
| Dashboard `/my/` | 1.326 s | 1.152 s | 1.530 s |
| Course catalogue | 0.900 s | 0.835 s | 1.130 s |
| Course page | 1.421 s | 1.211 s | 1.693 s |
| Quiz view | 1.611 s | 1.470 s | 2.225 s |
| Grade overview | 0.937 s | 0.895 s | 1.062 s |
| Login page (signed out) | 0.373 s | 0.337 s | 0.488 s |

Asset weight for the dashboard:

| Asset | Count / size |
| --- | --- |
| Stylesheets | 3 (two core YUI combos plus the theme stylesheet) |
| JavaScript files | 3 |
| Images | 8 |
| HTML | 195,588 B |
| Theme CSS | 1,610,255 B uncompressed, 264,922 B gzipped |
| Boost CSS for comparison | 1,342,328 B uncompressed, 228,496 B gzipped |

Nadine adds **267,927 bytes (20.0%)** over Boost's stylesheet, or **36,426 bytes
(15.9%)** gzipped. Of the 1.6 MB total, 83.4% is Bootstrap and Moodle's own base
styles; the theme's own block is 267,927 bytes across 1,684 rules, with no
selector emitted more than four times. It adds no network requests of its own:
no web font, no icon font, no CDN.

The earlier +2.10% figure in this file was measured before the admin area,
course page, calendar and catalogue designs landed. Re-measure after a large
visual change rather than trusting the number: `build_theme_css.php
--themes=boost` produces a like-for-like baseline.

Cold SCSS compilation is the one slow operation. On this machine a full theme
rebuild took about eight minutes; Moodle caches the result, and subsequent
requests are served from `moodledata/cache/theme`.

## Security checks

| Check | Result |
| --- | --- |
| Hard-coded credentials in the theme | None |
| Capability bypass | None — theme settings and admin pages are denied to non-admins |
| Unescaped output | All output goes through `html_writer`, `format_string` or Mustache escaping; the only `{{{ }}}` values are administrator-provided rich text stored as `PARAM_CLEANHTML` |
| File serving | `theme_nadine_pluginfile()` checks the component, file area and `context_system` before serving |
| Custom SCSS | Restricted to `moodle/site:config`, validated by Moodle's SCSS setting type |
| External requests | None at runtime |
| Core modification | None |

## What was not tested

These gaps are stated plainly rather than implied to be covered:

1. **Pixel-level visual regression.** Headless Firefox was used to measure
   element boxes, read computed styles and capture screenshots at 1440×900 and
   500×1000, so the layout numbers in this document are measured, not inferred.
   There is still no golden-image baseline to diff against, so a deliberate
   visual redesign would not be flagged automatically.
2. **Real mobile devices and touch.** Not tested on physical hardware. The
   500 px viewport exercises the same breakpoints a phone would, but not
   real touch targets, safe areas or mobile browser chrome. Firefox headless
   refuses to open a window narrower than 500 px, so the phone shots are 500
   wide rather than the 390–430 a device would report.
3. **Screen reader testing.** No NVDA, JAWS or VoiceOver session was run. The
   structural checks above cover semantics, labels and landmarks, not the
   spoken experience.
4. **Browser matrix.** Only Firefox was exercised in a real browser; the other
   pages were fetched with `curl` and audited as HTML. The theme uses standard
   CSS custom properties, logical properties and `prefers-color-scheme`, which
   are supported by current Chrome, Edge, Firefox and Safari, but those browsers
   were not run individually.
5. **Production web server.** PHP's built-in server was used. Apache/nginx,
   HTTP/2, compression and CDN behaviour were not measured.
6. **Load and concurrency.** No load test was run.
7. **Moodle 5.3 minor upgrades.** The theme declares `$plugin->supported = [503, 503]`;
   only `2026100500.06` was used.
8. **Other databases.** Only MySQL-compatible storage was used.
9. **Email.** The reference site has no working mail transport, so notification
   emails were not verified.
10. **Backup and restore of theme settings.** Settings live in `config_plugins`
    and file areas; a full site backup/restore cycle was not performed.

## How to reproduce

```bash
# Rebuild the theme stylesheet
php admin/cli/build_theme_css.php --themes=nadine

# Page matrix
./testpages.sh student1 'Student!2026' student
./testpages.sh teacher1 'Teacher!2026' teacher
./testpages.sh admin "$ADMIN_PASS" admin

# Structural audit over saved pages
python3 audit.py '/tmp/artifacts/*.html'

# Functional flows
python3 functional_test.py

# Browser geometry, console and network audit (Playwright + Firefox)
python3 measure.py 1440 390

# Timings
./bench.sh
```

The scripts live outside the plugin and are not part of the distributed ZIP.
