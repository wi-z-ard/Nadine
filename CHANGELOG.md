# Changelog

All notable changes to the Nadine theme are recorded here. The format
follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project
uses [Semantic Versioning](https://semver.org/).

## [1.29.19] — 2026-11-15

### Added

- The theme selector card now shows a screenshot of the theme. Moodle looks for
  `pix/screenshot.*` and accepts `png`, `jpg`, `gif` and `svg` — not `jpeg`, so
  the artwork ships as `pix/screenshot.jpg`.

### Changed

- The rail avatar is requested at size 50 as well, so it gets the 100px icon
  instead of the 35px one it was stretching over a 30.4px slot on retina
  screens. The initials keep their `1.9rem` box.
- The theme selector's preview is a 3:2 box rather than Boost's 7rem strip. On a
  three-column grid that strip is a 2.1:1 letterbox that cuts a 3:2 screenshot
  through its headline; the cards now show the preview whole and stay level with
  each other.

## [1.29.18] — 2026-11-15

### Changed

- The dashboard avatar is requested at size 50 so core serves the 100px icon
  instead of the 35px one it uses for every size below 50. It still renders at
  36px; the difference is that a profile photo is now crisp on a retina screen
  rather than a 35px file stretched over 72 device pixels. Costs about 12 KB on
  the dashboard, cached for an hour like any other icon.

## [1.29.17] — 2026-11-15

### Fixed

- The dashboard welcome avatar and the rail avatar now render initials as a
  circle the same size as the photo. Core's `.userinitials` only carries width
  and height for 16/30/35/50/64/100, so the welcome band's 72 and the rail's 36
  both fell through to an unsized pill, and a learner without a profile picture
  saw a small grey blob where the avatar belongs. The welcome avatar is now
  36px in both states, the rail initials match their 1.9rem photo, and both use
  the theme's surface and ink tokens rather than Boost's greys.

## [1.29.16] — 2026-11-15

### Changed

- The course card and dashboard rings now hold their own percentage, with the
  caption beside them, matching the course hero's ring. Both rings grew from
  2.6rem to 2.9rem so three digits still clear the cut-out.

## [1.29.15] — 2026-11-15

### Changed

- Course cards use the course's featured image when it has one, falling back to
  the generated pattern when it does not — the same rule core's own course cards
  follow. The URL is read from core's `course_image` cache, so a card costs one
  cached read rather than a file lookup. This fixes the inconsistency where
  core's "Available courses" grid showed real cover art while the theme's own
  cards showed a pattern for the same course.

### Fixed

- The comment on `theme_nadine_course_card()` claimed core never populates the
  `course_image` cache. It does; the cache is populated per course and cleared
  when the course is updated.

## [1.29.14] — 2026-11-14

### Changed

- The login panel's paper fade is now an ellipse anchored at the top-left
  instead of a full-width band. The copy keeps an opaque wash; the illustration
  is no longer flattened under a sheet of white from the header down to
  four-fifths of the panel, so the drawing reads from roughly halfway up.
- Dark mode adapts the panel instead of dimming it with a 0.92 brightness
  filter. The panel becomes the dark surface, the copy flips to the theme's
  light ink through six panel tokens, and the illustration sits at half opacity
  as a texture under the same scrim.

## [1.29.13] — 2026-11-14

### Changed

- Course cards and the dashboard's course cards now show completion as a small
  radial ring beside a display figure and caption, replacing the linear groove.
  The ring is the same shape as the one in the course hero, and all three are
  drawn from a single `nl-progress-ring` SCSS mixin so they cannot drift apart.
- The `--nl-progress-fill` token is gone. The ring's arc uses `--nl-accent`,
  which clears the 3:1 non-text minimum in every shipped palette; the old
  softened fill reached only 2.44:1 on white.

## [1.29.12] — 2026-11-13

### Added

- The navigation rail now has a second state: an icon rail 4.9rem wide. The
  collapse control narrows the column instead of sliding it off-canvas, so
  navigation stays one click away. Labels are clipped rather than removed, so
  every icon keeps its accessible name, and each link carries a `title` for the
  hover tooltip. The page gutter, the navbar inset and the seam all animate to
  the new width together.
- The rail state is stored as the `navrail-collapsed` user preference through
  `ajax/rail.php`, so it survives the next page load. Declared and exported by
  the privacy provider.

### Changed

- `.moodle-dialogue` is rounded to 0.7rem with `overflow: hidden`, and its
  header is tinted with 8% of the palette accent over the surface colour. The
  wrapper takes the same radius so no square hairline shows inside the shell.
  Fullscreen dialogues are excluded so the file picker keeps its own scrolling.
- Rail control labels read "Collapse to icons" and "Collapse or expand the
  navigation rail", matching what the control now does.

## [1.29.11] — 2026-11-13

### Changed

- `/user/preferences.php` is re-laid. Core renders the four link groups as a
  Bootstrap row of three equal columns, so the fourth wrapped under the first,
  and the page sat in Boost's 830px reading measure inside a 1137px area: two
  thirds of the window went unused and the cards read as a broken grid. The
  groups are now two balanced columns of cards across the full content width
  — `main-inner` 830 → 1140px, `region-main` 734 → 1044px, cards 253 → 512px.
  CSS columns rather than a grid, because the longest group is four times the
  shortest and a grid would stretch the short cards to fit.
- Each preference link is a full-width row with a hover wash and a trailing
  chevron that fades in, so the list reads as navigation instead of a
  paragraph of links. Card titles take a hairline under them, and the page
  heading is set at the display serif's 1.9rem step.
- One column below 768px, two above, and the chevron mirrors in Arabic.

## [1.29.10] — 2026-11-13

### Added

- `tests/lib_test.php`, 21 PHPUnit cases over the colour maths, the eight
  palettes, the contrast helpers, the icon loader and the SCSS prelude. The
  suite asserts the contrast floor for every shipped palette, so a new palette
  cannot ship with unreadable text.

### Fixed

- **White text on the accent failed WCAG AA.** `#ef5e00` reaches only 3.35:1
  against white, and it was the fill for primary buttons, outline button
  hover, the secondary navigation pills, pagination, the message send button
  and the notification count. `TESTING.md` had recorded 7.91:1 AAA for that
  pair — a measurement from the pre-orange palette that was never re-taken.
  The accent now has two roles: `--nl-accent` keeps the brand orange for
  decoration, and `--nl-accent-action` is stepped towards black until it
  clears 4.5:1 both under white text and on the canvas. The default palette
  becomes `#bf4b00` (4.97:1 under white, 4.53:1 on the canvas), amber becomes
  `#9e6218`, and the six palettes that already passed are untouched.
- The accent rows in the accessibility table were re-measured; four of them
  were wrong.
- The theme's CSS weight claim in `TESTING.md` was stale. Nadine now adds
  260,162 bytes (19.4%) over Boost rather than the recorded 2.10%, measured
  against a like-for-like Boost build.

### Changed

- `theme_nadine_action_color()` derives the action colour in one-percent
  steps, so a custom accent gets the same correction without manual checking.
- `theme_nadine_relative_luminance()` and `theme_nadine_contrast_ratio()` are
  now part of the theme's public helpers, used by both the build and the
  tests.

## [1.29.9] — 2026-11-13

### Added

- A privacy provider declaring the `drawer-open-index` user preference that
  `ajax/drawer.php` writes. The preference name is Boost's, because both themes
  drive the same course index drawer; the provider says so rather than
  inventing a second key that could drift out of step.
- `db/uninstall.php`, which restores the parent theme's `enablecolourmodes` and
  `defaultcolourmode` values that the theme mirrors its own controls into. The
  original pair is snapshotted once, the first time the theme writes to it, so
  removing Nadine leaves the site as it was found instead of leaving Boost
  configured by a theme that is no longer installed.

### Changed

- `pix/favicon.svg` is a real vector built from the logo's own path and
  gradient, 1.2 KB instead of a 500×500 PNG wrapped in base64 at 120 KB. It
  carries a `viewBox` and no fixed width, so it scales to whatever size the
  browser asks for.
- The README no longer describes `pix/lucide/` as the theme's own artwork; both
  icon sets are third-party and point at their licence files.

### Fixed

- The Arabic language file still named the theme `نورثلايت` (Northlight) after
  the rename. It now reads `نادين`.

### Code style

- The theme now passes the Moodle code checker with **0 errors**. The first
  full run against `moodlehq/moodle-cs` found 18 errors and 42 warnings in
  4,158 lines of PHP; 48 of them were formatting and were fixed with `phpcbf`.
  The rest were the 180- and 151-character lines in `lib.php` and
  `ajax/search.php`, a nested ternary in the search endpoint, two lowercase
  inline comments and five out-of-order language strings.
- What is left is six warnings, all of them expected: three `MOODLE_INTERNAL`
  notices on `db/` files that Moodle core writes the same way, one on `lib.php`,
  and two "expected login check" notices on the AJAX endpoints, which do their
  own `isloggedin()` / `forcelogin` checks rather than forcing a login the
  search panel is designed to work without.

## [1.29.8] — 2026-11-12

### Changed

- The theme ships the full favicon set instead of a single `.ico`: a 48/32/16
  `favicon.ico`, a scalable `favicon.svg`, a 96×96 PNG for pinned tabs, a 180×180
  Apple touch icon and a web app manifest with 192×192 and 512×512 maskable
  icons. `standard_head_html()` links them, so every layout gets them without
  repeating markup in a template, and the URLs carry the theme revision so an
  upgrade does not leave an old icon in the browser cache.
- The packaged set steps aside when an administrator uploads a favicon in the
  theme settings, since that file then defines the icon on its own.

## [1.29.7] — 2026-11-12

### Fixed

- Dropdown items no longer overhang the menu's right edge. Bootstrap's
  `width: 100%` is measured against the menu's content box and does not account
  for the theme's 0.3rem inline inset, so every item ran 0.3rem past the padding
  and left an asymmetric gap — 12px on the left, 3px on the right. The theme now
  sets `width: auto`, so the margins come out of the width and both sides sit
  evenly.

## [1.29.6] — 2026-11-12

### Changed

- Calendar event cards mark the event type with a dashed rule under the header
  instead of a bar down the inline start. The rule spans the card at the header's
  own edge, so the colour still reads at a glance without indenting the title,
  and the mirrored right-to-left layout no longer shifts it.

## [1.29.5] — 2026-11-12

### Changed

- `h3` (and `.h3`) pins at 1.65rem from 1200px up instead of Bootstrap's
  1.75rem. Below the breakpoint the responsive `calc(1.3rem + 0.6vw)` step is
  untouched. Component headings that set their own size — the management page
  titles, section names, admin settings — keep it.

## [1.29.4] — 2026-11-12

### Changed

- Course page section cards are borderless. The 1px outline on `li.section` and
  the bottom rule under `.course-section-header` are gone; the surface and the
  soft shadow carry the separation, and the header band takes the card's top
  corners itself so the tint still follows the radius.
- `li.section` no longer clips its overflow. That clipping cut off every
  dropdown inside a section — the section action menu and the Add content menu
  opened below the card edge and were invisible.
- The Add content button sits in core's divider band, which is hidden until the
  band is hovered or holds focus. The band now stays visible while its menu is
  open, so a click that leaves no focus behind still shows the list.
- The Add content menu and the section and activity action menus take the same
  chrome as the management menus: rounded surface panel, 0.9rem labels, and an
  icon chip per item. Each action keeps its own hue, keyed off its glyph so it
  holds where core omits the `editing_*` class — View blue, Edit settings amber,
  Highlight rose, Duplicate violet, Hide slate, Move teal, Assign roles green,
  Group mode indigo, Permalink steel, Delete danger, Activity or resource the
  accent and Subsection violet — with dark-mode tones lifted for contrast.

## [1.29.3] — 2026-11-12

### Changed

- The Categories and Courses panes on `/course/management.php` set their body
  text at 0.95rem, so the admin lists sit a notch under the page scale.
- The key/value rows in those panes no longer use core's `col-md-3` label
  column. The label keeps its natural width and never wraps, and the control
  takes the remaining line, dropping below only when the row cannot fit both.
  "Move selected courses to…" and "Move selected categories to…" are one line
  instead of four, and the Sorting selects are no longer clipped.
- Each pane's bulk-action band is separated by a hairline with its own padding
  instead of butting against the last list item.
- The per-row action menu is restyled: rounded surface panel, 0.9rem labels,
  and every Font Awesome glyph sits in an accent chip that deepens on hover.
  Destructive entries (Delete) take the danger tint.

## [1.29.2] — 2026-11-11

### Changed

- The header edit-mode switch follows the palette. Boost points the checked
  track at `--bs-primary`, so the on state was a navy blue that matched nothing
  else in the bar; the design-system variables are restated on
  `.editmode-switch-form` with the accent (hover: accent-strong, thumb:
  accent-ink, disabled: a 45% accent mix), so the switch reads as part of the
  theme in light and dark mode.

## [1.29.1] — 2026-11-11

### Changed

- The brand mark is the new `pix/logo.svg`: the monogram filled with an orange
  gradient (`#FFB46B` → `#F97316` → `#D2470C`) on a rounded plate washed with
  10% orange, so it reads on the light navbar and on the dark one. The header,
  footer and login page all use it; the old star mark is no longer referenced.

## [1.29.0] — 2026-11-11

### Changed

- The theme is now **Nadine**. The component is `theme_nadine`, the folder
  `public/theme/nadine`, the stylesheet `scss/nadine.scss`, the language files
  `theme_nadine.php`, the renderer helpers `nadine_*` and the JS modules
  `theme_nadine/*`; the drawer endpoint follows the folder. Settings, the hero
  image and the active-theme value were migrated in place, so the site keeps
  its palette, copy and uploads.
- Display name, settings title and the default palette are "Nadine"; the Arabic
  strings use "نادين".

## [1.28.7] — 2026-11-10

### Changed

- Selects take the small control size: `.form-select` is 0.875rem instead of
  Bootstrap's 1rem, so a short value no longer reads as large as the label
  beside it.

## [1.28.6] — 2026-11-10

### Changed

- The column titles on `/course/management.php` — Categories, Courses and the
  course detail name — are h3 elements that inherited the page-heading scale
  and rendered at 1.75rem inside a 300px column. They are 1.3rem now, with the
  weight, tracking and line height of a list label.
- The three management card caps take a 0.9rem × 1.15rem inset instead of
  core's 0.4rem × 0.6rem, and each title carries a small accent tick so the
  columns read as one family.

## [1.28.5] — 2026-11-10

### Changed

- A focused link is marked with the accent label alone. Core's focus treatment
  — a solid fill, a bar above and below, a gray-900 label and a reserved
  outline — is dropped on `.aalink`, `.autolink`, `.arrow_link`, the footer and
  activity links, so the text colour is the only change.

## [1.28.4] — 2026-11-10

### Fixed

- The arrow on the front-page course card turned white in dark mode and
  disappeared into its white disc. The glyph is a fixed dark ink now, since the
  disc stays white in both modes; the hover state still switches to the accent
  disc with the accent ink.

### Changed

- Front-page and search course cards take a 0.7rem radius instead of the 1rem
  card default.

## [1.28.3] — 2026-11-10

### Fixed

- Course cards on the front page, in search results and in the catalogue kept a
  `#d9d4ca` edge in dark mode. Core builds those rules from `$border-color`, a
  fixed light-mode value compiled into the stylesheet, and their id outranks the
  theme's `.coursebox` rule. The token is restated at the same weight, with a
  matching hover rule so the stronger edge still applies.

## [1.28.2] — 2026-11-10

### Fixed

- The header hairlines still carried core's `--bs-gray-300` fill behind the
  gradient, so the "faded" ends resolved to grey stubs instead of the bar. The
  fill is cleared with an id-weighted selector that outranks
  `.navbar.fixed-top .divider`.

## [1.28.1] — 2026-11-10

### Changed

- The hairlines between the header controls are 36px instead of 75% of the bar
  and fade out at both ends, so they read as separators rather than as full
  rules. The gradient replaces Bootstrap's `border-start`, which could only
  draw a hard line.

## [1.28.0] — 2026-11-10

### Changed

- The appearance control is a three-segment pill instead of a switch: sun, moon
  and system sit side by side in one 64px track, and the active one is a raised
  surface chip in the accent. Each segment sets its mode in a single click —
  the theme overrides `theme_boost/colour_mode_menu` and renders core's own
  `set-colourmode` options, so `theme_boost/colourmode` drives it unchanged and
  the dropdown is gone. The control keys off `aria-checked`, which the module
  already updates.

## [1.27.0] — 2026-11-10

### Added

- The colour mode control in the header is a switch instead of a bare icon
  button: a 3.2rem track with a 1.24rem knob that slides to the active mode
  (light at the start, system in the middle, dark at the end) and carries that
  mode's Lucide glyph — sun, contrast or moon. The state is read from the icon
  class core swaps on the trigger, so the control follows the setting with no
  extra JavaScript. Its menu becomes a card with a named row per mode, the
  active row in the accent with a check, and the same Lucide glyphs.

## [1.26.0] — 2026-11-09

### Added

- The icons in the header user menu are colorized: each glyph sits on a 1.6rem
  chip filled with 11% accent and is painted in the accent itself, and both
  deepen on hover (18% chip, accent-strong glyph). The chip is drawn by
  `::before` and the mask by `::after`, because a CSS mask clips every layer
  the element paints and the fill and the glyph cannot share one box.

### Fixed

- The user menu panel stayed white in dark mode while its labels flipped to
  light: Bootstrap defines `--bs-dropdown-bg: #fff` on `.dropdown-menu` itself,
  so the `:root` token never reached it. The panel now takes `--nl-surface`
  directly.

## [1.25.1] — 2026-11-09

### Changed

- The notification bell is masked onto core's icon element itself. The theme
  previously drew it with the toggle's `::before`, which is core's absolutely
  positioned pointer triangle; the bell needed `position: static` to escape
  that box and the rule fought core's geometry. Core's pointer triangle is
  hidden as well, so no grey wedge hangs under the rounded card.

### Fixed

- The "View full notification" link in the popover was clipped by the card's
  right edge: core sets the row to `width: 100%`, so the theme's inline margins
  pushed it past the container. The row is `width: auto` now, and the link is
  inset by the row's padding instead of core's 5px.

## [1.25.0] — 2026-11-09

### Added

- The notifications page (`/message/output/popup/notifications.php`) becomes a
  two-column card instead of a 600px box with two bare panes: a warm rail of
  padded rows, an accent dot on unread subjects, an accent edge on the selected
  row, and a detail pane with its own header band, readable body typography and
  a tinted footer. Both panes fall back to a centred, muted message with the
  theme's bell, and the layout collapses to core's sliding panes below 980px.

### Changed

- The notification bell sits on the same baseline as the message and user
  controls. Core positions its toggle glyph with `position: absolute`, so the
  theme's bell inherited the offset and hung below the navbar row; it is a flex
  item now. Core's white pointer triangle under the bell is removed as well.
- The notification popover drops at the theme's 4.5rem navbar height (72px)
  rather than core's 68px, so it starts below the header's border instead of
  tucking under it.

## [1.24.0] — 2026-11-09

### Added

- Every item in the header user menu carries a Lucide glyph — profile, grades,
  calendar, private files, reports, preferences, language, switch role, site
  administration and log out. Core prints the items with no icon at all, so the
  theme matches them on the target path and keeps a neutral dot for items a
  plugin adds; the panel itself becomes a card with a 16.5rem measure, rounded
  corners and the theme shadow.

### Changed

- The notification popover is a card rather than a bare white box: a warm
  header band with the title in the darkest ink, circular icon buttons, a
  hairline above the footer, an accent "See all", a scrollable body with a
  22rem cap, and notification rows with an accent tint and heavier subject when
  unread. The Font Awesome bell, check, gear and cross are replaced with Lucide
  masks, and the empty state gets a muted bell above the message. Core pins the
  panel at 500px with a 25px header and a 30px footer, which left an empty box
  under a short or empty list; the card now hugs its content and only scrolls
  once the body passes its cap.

### Fixed

- The administration and navigation blocks still showed core's sprite while a
  branch loaded its children. Core sets `i/loading_small` as a background image
  on the branch row, so the theme's `core/loading` override never applied. The
  sprite is removed and the row's caret chip becomes the accent ring for the
  duration of the request.
- Admin screens that call `pix_icon('i/loading')` directly — the system status
  table, plugin checks and the colour picker — printed a Font Awesome spinner
  that bypassed `core/loading`. Any `i.icon.fa-spinner` now paints the same
  ring and its glyph is suppressed.

## [1.23.0] — 2026-11-09

### Changed

- Course pages use the theme's content width. Boost caps every
  `body.limitedwidth` page at 830px, and the theme kept that cap for the course
  shell, so a course view or an activity filled about two thirds of the space
  between the rails. `pagelayout-course` and `pagelayout-incourse` now take the
  1240px grid. The page header and the activity header are widened with it, and
  the previous/next bar — fixed to the viewport and centred on it — is offset by
  the navigation rail so its buttons line up with the card above. Boost pins
  each of the three to 830px on its own, so the container alone would have left
  narrow strips inside a wide canvas.

## [1.22.2] — 2026-11-08

### Fixed

- The selected course index item flashed core's navy when hovered or focused.
  Core has a second page-item rule at four classes
  (`.courseindex-item:hover.pageitem`) that repaints the row with the primary
  colour and `#e6e6e6` text, and a five-class rule that recolours its link and
  chevron. Both outranked the 1.22.1 selected state, so the pill turned dark
  under the pointer. The hover, focus and focus-within states now restate the
  accent tint one step stronger (18% background, 45% border) and hold the link
  and chevron on the darkest ink; the link itself still turns accent-strong when
  pointed at directly.

## [1.22.1] — 2026-11-08

### Fixed

- The selected course index item was unreadable. Core paints a page item with
  the primary colour and a contrast foreground, so its link is white; the theme
  replaced only the background with a pale accent wash, leaving the white label
  on a near-white pill. The selected state now sets the background, the hairline
  and the link/chevron colour as one pair: a 14% accent tint, a 36% accent
  border, and the darkest ink. It applies to the current activity and to the
  current section title.

## [1.22.0] — 2026-11-08

### Changed

- The course index is a full-height column from 1200px up. Core anchored it
  108px down and rounded all four corners, so it read as a grey card floating in
  the page with a gap above and below. The panel now starts at the viewport top
  (its header padded clear of the navbar), runs to the bottom edge, has square
  corners, a hairline seam on the page side, and the theme surface instead of
  core's `gray-200`. The header is a warm band with the course name in the
  darkest ink, and *Collapse all* is the same pill as the course body's toggle.

- Course index completion is one marker. Core prints a Font Awesome circle for
  done and an SVG image for to do, so the two states never matched; both are now
  an 8px dot — filled in the semantic colour for done or failed, a hairline ring
  for to do — while the original icon stays in the accessibility tree.

## [1.21.0] — 2026-11-08

### Changed

- Every AJAX spinner is now the theme's own ring. Core's `core/loading`
  template is overridden, replacing the Font Awesome dot sprite with a single
  masked conic gradient that fades from transparent to the active accent, so it
  re-tints with the colour theme. Inside buttons it follows the label colour so
  it stays visible on a filled accent surface. The overlay wash uses the themed
  surface with a small backdrop blur instead of flat white, which removes the
  white flash in dark mode. The accessible "Loading" label is kept in the
  accessibility tree.

## [1.20.1] — 2026-11-08

### Fixed

- Collapsed profile sections now actually hide. The edit form's two-column grid
  was declared on `.fcontainer` itself, which outranks Bootstrap's
  `.collapse:not(.show) { display: none }`: the height animation ran, then the
  fields were painted again at full height. The grid is scoped to `.show` and
  `.collapsing`, so a closed section has no layout. A collapsed section also
  drops the rule and the bottom padding under its header, so the card ends at
  the header instead of ruling off an empty band.

## [1.20.0] — 2026-11-08

### Added

- The breadcrumb trail is rebuilt as a set of chips. Each step is a padded
  target, the slash separator becomes the theme's chevron, and the current page
  is a hairline chip with a marker at its head and the darkest ink in the trail.
  The icons are masks on `::before`, so they follow the ink colour in both
  palettes, and the chevron mirrors in RTL.

- The two "Expand all / Collapse all" controls — core's link above an mform and
  its button above the course sections — share one pill: a hairline border, the
  fold glyph and a label that names the next action.

### Fixed

- The form's "Expand all / Collapse all" no longer gets stuck. Core decided the
  direction from a class it only rewrote once every section had finished
  transitioning, so a form that opened with some sections expanded kept offering
  *Expand all* and a second click expanded instead of collapsing; a section
  behind *Show more* could also be left half-open, because a hidden container
  cannot run a height transition. The theme now reads the sections on each click
  and writes the state of a hidden section directly.

## [1.19.0] — 2026-11-08

### Changed

- The edit-profile form (`/user/editadvanced.php`) is rebuilt as a document.
  Each collapsible section is a surfaced card with a numbered heading and a ring
  chevron; fields sit on a two-column grid with the label above its control,
  full-width for editors, file managers, textareas and autocompletes; the
  password-policy note hangs off a hairline; the save bar keeps core's fixed
  positioning but takes the page surface, a hairline and a blur instead of
  `bg-white`; and the page widens to 72rem. Core's duplicate `<h2>` above the
  form is hidden — the page header already names the user.

  All of it is scoped to `body#page-user-editadvanced`, so no other mform
  inherits the layout.

## [1.18.1] — 2026-11-08

### Changed

- The colour theme is picked from a grid of swatch cards instead of a select.
  Each card shows its palette as an accent/primary strip, the selected card takes
  a ring in its own accent plus a tick, and *Custom* shows a spectrum strip. The
  cards are labels over native radio inputs, so arrow-key navigation, the
  `colortheme` dependency that reveals the two colour pickers, and form
  submission all still work. Rendered by a new
  `theme_nadine\setting\colourtheme` admin setting.

## [1.18.0] — 2026-11-08

### Added

- Colour themes. A new *Colour theme* setting offers seven curated palettes
  beside the default Nadine palette and a *Custom* option that falls back to
  the accent and primary pickers (which are hidden unless *Custom* is chosen).
  Forest, Ocean, Plum, Clay, Slate, Rose and Amber are each an accent/primary
  pair; the strong, soft, progress and dark-mode variants are derived from the
  pair at build time, so one setting reaches buttons, links, focus rings, active
  navigation, pagination, progress fills and the section marks. The hero and
  login washes now read `rgba($nl-accent, …)` instead of a literal orange, so
  they follow the theme too.

  The paper, surfaces and type stay constant — a theme recolours the brand
  carriers and leaves the page recognisable. Every pair was checked for contrast
  before shipping: white on the accent is at least 4.5:1, and the derived
  dark-mode accent is at least 6.3:1 against the dark ink.

  Sites that had set custom accent and primary colours should select *Custom*
  after upgrading; the shipped palettes ignore those two pickers.

## [1.17.2] — 2026-11-08

### Changed

- The display stack is now `Georgia, "Noto Naskh Arabic", "Times New Roman",
  serif`. Iowan Old Style, Palatino Linotype, Book Antiqua and Palatino were
  local-only progressive enhancements: they cost nothing to download but meant a
  Mac rendered headings in Iowan, a PC in Palatino Linotype and a Linux desktop in
  Georgia, so the theme never looked the same twice. Georgia ships on both
  Windows and macOS, so one face now covers every desktop. The same stack is set
  for Bootstrap's `$headings-font-family` in `lib.php`.

## [1.17.1] — 2026-11-08

### Changed

- The front-page course card's hover spine drops from 3px to a 1px hairline
  (`inset 1px 0 0 var(--nl-accent)`), and the hovered card takes `opacity: 0.95`
  so the accent and the raised shadow read as one gesture. The 3px rule sat at
  the weight of a border and competed with the shadow; at 1px it is a mark.

## [1.17.0] — 2026-11-07

### Changed

- The front page's *Available courses* list is rebuilt as a numbered catalogue.
  Core prints these as a stack of wide rows; they were already tiles, but tiles
  with no hierarchy and no affordance. Each card now carries its entry number in
  a frosted ink plate on the cover — the card's position in the list, so it is
  an index rather than decoration — with an open disc opposite it. The card is
  one link, so the disc is painted, not a second target; its arrow turns
  up-right on hover, together with the plate, a leading-edge accent spine, a
  1.05 cover zoom and an accent rule that grows under the title. The teacher
  line becomes the card's footer behind a hairline, and the section heading
  gains the theme's accent tick and a rule that runs to the edge of the column.
  RTL mirrors the disc, the arrow, the spine and the title rule.

## [1.16.9] — 2026-11-07

### Changed

- The secondary navigation's tab rail sits on its own inset strip:
  `#f9f9f8` with `0 0.5rem` padding and a 0.5rem radius, so the pills read as
  one grouped control instead of loose labels floating on the white card. The
  strip is 60px tall — core fixes that height and the React nav centres each
  pill in it — and the dark palette drops it to `#121917`, a shade below the
  card surface, because a near-white band would flood the bar.
- The glyph tile beside each site administration heading is now a circle: the
  radius goes from 0.7rem to 2rem on a 2.3rem box. Only the tile rounds — the
  masked glyph in `::after` keeps its square box, since a radius there would
  clip the icon's corners.

## [1.16.8] — 2026-11-07

### Changed

- Dashboard course cards (`/my/`, `block_myoverview`) are rebuilt as tiles. Core
  fixes the cover at 7rem, which letterboxed a 386px card; the cover is 16:9
  again, the title is the display serif at 1.04rem, the category is a small
  uppercase line, and the progress and action menu share one ruled footer.
  The whole card lifts 2px with a deeper shadow on hover, and the cover zooms
  1.04 while the card is hovered or focused.
- Core renders course progress as the muted sentence "Progress 75%". The
  dashboard now gets a labelled measure instead — caption and figure above an
  8px groove — via a theme override of `block_myoverview/progress-bar`. The
  same partial feeds the list and summary views, where it stretches to a
  26rem maximum rather than collapsing to the caption width. The bar carries
  `role="progressbar"` and the core "Progress" label; the visible caption is
  `aria-hidden`, so the value is announced once.
- Progress fills are flat. The gradient and its gloss are gone from the
  homepage card, and both surfaces now draw the same token, `--nl-progress-fill`
  — the accent mixed 28% towards white in light (a faded orange) and 12%
  towards black in dark. The caption and the percentage carry the value, so the
  bar is a redundant, decorative indicator rather than the only cue: it measures
  2.05:1 against the groove in light and 4.23:1 in dark.

## [1.16.7] — 2026-11-07

### Changed

- The homepage course card carries a real progress readout instead of a bare
  5px rule: a `Progress` caption and a display-size percentage sit above a
  recessed 8px groove, and the fill glosses vertically and grows in over one
  second. The old meter had no label and read as a divider under the title. The
  caption is `--nl-muted` (5.6:1) rather than `--nl-subtle` (3.8:1), and the
  fill uses `--nl-accent-strong` so it clears 3:1 against the groove — the pure
  accent reached only 2.8:1 on a light surface. The visible caption and figure
  are `aria-hidden`; the bar itself keeps the `progressbar` role and the
  "75% complete" label, so a screen reader hears the value once.
- The category is a frosted chip over the cover instead of an eyebrow line in
  the body, so the course name leads the copy. The chip uses a light fill in
  both palettes because it sits on artwork, not on a theme surface.
- The card action is a pill: a tinted chip with near-black label and an accent
  arrow, deepening its tint and nudging the arrow on hover. White on the accent
  was 3.35:1, so the label does not invert to a solid fill.
- The card takes a 2px accent focus ring (`:focus-within`) and its link
  suppresses the default outline, which would have been clipped by the card's
  radius.

### Fixed

- The single-card banner was 400px tall on a course whose cover fell back to the
  generated pattern: the square 320×320 SVG set the media's height through the
  image's intrinsic ratio. The image is now out of flow in the banner, so the
  body sets the height and the cover crops to fit — the card is 200px tall on
  the same course.

## [1.16.6] — 2026-11-06

### Changed

- The course browser is a stack of disclosure cards rather than a bare tree.
  Core renders `/course/index.php` as a nested list of category names flush
  against the left edge, so four categories left two thirds of the column
  empty; each category is now a card with its name at the leading edge and a
  Lucide chevron at the trailing one. The chevron toggles the panel in place and
  the name still opens the category page — core's expander ignores clicks that
  land on the anchor, so the two affordances keep their own meaning. Expanded
  headers take the muted fill and a hairline rule, and the panel's course rows
  become a list with a book glyph and a summary button instead of four nested
  boxes.

- `Expand all` is a pill in the surface language instead of a bare link.

- A category page lists its courses in two columns above 992px. Core leaves
  `.courses.category-browse` as a block, so a single card stretched across
  1140px with its summary stranded on the left.

- The core toolbar above the tree repeats the catalogue hero's search field and
  category select a few centimetres lower. Both are hidden on this layout; the
  *More actions* menu (add course, manage courses) stays.

### Fixed

- Dark mode underlined every link inside a course card — the title, the teacher
  line — which turned a card into running text. Card links opt out, as the
  breadcrumb trail and section titles already do.

## [1.16.5] — 2026-11-06

### Changed

- The pressed outline button no longer draws a near-black ring. Its active
  border was `--nl-ink`, which on a near-white fill read as a hard black
  outline; it now uses a dedicated `--nl-border-active` grey (`#5f6b68`, 5.3:1
  on the pressed fill), one step deeper than the hover edge so the state still
  reads.

### Fixed

- `btn-outline-secondary` keeps its tokens in dark mode. Boost's
  `[data-bs-theme="dark"]` block outranked the theme's bare class selector, so
  every token — resting border, hover fill, active border — fell back to
  Boost's grey. The dark palette is now named explicitly, and the border
  restatement stays on the bare class so the `:active` rule still wins.

## [1.16.4] — 2026-11-06

### Changed

- Form controls take a neutral control border (`#8f8a80`, 3.4:1 on the surface)
  and a 6px radius. The previous green-grey hairline read as a smudge at 1px
  beside the warm canvas — most visible on the language select in the sign-in
  header, which sits on a white bar.

- The select arrow is the Lucide chevron drawn at 1rem instead of Bootstrap's
  16×16 glyph squashed into 16×12, so its stroke stays as sharp as the hairline
  around it; the dark palette gets a lighter stroke.

## [1.16.3] — 2026-11-06

### Changed

- The header's guest *Sign in* action is a pill with a Lucide `log-in` glyph.
  The glyph carries the accent and nudges towards the reading edge on hover and
  keyboard focus (mirrored in RTL); the label keeps the full-contrast ink, so
  the control stays legible at 3:1 instead of relying on accent-on-white text.
  *Create account*, when self-registration is enabled, takes the same shape with
  a `user-plus` glyph.

## [1.16.2] — 2026-11-06

### Changed

- The sign-in form is no longer a card. Its surface, radius and shadow are
  gone, so the form sits directly on the canvas and the split layout reads as
  one composition; the fields, the divider and the primary button carry the
  structure.

- The branding panel takes a subtle gradient overlay: the accent wash from the
  top-left corner is slightly stronger, and a faint diagonal veil warms the top
  and settles the illustration into the paper at the foot.

## [1.16.1] — 2026-11-06

### Changed

- The secondary navigation bar spans the content column instead of hugging its
  tabs. Core centres the tab list inside an 830px reading measure, which left
  the bar ending short of the cards and tables below it; the list now starts at
  the bar's leading edge, so the two share the same left and right edges.

- The report card's tinted leading rule is visible at rest — 20% of the card
  height at 30% opacity — and brightens to full opacity on hover and keyboard
  focus, rather than appearing from nothing.

## [1.16.0] — 2026-11-06

### Added

- The course reports page (`/report/view.php`) is a card grid instead of a bare
  list of links. Each card carries the report's Lucide glyph in a tinted chip,
  the plugin's own title, a one-line summary and a trailing chevron; the whole
  card is the hit area, and hover lifts it and grows a tinted rule on the
  leading edge. The tint map is keyed by report URL, so a report a plugin adds
  still renders — in the neutral tone and without a summary — rather than
  disappearing. The page opts out of the 830px reading width so the grid can
  run to four columns, and falls to one on a phone.

## [1.15.3] — 2026-11-06

### Fixed

- The secondary outline button's resting border now uses the theme's
  `--nl-border-strong` token. Boost's preset appends a literal
  `border-color: $gray-600` to `.btn-outline-secondary` — the variable block
  from Bootstrap's mixin does not carry the border on its own — and that
  declaration sat later than `.btn`'s `border` shorthand while our rule only
  changed the custom property. The variant now restates the border from the
  variable, so the token reaches the pixel.

## [1.15.2] — 2026-11-06

### Changed

- Buttons carry their type scale as a Bootstrap custom property: `0.925rem`
  for the base button and `1.2rem` for `.btn-lg`, so every variant and every
  page inherits the same sizes instead of the stock 1rem/1.25rem pair.
- `.btn-outline-secondary` uses its own hover pair — a `#677c7b` border with
  `#4e5b59` text — rather than the theme's near-black ink, so the secondary
  action reads as a quiet grey-green rather than a darkening outline.

## [1.15.1] — 2026-11-05

### Fixed

- The calendar page uses the width it has. Boost caps every
  `pagelayout-standard` page at its 830px course-content measure, and that rule
  outranked the theme's container width, so the month grid only filled about
  two thirds of the area between the drawers on a wide screen. `/calendar/*`
  now opts out and takes the whole page area; the gutter still keeps the grid
  off the edges.

## [1.15.0] — 2026-11-05

### Added

- The public profile page gets an identity band. Core's page header — picture,
  name, message button — and a new strip of facts share one surface: department
  and institution, city and country, and the month the account was created.
  Every chip is read from the user record and filtered against the site's hidden
  profile fields, so the strip never shows more than the tree below it.
- The edit action sits at the far end of that strip, opposite the facts, and
  follows the same capability and authentication-plugin rules core applies
  before it adds the node to the tree. The card no longer carries a second one.

### Changed

- The profile page opts out of the 830px reading width, as the site home and the
  dashboards already do, so the grid uses the full 1240px container.

- The profile tree is a responsive grid of cards instead of one stack of
  full-width Bootstrap cards. Section headings are small uppercase labels with
  an accent tick, detail rows are a two-column definition grid with hairline
  separators, and long course lists become chips. On a phone the definition rows
  stack and the grid falls to one column.
- The message button carries a Lucide glyph instead of the Font Awesome icon the
  theme never loads, and the page-editing controls are reduced to a quiet text
  action so they no longer compete with the identity.

## [1.14.6] — 2026-11-05

### Added

- Every headline on the editorial surfaces is now editable from the theme
  settings, not just the homepage hero. *Homepage → Hero content* gains the
  primary button label and the side-panel title and text; *Homepage →
  Catalogue band* gains the eyebrow, title and introduction above Moodle's own
  course listing; *Homepage → Features* gains the section heading and each
  card's title and text; *Homepage → Closing call to action* gains the title,
  text and both button labels; *Dashboard → Welcome band* gains the eyebrow,
  heading and introduction, with `{name}` standing in for the signed-in user;
  *Branding* gains the login panel eyebrow and the login image description.
- Empty fields fall back to the translated default, so an untouched site keeps
  its current copy.

### Changed

- The catalogue and dashboard introductions are rendered as HTML, matching the
  hero and login introductions, so a configured paragraph keeps its markup
  instead of printing tags on the page.
- The new fields start with the copy the theme already prints, seeded by the
  upgrade step, so an administrator edits the live text instead of a blank box.
  Clearing a field still falls back to the language string.

## [1.14.5] — 2026-11-04

### Added

- Social links in the footer, configured per network in *Footer → Social links*:
  Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp and GitHub. The
  icons are the networks' own marks, inlined so they take the link colour. A
  network with no address is not rendered, so a site never advertises an
  account it does not keep.
- The footer settings have their own tab, which now holds the tagline, the
  description and the social addresses.

### Changed

- The footer is rebuilt as a colophon. It is the one dark surface in light
  mode, closed by a hairline that carries the accent from the reading edge, and
  lifted one step off the canvas in dark mode rather than inverting. The
  identity, tagline and description sit in a wider first column, the three link
  columns carry an accent tick above each heading, and links underline from the
  reading edge on hover without moving.
- The identity is no longer truncated in the footer. The navbar's 42vw cap on
  the site name applied here too and cut the name in half on a phone.
- The bottom bar gains a *Back to top* link beside *Built with Moodle*.

### Fixed

- The footer could collapse to nothing once its content was clipped: in the
  drawers layout it is a flex item, and `overflow: hidden` drops a flex item's
  automatic minimum size to zero. It is now explicitly `flex: none`.

## [1.14.4] — 2026-11-04

### Added

- The login panel ships its own illustration (`pix/login-art.png`) and uses it
  when no image has been uploaded in *Login image*. The illustration fills the
  panel as its background; a paper-to-transparent gradient over it keeps the
  copy on solid ground at the top and lets the drawing come through from the
  middle down.

### Changed

- The login panel is a printed surface rather than a dark photograph: warm
  paper, ink copy, and an accent wash behind the headline. Core's stock
  photograph and its "AI generated" watermark are switched off. Dark mode dims
  the panel instead of inverting it, so the illustration keeps its paper.
- The login card has no border. It is held by its own surface and a soft
  shadow, which reads cleaner against the canvas than a hairline box.
- The three statements under the intro are one compact checklist. Their long
  descriptions stay in the markup but out of the panel, which leaves the
  illustration the lower half of the layout.
- The first field takes focus on desktop. On a phone the keyboard would cover
  the form before it has been read.

### Fixed

- The login page printed two password visibility controls on top of each other:
  the theme added its own button while core already ships `toggle-sensitive`.
  The theme's button and its AMD module are gone, and core's control is dressed
  instead — the glyph is a Lucide mask keyed on the field's `type`, so the eye
  and the field can no longer disagree, and the stray Font Awesome box is gone.
- The password control no longer paints Bootstrap's blue focus halo inside the
  field.

## [1.14.3] — 2026-11-03

### Added

- The front page's available-courses list is a tile grid instead of a stack of
  wide rows. Core prints a title, then a thumbnail beside the summary; the theme
  lifts the image and the copy out of that flex row with `display: contents`,
  so the same markup stacks as cover, title, summary, teachers without
  overriding core's renderer. Summaries clamp to three lines so a row of cards
  stays level, and the section heading takes the same scale as the other
  front-page titles.
- Teacher names in those cards carry a Lucide person mark. The "Teacher:" label
  stays in the accessible name and is hidden visually.

### Changed

- The hero no longer adds its own block padding, so it starts where the page
  content starts. The vertical rhythm above and below it now comes from
  `.main-inner` alone, which keeps the hero aligned with the rest of the page
  instead of sitting 40–70px lower than everything else.
- The hero art column is top-aligned with the copy column. Centred, a short
  image drifted down the page and its top edge landed level with the middle of
  the headline; it now starts on the same line as the eyebrow.
- The login page keeps its split layout but is rebuilt as one surface. The
  photograph is treated as texture under a scrim, so the panel copy and the
  small print are readable; the brand no longer appears twice; fields read as
  surfaces with an accent focus ring; the recovery link sits with the password
  field rather than under it; the language selector that duplicated the top
  bar's is gone; and the guest button carries the same person mark as the
  course cards.
- On phones the login panel comes back as a slim banner carrying the eyebrow
  and the headline. Core hides the panel below 992px, which left the phone with
  a form and no identity at all.

### Fixed

- Links inside the login card were underlined in dark mode only. Moodle's own
  dark stylesheet underlines every link inside `#page-header` at a specificity
  the card's rule could not beat, so the two palettes disagreed.

## [1.14.2] — 2026-11-03

### Fixed

- Opening the message drawer no longer leaves the docked blocks column stranded
  above it. The header band was inset by that column, so while the drawer was
  open the top-right corner kept the column's own heading and its collapse
  control, and the header read as narrowed to make room for them. The band now
  runs to the right edge of the viewport for as long as the drawer is open, the
  column's head is hidden with the rest of the column, and the header inner
  takes the column width back as padding, so nothing inside the band moves.
- The drawer sits on the header's line. Core parks it on its own navbar
  constant, which is 4px shorter than this header, so its top edge cut into the
  band's bottom border.
- The drawer's close control takes a 0.5rem margin on every side instead of a
  lopsided padding, so the disc keeps an even distance from the top, the edge
  and the header band below it. Core's `pe-2` utility is reset for the same
  reason.

## [1.14.1] — 2026-11-02

### Changed

- The message drawer — the panel behind the navbar's message icon — is the same
  application as `/message/index.php`, so it now shares that component instead
  of only borrowing its background. The overview list sits on the rail's tone,
  the search field and its submit button share one rounded box, the settings
  gear and the sub-view back/search controls are round ghost targets, the close
  glyph is a quiet disc, "Contacts" is a pill, the three sections are uppercase
  micro-labels opened by hairlines, and "See all" is a hairline footer link.
- Contacts and Requests in the drawer are a segmented control rather than two
  filled Bootstrap pills, and the settings groups take the same micro-label
  treatment as the overview sections.

### Fixed

- The drawer's section carets are masked Lucide chevrons, matching the
  administration tree, instead of Font Awesome glyphs.
- The hairlines between overview sections were being discarded: core marks each
  section `.border-0`, which is `!important`, so the rule needed the same weight.
  The same fix restores the separators on the messaging page.

## [1.14.0] — 2026-11-02

### Added

- The messaging page is a designed surface. `/message/index.php` becomes one
  app frame — a recessed contacts rail on the left, the conversation on the
  right — with a search field that shares a rounded box with its submit button,
  round ghost targets for the settings gear and the conversation's back
  control, a "Contacts" pill, uppercase section labels separated by hairlines,
  contact rows with a warm hover wash, a framed empty-conversation note, and a
  composer with a filled accent send button. Sent bubbles are primary ink,
  received bubbles are the recessed canvas, and the CSS-triangle tails are gone.
- `--nl-primary-ink` joins the palette: the dark mode primary is lifted towards
  white, so text on a primary fill has to invert with it.

### Changed

- Text links no longer underline on hover. Bootstrap 5.3 re-underlines every
  anchor; the theme answers with colour and, in lists, with a wash, so the extra
  rule only doubled the signal and made rows jitter as the pointer crossed them.
- On phones the messaging page shows one pane at a time — the rail until a
  conversation is opened, then the conversation with its back control — instead
  of core's fixed 4/8 split, which left a 111px rail and a one-letter search
  field.

### Fixed

- The contacts rail started a third of the way down the page and stopped halfway,
  because `/message/index.php` asks for the mydashboard layout and so received
  the dashboard welcome panel, and because core's `height: 100%` chain resolved
  to auto against auto-height ancestors. The panel is now limited to the
  dashboard itself (`my-index`) and the app fills the viewport.
- The first overview section drew a 3px black rule under its label: core focuses
  the toggle on load and the browser's focus ring was drawn outside the button
  and clipped by the header. The app draws an inset accent ring instead.

## [1.13.0] — 2026-11-02

### Changed

- The calendar block is a compact restatement of the full calendar rather than
  the full calendar squeezed into a rail: the empty toolbar band is removed, the
  arrows become 26px discs with the month on its own centred row, cells tighten
  to 2.4rem with 1.7rem discs, the weekday band flattens to a rule, and the
  footer becomes two quiet rows with a trailing chevron. Today keeps the ink disc
  and accent halo; a day with events keeps the accent dot under the disc.
- The administration block's branch toggle is a thin Lucide chevron instead of
  Font Awesome's solid caret, so the toggle matches the leaf chevrons beside it.
  It mirrors in RTL and turns the other way when the group is open.

### Fixed

- The calendar block carried a 27px dead band under the block header: the month
  view's toolbar holds a course filter and a new-event button that core hides,
  but it kept the page toolbar's padding and hairline.
- The block's month name sat left of centre because core's block-region sheet
  pins the heading at 40% of the toolbar. It fills the middle column now.
- Core drew a day with events as a 3em period, which clipped to a black square at
  this width. It is a 3.2px accent dot under the disc, matching the phone layout
  of the full page.
- The block footer's bullet separators added a 21px line box above each link, so
  the two rows sat 51px apart instead of 30px.

## [1.12.1] — 2026-11-02

### Changed

- Sidebar blocks — course categories, calendar, anything else in the right
  region — take `--nl-radius-sm` instead of `--nl-radius-lg`. Blocks in the
  main column keep the large corner, because they are page-sized surfaces.
- The block header has no fill at all: the gradient is gone and nothing replaced
  it, so the header sits on the card surface and a `1px dashed` rule does the
  separating.
- The icon chip's ring is the accent itself at 60% instead of a fixed
  `#D5D3D3`, so ring and glyph read as one object. The `--nl-block-chip-line`
  token is gone with it.
- Block links use `--nl-link` (`#485150`; `#a9b7b1` in dark) with `--nl-ink` as
  the fallback, instead of borrowing the body ink colour.
- The home page's closing band reads `--nl-cta-bg` and `--nl-cta-ink`, its own
  tokens, rather than the ink and canvas pair — restyling links or body copy no
  longer repaints it.

### Fixed

- The calendar block drew a second card inside the block card, because the
  full-page `.maincalendar` frame applied to the mini calendar as well.
- The dashed header rule was being replaced by Boost's drawer rule, which sets
  a hardcoded `#d9d4ca` at four classes; the sidebar region now outranks it.

## [1.12.0] — 2026-11-01

### Changed

- `/calendar/view.php` is one surface: the view selector, course filter and
  new-event action sit in a toolbar, the month grid fills the card edge to
  edge, and the "Import or export calendars" link closes it with a hairline
  and a calendar glyph. Month, day, upcoming and week views all share it.
- The month grid: a quiet uppercase weekday rule on a recessed band, 8.25rem
  rows instead of core's fixed 11.5em, a hairline grid instead of the browser's
  cell borders, a wash on weekends, a tint on the hovered day, and today marked
  with an ink disc and a primary halo. The disc is ink rather than the accent
  because white on the accent only reaches about 3.3:1, while ink clears AA in
  both colour modes.
- Events inside a day are tinted pills in the event type's own hue instead of
  a ringed 12px disc beside a label truncated to six characters. The hue comes
  from the `data-event-eventtype` attribute core already prints, so a plugin
  event type still lands on the neutral colour rather than nothing.
- Month and day navigation are pills with masked Lucide chevrons, mirrored in
  RTL, around a centred display-serif month name.
- Core clips the toolbar with `overflow: hidden`, which cut the view selector's
  dropdown in half. The bar no longer clips.
- Upcoming, day and week event cards get a coloured leading edge, a
  display-serif title and quiet edit/delete controls that stay readable on
  touch rather than appearing only on hover.
- The calendar's own "Events key" block carries a calendar glyph in its header
  and a tinted swatch per event type.

## [1.11.0] — 2026-11-01

### Added

- **Homepage scroll animation** setting (Homepage tab): *None*, *Reveal* or
  *AOS-style*. It applies to the site home only, and the hero is never
  animated because it is the largest paint on the page. *Reveal* is a single
  quiet rise of 1.25rem over 0.55s. *AOS-style* is a longer 0.8s move on a
  decelerating curve, with per-element direction (`up`, `left`, `right`,
  `zoom`) and a 70ms stagger between items that share a parent, so a row of
  cards reads as one gesture.
- The animation is native: `js/scroll-reveal.js` is a plain IIFE with no
  library, no build step and no third-party licence to carry. The mode rides on
  the body as a class, so the stylesheet is static and nothing is compiled per
  setting.
- Visitors who ask their system to reduce motion get the static page, and the
  hidden state is gated on a class the script adds before the page content is
  parsed, so a blocked or failing script leaves the page visible instead of
  blank.

### Changed

- The glyph in a block header is a full disc (2rem radius) instead of a rounded
  square, matching the circular icon tiles used elsewhere in the theme.

## [1.10.0] — 2026-11-01

### Added

- Every section in the site administration tab bar carries a Lucide glyph, and
  every settings category on `/admin/search.php` gets its own glyph in a tinted
  tile instead of the one repeated folder that Font Awesome drew for all of
  them. The glyph is attached in CSS from the href each navigation node already
  exposes and masked with `currentColor`, so nothing is added to the DOM, the
  React tab bar is untouched, and plugin categories added later still land on a
  sensible default. Tints reuse the category colours the theme already had.
- The same glyphs are applied to the server-rendered tab links, so the icons
  are present before the React island mounts and stay after it does, including
  with JavaScript switched off. Plugin folders, which are headings with no link
  of their own, take a folder glyph, and a category the theme has never seen
  falls back to a neutral one instead of drawing an empty tile.

### Changed

- The selected tab is now the accent fill the rest of the theme uses. Core
  styles the tab bar with `.active` while the theme only ever styled
  `.mds-nav-pill--selected`, so the selected tab had been falling back to the
  design-system grey, and on settings pages it carried a stray indicator dot
  next to the icon.
- The administration tab bar spans the content column instead of hugging its
  tabs, so its edge lines up with the category cards below it.
- Category rows are a two-column grid (heading, then chips) rather than a
  Bootstrap row, and their chips are smaller, so a category with ten links fits
  in two lines instead of four.
- The root row of the admin index, which lists the site-wide actions and has no
  heading, is presented as a quiet toolbar rather than a card with an empty
  quarter column.
- On phones the settings search drops below the page heading, which stops
  "administration" from breaking mid-word.

### Fixed

- Core underlines every unclassed link inside `#region-main` in dark mode
  through a five-`:not()` selector. The admin chips and category headings are
  navigation, not prose, so the underline is removed there.

## [1.9.0] — 2026-10-31

### Added

- The dashboard welcome band has a second column that answers "what do I do
  now?" with the one course the learner should pick up. It picks the most
  recently accessed course that is genuinely in progress, then one enrolled but
  not started, then anything they can open, and shows its completion in a ring
  with a resume action. Users with no courses get the catalogue prompt instead,
  so the space is never an empty panel.
- The site home shows the signed-in learner's own courses under the counters,
  as a three-card strip with progress and a resume action. It is deliberately
  not a second catalogue: core already prints every available course further
  down the page, so repeating that list would just be duplication. Guests never
  see it, and it can be switched off in *Homepage* settings.
- `course_card.mustache` is a single card component shared by the homepage
  strip and the dashboard, so the two surfaces cannot drift.
- `theme_nadine_get_user_courses()` orders a learner's courses by last
  access, which is what makes "continue" mean the course they were last in.
  Moodle's own enrolment helpers return courses in creation order.

### Changed

- The dashboard welcome band is a two-column grid above 1100px and stacks below
  it, with the aside becoming a bordered footer.
- Cards carry a 16:9 media block, a category eyebrow, an optional progress
  meter and a trailing arrow that nudges on hover. All of it mirrors in RTL and
  follows the existing dark-mode tokens.
- The card row sizes itself to its contents. Two courses share the row, and a
  learner with a single enrolment sees one wide banner instead of a narrow tile
  beside two empty columns.

### Fixed

- Links without a class no longer get core's blue focus fill with a bar above
  and below. That treatment is drawn for inline text and ignores border radius,
  so on the footer's link list and the admin index's link chips it showed as a
  stray blue line and a dark underline floating around the element. Both
  surfaces now use the theme's own accent ring, which follows the shape.
- The *Homepage* settings page had no `homepagesettings` language string, so its
  title rendered as `[[homepagesettings]]` for every administrator. Every string
  the theme asks for now exists in both English and Arabic.
- A signed-in user opening `/login/index.php` gets core's "you are already
  logged in" notice instead of the login form, and that notice has no heading.
  The login layout now supplies one, so every page the theme renders has
  exactly one `h1`. Boost has the same gap.

## [1.8.0] — 2026-10-31

### Changed

- The icons the theme draws itself now come from Lucide instead of Font Awesome.
  The navigation rail, the drawer handles and close controls, the hero, the
  statistics strip, the feature grid, the dashboard quick links, the course
  header and the site search all use inlined SVG, so they sit on `currentColor`,
  follow the text size of their container and share one 2px stroke.
- The drawer handles previously fell back to bitmap `e/sidebar_*` pix images
  because those names have no Font Awesome mapping. They are now the same
  stroke icons as everything else.
- Moodle's icon system stays on Font Awesome. Core and plugin `pix_icon` calls,
  activity module icons and the glyphs core prints into markup the theme does
  not own (block controls, tree carets, activity chevrons, admin category
  tiles) are unchanged, so no plugin loses its icon.

### Added

- `theme_nadine_icon()` in `lib.php` renders a Lucide SVG from
  `pix/lucide/` with a class, a pixel size and `aria-hidden` already applied.
- `pix/lucide/` with 31 icons and the upstream ISC licence.

## [1.7.0] — 2026-10-31

### Changed

- The course index starts collapsed, so the course itself gets the full width.
  Opening it once still stores the choice for that user, so it reopens on later
  visits for anyone who wants it.
- The toggle for the collapsed index is a tab docked in the gutter between the
  navigation rail and the page, aligned with the top of the page card and with
  the edge the drawer expands from. It steps below the rail handle when the
  navigation rail is collapsed, mirrors in RTL, and keeps its place at every
  width above 1200px.
- The tab is 40×40 with a visible focus ring, an accent edge on the side the
  panel opens from and the existing tooltip, so it reads as a control rather
  than as a stray button over the rail seam.
- The close control inside the open index is a bordered chip that matches the
  tab, and the panel carries the theme radius and shadow.
- `js/drawer.js` and `ajax/drawer.php` persist the open state as the
  `drawer-open-index` preference. Core writes that preference through a REST
  route that returns 500 on this build, so the theme writes it directly with the
  same name and both readers stay compatible.

### Fixed

- The course index toggle no longer straddles the navigation rail at wide
  widths, where half of it sat on the rail and half in the gutter.
- The toggle now exposes `aria-controls` and `aria-expanded` for the panel it
  opens.

## [1.6.0] — 2026-10-30

### Added

- A course hero on every `course-view` page: the category eyebrow, the course
  title, the summary, a metadata row (sections, activities, teachers) and a side
  panel with the completion ring, the primary call to action and the next
  activity. It replaces the stock page heading, which is hidden while the hero
  is present.
- `templates/course_hero.mustache` plus `theme_nadine_get_course_hero_context()`
  and `theme_nadine_get_course_teachers()`. The context only resolves on the
  course page, counts visible sections and activities, and reads the first
  visible activity for the call to action. Teacher names come from the role
  assignments, because this build has no `get_course_contacts()` helper.
- Section cards on the course page: numbered headers, a collapse chip, a
  tinted activity chip per purpose, a hover chevron and a per-row lift.

### Changed

- Section numbers are driven by a CSS counter on `li.section[data-number]` only.
  Counting the nested `.section` wrappers inside a section body used to shift
  every number by one.
- Empty sections no longer render a blank grey body. The check is anchored to
  `.section-item > .content` because the activity icon carries a literal
  `content` class.
- Section summaries use the real `.summarytext` markup instead of the
  `.summary` class that this build does not emit.
- Course copy keeps its own base direction (`unicode-bidi: plaintext`), so a
  Latin sentence inside an Arabic page no longer moves its full stop to the
  start of the line.
- Secondary navigation renders as a compact pill group sized to its content,
  covering both the legacy `nav-tabs` markup and Moodle 5's `mds-nav-pill`.

### Fixed

- Section titles were underlined in dark mode. Core underlines every
  `#region-main` link there; the title now opts out explicitly.
- Activity icon chips were clamped to 32px by core's `max-width` on the
  `.smaller` variant.

## [1.5.0] — 2026-10-30

### Added

- Live course search on the homepage hero and the course catalogue. Typing two
  characters opens a panel of matching courses with their category and summary;
  arrow keys move through the list, Enter opens the highlighted course, Escape
  closes it, `/` focuses the field and the clear button empties it. The panel
  reports its own state through `role="status"`, so screen readers hear the
  result count instead of a silent list.
- `theme/nadine/ajax/search.php`, a POST-only JSON endpoint. It requires a
  session key, answers 405 to anything that is not a POST, throttles a session to
  40 queries a minute, caps the query at 64 characters and runs every match
  through `get_courses_search()`, so hidden courses and hidden categories stay
  hidden. It returns plain text, never markup.
- `js/search.js`, plain ES5 with no AMD dependency, because the public front page
  has no `M.cfg` loader available. It writes results with `textContent`, aborts
  in-flight requests on a new keystroke and leaves the form fully working with
  JavaScript disabled.

### Changed

- Block header glyphs are now outlined chips: a 2rem square, 0.5rem radius, a
  7% accent wash and a `1px solid #D5D3D3` hairline on light surfaces. The glyph
  itself takes the block accent colour instead of white on a gradient tile.
  Dark mode swaps both tokens (18% wash, 22% light hairline) and lifts the glyph
  to 55% accent mixed with white, so the chip never reads as a glowing box.

## [1.4.1] — 2026-10-30

### Fixed

- The Arabic pack was missing 25 strings, so the homepage feature grid, the
  closing call to action, the site statistics labels and the rail/navigation
  labels fell back to English. Both packs now define the same 132 keys.
- Added `administration`, `collapserail`, `helpandsupport`, `primarycolor`,
  `primarynavigation`, `togglenavrail` and `toggleblockrail` to Arabic.

## [1.4.0] — 2026-10-30

### Added

- Docked primary navigation rail. From 1200px up the page sits between two
  columns of the same width (18rem), surface and header height: navigation on
  the left, blocks on the right. The rail carries the brand, a coloured glyph
  per destination, a "Management" group for administrators and a footer with
  site administration, support and the signed-in user. Below 1200px it is not
  rendered and the stock Boost navbar and primary drawer take over unchanged.
- Both rails collapse from a control in their own header. While a rail is
  collapsed its edge shows the glass handle, which slides back into place and
  fades in, so a seam never crosses a control and the page never carries a
  button it does not need.

### Changed

- The blocks panel gained a "Blocks" header that mirrors the navigation rail's
  header, and its surface is now the same white as the left column instead of
  the recessed canvas.
- The navbar is inset between the two rails rather than spanning the viewport,
  and it drops the brand and the primary links at desktop widths because the
  rail owns them.
- The course index drawer opens beside the navigation rail instead of on top
  of it.

## [1.3.0] — 2026-10-30

### Added

- Primary colour setting. `theme_nadine/primarycolor` drives Bootstrap's
  `$primary`, so buttons, links, active navigation, pagination and progress bars
  all follow one value. Empty means "inherit the accent colour", which is what
  an existing site already had. The input and focus-ring tints are derived from
  it instead of being hard-coded teal.
- Homepage counters count up from zero when they scroll into view, easing over
  1.5 s with `requestAnimationFrame` and formatting through `Intl.NumberFormat`
  in the page language. The markup ships the final figure, so the numbers are
  correct without JavaScript and under `prefers-reduced-motion`.

### Changed

- Homepage stat cells are now icon-led: a 2.7 rem chip tinted at 12 % of the
  cell's own colour, the figure in the display face, the label beneath. Hover
  raises the chip and washes the corner of the cell with 9 % of the same colour.
  Dark mode lifts the chip to 26 % and the glyph towards white.
- The rail handle no longer straddles the seam. It sits in the gutter 0.9 rem
  clear of it, so the divider can never cross the control, and it gained a glass
  surface (`backdrop-filter`), a two-layer shadow, an accent glow ring on hover,
  a press scale, and an animated chevron flip instead of a glyph swap.
- The rail divider is a gradient hairline that fades out at both ends instead of
  a full-height 1 px border, and the rail casts a soft edge shadow.

## [1.2.3] — 2026-10-30

### Changed

- Admin and settings trees are coloured per glyph, not per node type. Almost
  every row in an administration block is `type_setting`, so the node-type
  accents painted the whole tree the same slate. Each icon a user meets often
  (gear, users, user-plus, user-tag, user-lock, filter, charts, backup, restore,
  file, book, lock, shield, …) now has its own hue, 54 glyphs in total.
- Branch carets are a neutral `#94a3b8` so the coloured item glyphs carry the
  meaning and the tree structure stays quiet.
- One icon column (`1.25rem`) and one gap (`0.6rem`) for every row type. The
  activity monologo wrapper was inheriting core's 2 rem box, which left a 13 px
  hole between glyph and text; it is pinned to the shared column, and the row
  text uses the same gap as the tree rows instead of Bootstrap's `.ms-2`.

## [1.2.2] — 2026-10-29

### Changed

- Block item icons are now bare coloured glyphs instead of tinted tiles. The
  14% background chip and the gradient activity tile are gone; each row keeps
  its own hue and nothing else, so the list reads as text with colour accents
  rather than a column of badges.
- Activity monologos are tinted with per-purpose filter chains that resolve to
  the same palette as the rest of the theme (assessment `#ea580c`,
  collaboration `#7c3aed`, communication `#2563eb`, content `#0d9488`,
  interactive `#16a34a`, administration/other `#475569`). Core writes an inline
  `feColorMatrix` filter on those images, so the tint is applied with
  `!important`.

### Fixed

- Item glyphs were 32.8 px tiles that read heavier than the row text; they are
  now 1.35 rem (activity) and 1.1 rem (tree/link rows).

## [1.2.1] — 2026-10-29

### Added

- Rail handle: a visible control for the docked sidebar. It straddles the rail's
  inner edge while the rail is open, docks against the viewport edge when it is
  collapsed, and flips its chevron with the state. The state is a checkbox, so
  the control works without the drawer JavaScript.
- Per-item icons inside blocks. Activity rows get a solid tile coloured by their
  Moodle purpose (assessment, collaboration, communication, content,
  interactive, administration, other); navigation, settings and admin trees get
  a glyph per node type (course, activity, resource, section, category, user,
  system, my courses); plain link lists fall back to a chevron in the block's
  accent. Rows are full-width hover targets.
- The rail slides out and the page gutter closes when the handle is toggled.

### Changed

- The blocks drawer toggle is no longer removed at wide widths. The stock
  button is replaced by the rail handle rather than deleted, so the sidebar can
  always be collapsed.

## [1.2.0] — 2026-10-29

### Added

- Docked sidebar rail: at 1200px and wider the side blocks are taken out of the
  overlay drawer and shown as a permanent right column below the navbar. The
  rail is pure CSS over core's own markup, so the blocks stay reachable even
  when the drawer JavaScript does not run, and the page reserves the gutter only
  on pages that actually render side blocks.
- Block header band: every block is now a card with a full-bleed tinted header,
  a solid gradient icon tile with a white glyph, ghost header controls and
  hoverable navigation rows. The accent is declared once per block type and
  drives the band, the tile, the row hover and the focus ring.
- The block icon map grows to 45 types (up from 30), covering the full set of
  blocks shipped with Moodle 5.3 plus the admin, calendar and tagging blocks.
- Calendar block: fixed table layout, uppercase weekday labels and round day
  targets with an accent pill on today.
- "Add block" renders as a dashed drop target instead of a solid button.

### Changed

- The drawer chrome is hidden when the rail is docked, and the blocks toggle is
  removed at that breakpoint so it cannot open a second copy of the same blocks.

## [1.1.0] — 2026-10-28

### Added

- Homepage counters strip under the hero: courses, learners, categories and
  enrolments, every figure read from the database rather than invented.
- Homepage feature grid and a closing call-to-action band, so the public front
  page reads as a designed landing page instead of a block region. The CTA
  band inverts with the colour mode.
- Coloured icon tile on every block header, mapped by `data-block` for the 30
  most common block types, with a neutral fallback and a dark-mode brightness
  adjustment.
- Premium administration styling: the admin settings form becomes a surfaced
  card with hoverable setting rows, monospace setting short names and accent
  rules under section headings.
- The admin index (`/admin/search.php`) is presented as a grid of category
  cards with icon tiles and link chips instead of plain lists separated by
  horizontal rules. Common categories get their own icon and colour.
- Settings and navigation block links get rounded hover and accent active
  states.

## [1.0.1] — 2026-10-28

### Fixed

- The page canvas stopped at the bottom of the viewport, so every page taller
  than the screen showed a white band under the fold. The theme now paints the
  canvas colour on `html` as well as `body`.
- The front page, the catalogue and the dashboards were locked to Moodle's
  830px reading width while the navbar and footer used the 1240px design grid,
  which left the homepage content narrow and off-centre. Those layouts now use
  the theme container; prose layouts keep the narrower measure.
- Removed Boost's extra 3rem page gutter and the 15px `role="main"` inset so
  the navbar, page content, hero and footer share one set of edges.
- The hero applied the container width and gutter a second time inside
  `.main-inner`, which pushed the hero copy out of line with the navbar.
- The course cover in a `.coursebox` was capped at 100px by a core selector and
  collapsed to a strip; cards now show a 16:9 cover and more padding.
- The footer now sits outside `#page`, so it stays full width when a drawer
  shifts the page.
- The layouts never printed `standard_end_of_body_html`, so RequireJS was never
  loaded: every AMD module call failed with "requirejs is not defined", the
  course overview and timeline blocks stayed as skeletons and the drawer
  JavaScript never ran. Both layouts now print it.
- Added a bundled `pix/favicon.ico` built from the brand mark. Without it every
  page requested a favicon that did not exist and logged a 404.
- Core prints a second "Log in" link in the guest user menu next to the
  theme's own sign-in button; the duplicate is now hidden.
- The child theme re-introduced YUI's `cssbase` stylesheet that Boost empties
  out via `$THEME->yuicssmodules`. Its `input[type=text] { width: 12.25em }`
  outranks Bootstrap's `.form-control { width: 100% }`, which squeezed every
  text input on the site — worst on the login form, where the placeholders
  were cut mid-word inside a 196px input. The list is emptied again.
- The catalogue browse view renders the same `.coursebox` markup as the front
  page but core leaves it at zero padding, so the course title sat flush
  against the card's top border. The card padding rule now covers it.
- The secondary navigation (site home and course pages) is server-rendered as
  a plain link list and the moremenu drops the `nav-tabs` class at runtime, so
  core's tab chrome never applied: admins saw stray grey text on the canvas.
  It now sits in a bordered card with pill links and an accent active state.
- Version bumped to 1.0.1.

## [1.0.0] — 2026-10-27

First stable release for Moodle 5.3.

### Added

- Child theme of `theme_boost` with the full Moodle 5.3 layout set: `base`,
  `standard`, `course`, `coursecategory`, `incourse`, `frontpage`, `admin`,
  `mycourses`, `mydashboard`, `mypublic`, `login`, `popup`, `frametop`,
  `embedded`, `maintenance`, `print`, `redirect` and `secure`.
- Design system in `scss/nadine.scss` with light and dark tokens, typography,
  spacing, radii, shadows, motion and responsive rules.
- Public homepage hero built from real site data, with a working course search
  that posts to `/course/search.php`.
- Dashboard welcome band with the user's picture, name and real links.
- Course catalogue introduction using the real top-level categories.
- Split login page with branding panel, language selector, password visibility
  toggle and footer.
- Theme footer with three real link columns and a configurable description.
- Skip link and `#region-main` landmark on every page.
- Light, dark and system colour modes, with the choice applied before first
  paint.
- Right-to-left support through logical properties and mirrored directional
  icons.
- 107 translatable strings in English and Arabic, including settings
  descriptions.
- Administrator settings for branding, homepage, dashboard, appearance and
  custom SCSS.
- `theme_nadine` file areas for the logo, favicon, hero image and login
  image, served through a capability-checked plugin file callback.
- Optional AMD module for the password visibility toggle.

### Accessibility

- Single `h1` per page, verified across the page matrix.
- All images have `alt` text; decorative images are hidden from assistive
  technology.
- All form controls are labelled.
- Text contrast meets WCAG 2.2 AA, most of it AAA; form control boundaries meet
  the 3:1 non-text requirement.
- Reduced motion is respected.

### Performance

- No additional HTTP requests beyond the theme stylesheet that Moodle already
  serves.
- Compiled CSS is 1.37 MB uncompressed, 2.01% larger than Boost alone, and about
  236 KB gzipped.

### Known limitations

See `TESTING.md` for the full list. The main ones are that the release was
verified with HTTP-level and structural checks rather than a screenshot-based
visual regression suite, and that the reference instance runs on PHP's built-in
web server rather than a tuned production stack.
