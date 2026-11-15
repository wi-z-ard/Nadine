# Nadine design system

Nadine is a child of Boost and keeps the Moodle Design System underneath it.
The theme does not replace Bootstrap components; it changes the tokens, the
typography, the surfaces and the composition of a small number of pages.

## Principles

1. **Reading first.** Body text is 1rem with a 1.65 line height, a maximum
   measure of about 70 characters, and generous space between blocks.
2. **Quiet surfaces.** Warm paper tones in light mode, deep green-black in dark
   mode. No gradients, no glass effects, no decorative blobs behind content.
3. **One accent.** A single evergreen accent is used for actions, links, focus
   and selection. It is configurable and flows into Bootstrap through
   `--bs-*` variables.
4. **Real content only.** The hero, dashboard band, catalogue intro and footer
   are built from real site data and real Moodle URLs. No invented statistics.
5. **Motion is optional.** Durations are short and every transition is disabled
   under `prefers-reduced-motion: reduce`.

## Tokens

All tokens are CSS custom properties declared in `scss/nadine.scss`. The
dark values override the same names under `[data-bs-theme="dark"]`, which is the
attribute Moodle's colour mode feature sets on `<html>`.

### Colour

| Token | Light | Dark | Used for |
| --- | --- | --- | --- |
| `--nl-canvas` | `#f6f4ef` | `#0f1513` | Page background |
| `--nl-canvas-deep` | `#efece4` | `#0b100e` | Recessed areas, disabled fields |
| `--nl-surface` | `#ffffff` | `#151d1a` | Cards, blocks, inputs |
| `--nl-surface-muted` | `#fbfaf6` | `#19221f` | Secondary panels, table headers |
| `--nl-ink` | `#14201f` | `#eef3f0` | Body text |
| `--nl-ink-strong` | `#0b1412` | `#ffffff` | Headings, emphasis |
| `--nl-muted` | `#5f6a66` | `#a6b3ad` | Secondary text |
| `--nl-subtle` | `#7b8580` | `#8b9993` | Placeholders, metadata |
| `--nl-link` | `#485150` | `#a9b7b1` | Links inside blocks |
| `--nl-border` | `#ded9cf` | `#2a3531` | Decorative dividers |
| `--nl-border-strong` | `#cfc9be` | `#38443f` | Emphasised dividers |
| `--nl-border-control` | `#8f8a80` | `#6b7671` | Input, select and checkbox boundaries |
| `--nl-border-active` | `#5f6b68` | `#8b9993` | Pressed/selected outline on a light fill |
| `--nl-accent` | colour theme (`#ef5e00`) | derived | Decoration: dots, borders, washes, plates |
| `--nl-accent-action` | accent stepped to 4.5:1 (`#bf4b00`) | the dark accent | Fills and labels that carry text |
| `--nl-accent-strong` | darkened accent | lightened accent | Hover and active states |
| `--nl-accent-ink` | `#ffffff` | `#07110d` | Text on the accent |
| `--nl-primary-ink` | `#ffffff` | `#07110d` | Text on a primary fill |
| `--nl-highlight` | `#a8742c` | inherited | Small editorial accents |

`--nl-border-control` exists because decorative borders do not need to meet
WCAG 1.4.11, but the only visible boundary of a form control does. It is a
neutral warm grey, verified at 3.43:1 against the light surface, 3.12:1 against
the light canvas and 3.45:1 against the dark surface-muted panel. The hue is
deliberately neutral: the earlier green-grey read as a smudge at 1px next to the
cream canvas. Controls take a 6px radius, and a select's chevron is the Lucide
glyph drawn at 1rem — Bootstrap's arrow is a 16×16 drawing squeezed into 16×12,
which blurs the stroke.

`--nl-border-active` is the pressed edge for outline buttons, which sit on a
near-white `--nl-surface-muted` fill: a step deeper than the `#677c7b` hover
border (5.3:1 on that fill) so the state reads, but still grey. `--nl-ink` here
drew a near-black ring around a white button.

### Colour themes

The site picks one of eight curated palettes, or *Custom* to use the accent and
primary pickers. A theme is only an accent/primary pair: the warm paper, the
surfaces and the type are the theme's identity and do not move, so a theme
recolours the things that carry the brand and leaves the page recognisable.

| Theme | Accent | Primary | Character |
| --- | --- | --- | --- |
| Nadine | `#ef5e00` | `#01275a` | Default. Burnt orange on navy |
| Forest | `#2f7d4f` | `#123a2b` | Deep green on forest ink |
| Ocean | `#0e7490` | `#0b2e4a` | Teal on night blue |
| Plum | `#8e3b6b` | `#2b1033` | Aubergine on blackcurrant |
| Clay | `#b04a2a` | `#3a2318` | Terracotta on burnt umber |
| Slate | `#3f5ea8` | `#16233f` | Steel blue on graphite |
| Rose | `#b23a5b` | `#33101f` | Dusty rose on oxblood |
| Amber | `#a8681a` | `#3b2a0e` | Saffron on dark tobacco |

Everything else is derived at build time, which is why the table is two columns:
`--nl-accent-strong` is `mix(#000, accent, 14%)` and the dark mode accent is
`mix(accent, #fff, 38%)`.
The hero and login washes are `rgba($nl-accent, …)`, so they follow the theme
instead of staying orange. The pair is resolved in
`theme_nadine_get_accent_color()` / `theme_nadine_get_primary_color()`,
which both Bootstrap and the theme's tokens read, so one setting reaches buttons,
links, focus rings, active navigation, pagination, progress and the section
marks.

Contrast is checked per palette before it ships: white on the accent is at least
4.5:1 for every theme (Nadine itself is 3.35:1 and is the floor the others
beat), and the dark-mode accent against `#07110d` is at least 6.3:1. Measured
values are in TESTING.md.

### Type

| Token | Value |
| --- | --- |
| `--nl-font-body` | Platform UI stack, then Noto Kufi Arabic, then Tahoma |
| `--nl-font-display` | Georgia, then Noto Naskh Arabic, then Times |
| Body size / line height | `1rem` / `1.65` |
| Display scale | 2.4rem → 1.9rem → 1.5rem across breakpoints |

No web font is requested. Georgia is the one serif shipped on both Windows and
macOS, so headings land on the same face on every desktop instead of on
whichever of Iowan, Palatino or Georgia that platform happens to own. The Arabic
family is used when it is installed on the client, which keeps Arabic rendering
consistent without a third-party request.

Bootstrap's `h3` climbs with the viewport and pins at 1.75rem from 1200px up;
in the display face that step runs wide, so the theme pins it at 1.65rem.

### Space, radius, elevation

| Token | Value |
| --- | --- |
| `--nl-radius-sm` | `0.45rem` |
| `--nl-radius` | `0.75rem` |
| `--nl-radius-lg` | `1rem` |
| `--nl-radius-xl` | `1.4rem` |
| `--nl-shadow-sm` | `0 1px 2px rgba(20,32,31,.05)` |
| `--nl-shadow-md` | `0 12px 32px rgba(20,32,31,.08)` |
| `--nl-shadow-lg` | `0 24px 60px rgba(20,32,31,.12)` |
| `--nl-content-width` | `1240px` |
| `--nl-gutter` | `clamp(1rem, 4vw, 3rem)` |
| `--nl-duration` | `0.18s` |
| `--nl-ease` | `cubic-bezier(.2,.7,.2,1)` |

Radii stay below 1.5rem so cards and panels read as documents rather than
bubbles.

### Layout grid

The navbar, the page container, the hero and the footer all use
`--nl-content-width` with `--nl-gutter`, so their content edges match at every
breakpoint. Boost's fixed 3rem `#page` gutter and its 15px `role="main"` inset
are removed, because the theme container already keeps content clear of the
drawer toggles.

Moodle adds `body.limitedwidth` to reading layouts and caps them at 830px. The
theme keeps that measure for prose (a page, a book chapter) but opts the front
page, the catalogue, the dashboards and the course shell — course view, section
and activity — back into the 1240px grid. On a module page the page header and
the activity header are widened with it, because Boost pins each of them to its
own 830px measure and centres them. The previous/next bar is fixed to the
viewport and centred on it, so from 1200px up it is offset by the navigation
rail and starts on the page column instead, lining its buttons up with the card.

The footer is rendered outside `#page`, so it stays full width when a drawer
shifts the page to the right.

### Two-sided rail frame

From 1200px up the page is framed by two docked columns, both 18rem wide
(`$nl-rail-width`), both on `--nl-surface`, both with a 4.5rem header row
(`--nl-navbar-height`) closed by the same hairline:

| Column | Header | Body | Footer |
| --- | --- | --- | --- |
| Left | brand + collapse control | destinations with coloured glyphs, grouped | site administration, support, user |
| Right | "Blocks" + collapse control | block cards | — |

The navbar is inset between the columns instead of spanning the viewport, and
it gives up the brand and primary links because the rail owns them. Each column
carries a 1px seam that fades out at both ends, and each collapses from its own
header; the floating glass handle only appears once a column is collapsed, so
the seam can never cross a control. Below 1200px the rail is not rendered at
all and Boost's navbar and primary drawer behave exactly as before.

## Components

The stylesheet styles these groups on top of the Moodle Design System:

- **Brand** – the theme's own `pix/logo.svg` is the default mark: the monogram
  filled with an orange gradient (`#FFB46B` → `#F97316` → `#D2470C`) on a
  rounded plate washed with 10% orange, so it holds on the light bar and on the
  dark one. An uploaded logo replaces it. The same mark renders at 34px in the
  header and on the login page and at 38px in the footer, and the site name
  always follows it, so the image is decorative to assistive technology. The
  browser icon is the same monogram, shipped as a set rather than one `.ico`: a
  48/32/16 `favicon.ico`, a scalable SVG, a 96px PNG, a 180px Apple touch icon
  and a manifest with 192px and 512px maskable icons. The SVG is a real vector
  drawn from the logo's own path and gradient, so it stays 1.2 KB and stays
  sharp at any size; the raster sizes carry the white plate they need, so the
  mark reads at tab size without a second drawing.
- **Navigation** – translucent or solid bar, search field, language menu,
  colour mode switch, user menu, edit switch. The edit switch is core's
  design-system control; its checked track is restated with the accent
  (hover: accent-strong, thumb: accent-ink), because Boost otherwise paints it
  with the primary colour and the on state reads as an unrelated blue. The
  hairlines between controls are
  36px tall and fade at both ends rather than running the height of the bar. For
  a visitor the bar ends in a
  pill *Sign in* whose door glyph carries the accent while the label stays ink,
  so the one destination available to them reads as an action rather than as a
  third control beside the language selector; when self-registration is on, a
  filled *Create account* with a person-plus glyph follows it.
- **User menu** – core prints the menu as a list of destinations with no glyphs.
  Each item now carries a Lucide icon matched on its target path — profile,
  grades, calendar, private files, reports, preferences, language, switch role,
  site administration, log out — so the menu survives reordering, relabelling
  and plugin additions; anything unmatched keeps a neutral dot on the same
  baseline. Every glyph sits on a 1.6rem chip tinted with 11% accent and is
  drawn in the accent itself, so the column reads as a set of destinations
  rather than as grey text; hover deepens the chip to 18% and the glyph to the
  accent-strong. The panel is a card at 16.5rem with a hairline, rounded corners
  and the theme shadow, and its items take the surface-muted hover.
- **Colour mode** – a three-segment pill rather than a switch or a dropdown:
  sun, moon and system sit side by side in a 64px track, the active one on a
  raised surface chip with its glyph in the accent, the others muted. Each
  segment applies its mode directly; the theme overrides the Boost template and
  renders core's own `set-colourmode` options, so the existing module drives it
  and no dropdown is needed. Labels come through `title` and `aria-label`, and
  the group carries `role="radiogroup"`.
- **Notifications** – the bell toggle is a Lucide glyph rather than core's Font
  Awesome bell. The popover is a card, not a bare white box: a warm header band
  with the title in the darkest ink, circular icon buttons (mark all read,
  preferences, close) drawn from Lucide, a hairline above the footer, an accent
  *See all*, and a body capped at 22rem that scrolls. Unread rows carry a 6%
  accent tint and a heavier subject; the empty state sets a muted bell above its
  message. Core's fixed 500px height, 25px header and 30px footer are dropped,
  so the card hugs a short list instead of leaving an empty box under it. The
  mask sits on core's icon element itself, not on the toggle's `::before` —
  that pseudo-element is core's absolutely positioned pointer triangle — so the
  bell stays a normal item on the navbar's baseline. The panel drops at the
  theme's 4.5rem bar (72px), not core's 68px, so it starts under the header's
  border.
- **Hero** – two-column editorial block with an eyebrow, display heading,
  supporting text, real course search and a course count. It adds no block
  padding of its own; the rhythm above and below it belongs to `.main-inner`,
  so the hero starts where the rest of the page starts. The art column is
  top-aligned, which keeps the image on the headline's first line instead of
  letting a short image drift down to the middle of the copy.
- **Front page sections** – a counters strip (courses, learners, categories,
  enrolments, all read from the database), a continue strip for signed-in
  learners, a three-card feature grid and a closing call to action. The CTA band
  has its own pair of tokens — `--nl-cta-bg` and `--nl-cta-ink` — rather than
  borrowing the ink and canvas, so restyling body text or block links cannot
  repaint it; the pair inverts in dark mode. The continue strip is deliberately
  not a second
  catalogue: core already prints every available course further down the page,
  so the strip shows only the viewer's own enrolments, ordered by last access.
  Guests never see it, and it can be switched off in the *Homepage* settings.
- **Homepage motion** – two optional entrance treatments for the sections below
  the hero, chosen in the *Homepage* settings. *Reveal* is one quiet 1.25rem
  rise over 0.55s. *AOS-style* is a 2.25rem move over 0.8s on a decelerating
  curve, with per-element direction (`up`, `left`, `right`, `zoom`) and a 70ms
  stagger between items that share a parent, so a row of cards reads as one
  gesture. Both animate `opacity` and `transform` only, so nothing reflows;
  both are gated on a class the script adds before the page is parsed, so a
  blocked script leaves the page visible rather than blank; and
  `prefers-reduced-motion: reduce` removes the effect entirely. The hero is
  never animated — it is the largest paint on the page.
- **Course cards** – one component (`course_card.mustache`), used by the
  homepage continue strip and available to any future course surface, so a
  course looks the same wherever it is listed. A 16:9 media block on top — the
  course's featured image when it has one, read from core's `course_image`
  cache, and the generated pattern derived from the course id when it does not,
  so a card stays recognisable either way — with the category as a frosted chip
  in its top corner, then
  the course name, a progress row and a pill action. The progress row is a
  2.9rem ring that holds its own percentage, with an uppercase caption beside
  it; the arc is `--nl-accent` and the cut-out follows `--nl-surface`, so the
  ring sits on the card surface in both colour modes. The value is one inline
  `--nl-progress` number on the ring, and the ring carries the `progressbar`
  role while the visible figure and caption are `aria-hidden`, so the value is
  announced once.
  The whole card is
  one link. It lifts two elevation steps on hover, takes a 2px accent ring on
  keyboard focus, and mirrors in RTL. The row uses `auto-fit`, so two courses
  share the row instead of leaving an empty third column, and a lone course
  becomes a wide banner — media at 34% on the left, copy centred on the right —
  rather than one narrow tile beside two empty columns. That is the common case
  on a new site, where a learner has exactly one enrolment. In the banner the
  cover is out of flow, so a square fallback image cannot stretch the card.
- **Available courses** – core's front-page list, restyled rather than replaced,
  as a numbered catalogue. Each card prints its own entry number in a frosted
  ink plate on the cover — the number is the card's place in the list, so it is
  an index rather than an ornament, and it gives the grid a rhythm to scan. An
  open disc sits opposite it: the card is a single link, so the disc is painted
  and not a second target, and its arrow turns up-right on hover along with the
  plate and the leading-edge spine. The spine is a 1px hairline, not a band: at
  3px it read as a border and competed with the shadow that carries the lift, so
  the hovered card also takes `opacity: 0.95` and the two states read as one
  gesture. The teacher line is the footer, separated by
  a hairline; the title grows an accent rule underneath on hover, drawn as a
  background because core forces `text-decoration` on card links in dark mode.
  The section heading carries the theme's signature: an accent tick and a
  hairline that runs to the edge of the column.
- **Continue card** – the dashboard's answer to "what now". One course in a
  bordered tile: a 3.1rem completion ring, the name, the category and a resume
  action. Courses without completion tracking show a tinted disc with a book
  glyph rather than a misleading 0%. With no courses at all the panel becomes a
  catalogue prompt.
- **Dashboard course cards** – `block_myoverview`'s card grid, styled rather than
  replaced. The cover is 16:9 (core fixes it at 7rem, which letterboxes a 386px
  tile), the title is the display serif, the category is a small uppercase line,
  and the progress and action menu share one ruled footer. The card lifts 2px
  with a deeper shadow on hover and the cover zooms 1.04. Progress is a small
  ring beside a display figure and an uppercase caption — the same shape as the
  course hero's, both drawn from one `nl-progress-ring` mixin. The theme
  overrides `block_myoverview/progress-bar` so the dashboard's card, list and
  summary views get that ring too; in the two row views it stretches to a 26rem
  maximum instead of collapsing to the caption width. The ring carries
  `role="progressbar"` and the core "Progress" label, and the visible figure and
  caption are `aria-hidden` so the value is read once.
- **Progress rings** – the filled arc is `--nl-accent`, which clears the 3:1
  non-text minimum against the surface in every shipped palette (asserted by the
  test suite). The empty arc is the accent mixed into the surface — 12% for the
  7rem hero ring, 16% for the 2.9rem card rings, where a 4px arc needs a firmer
  track to stay legible. No gradient, gloss or animation: the caption and figure
  carry the value, so the arc is a redundant indicator.
- **Course browser** – core prints the category tree as nested names with a 12px
  tree glyph, so the catalogue page was a column of four links with the rest of
  the width empty. Each category is a card instead: the name (display serif) at
  the leading edge, a Lucide chevron at the trailing one. The chevron rotates
  and takes the accent when the panel is open, the header switches to the muted
  fill and gains a hairline rule, and the panel's courses — which arrive from
  `category.ajax.php` as compact rows — read as a list with a book glyph and a
  summary button. The two affordances keep core's split: the chevron toggles in
  place, the name opens the category page. `Expand all` is a pill in the same
  surface language. A category page lays its courses out two-up above 992px,
  because core leaves `.courses.category-browse` as a block and one card
  stretched across the full column strands its summary on the left.
- **Search** – one component, two sizes. The hero renders it at 34rem and the
  catalogue at 32rem; both are the same `search_form` partial, so the states
  cannot drift. The field is a pill with a focus ring that adds the accent
  border, a 0.2rem soft ring and one elevation step. Inside it sit the icon, the
  input, a spinner, a clear button and the submit button, in that DOM order, so
  the layout mirrors itself in RTL with no direction-specific rules.
- **Search results panel** – floats 0.45rem under the field on its own surface
  with `--nl-shadow-lg`. A status line announces the count, each row is an
  accent-washed `role="option"` with a 1.9rem icon chip, the course name and a
  two-line clamp of its category and summary, and a footer link appears only
  when more courses matched than fit. Mixed-direction copy uses
  `unicode-bidi: plaintext` so an English summary inside an Arabic page keeps
  its own order.
- **Blocks** – a card system, not a list of headings. Each block declares one
  accent in `--nl-block-accent`, mapped by `data-block` across 45 types (neutral
  fallback), and everything else derives from it: the header rule, the icon
  chip, row hover states and the focus ring. The header has no fill at all — it
  sits on the card surface with a `1px dashed` rule beneath it, so the block
  reads as one object instead of a header band stacked on a body. The chip is a
  2rem disc with a 7% accent wash and a hairline of the accent itself at 60%, so
  the ring and the glyph are one hue; dark mode lifts the wash to 18%. Header
  controls stay at 70% opacity until hover or focus. Blocks lift one elevation
  step on hover. Sidebar blocks take `--nl-radius-sm` (0.45rem) because a 1rem
  corner reads as a bubble around a 259px column, while blocks in the main
  column keep `--nl-radius-lg`.
- **Block items** – every row inside a block carries its own coloured glyph and
  no container. Activity monologos are tinted by Moodle's purpose taxonomy
  (assessment, collaboration, communication, content, interactive,
  administration, other) with per-purpose filter chains, since the SVGs are
  single-colour paths; tree rows take a glyph per node type (course, activity,
  resource, section, category, user, system, my courses) in that node's hue,
  and administration rows go further and colour the glyph itself, because
  almost every one of them is `type_setting` and would otherwise be one flat
  slate. Branch toggles are thin Lucide chevrons rather than Font Awesome's
  solid caret, so they read as structure instead of a filled marker, and they
  stay neutral grey so the coloured glyphs carry the meaning. Colour is the only
  decoration — no chips, no tiles — and every row type shares one icon column
  and one gap. The whole row is the hover target.
- **Sidebar rail** – from 1200px up the side blocks are docked as a fixed right
  column below the navbar instead of living in the overlay drawer, so they are
  visible without JavaScript. The rail is separated by a 1 px gradient seam that
  fades out at both ends rather than a full-height border, and it casts a soft
  edge shadow. A handle in the gutter, 0.9rem clear of the seam, collapses it:
  glass surface (`backdrop-filter`), two-layer shadow, accent glow on hover, a
  press scale and an animated chevron flip. A checkbox drives the state in CSS,
  so the control does not depend on the drawer module. The page reserves the
  gutter only on pages that render side blocks, and the gutter closes when the
  rail is collapsed.
- **Homepage counters** – each figure is led by a 2.7rem chip tinted at 12 % of
  that cell's colour, with the number in the display face and the label beneath.
  Hover raises the chip and washes the corner of the cell at 9 %. The number
  animates from zero the first time the strip is 35 % visible, and the markup
  carries the final figure so it is correct without JavaScript.
- **Colour settings** – two values, not one. The accent drives the theme's own
  surfaces; the primary is compiled into Bootstrap's `$primary` so buttons,
  links, active navigation, pagination and focus rings all follow it. Empty
  primary inherits the accent.
- **Administration** – the settings form becomes a surfaced card with hoverable
  setting rows and accent rules under section headings. The admin index is a
  list of category cards, each a 2.3rem circular tinted glyph tile beside the heading and
  a wrap of link chips; the heading and the chips are one grid row, so the
  heading column is the same width on every card whatever the link count. The
  root row, which has no heading and holds the site-wide actions, becomes a
  quiet toolbar across the full width instead of a card with an empty column.
  The tab bar above it carries a Lucide glyph per section and fills the content
  column so its edge lines up with the cards.
- **Theme selector** – the preview card is a 3:2 box instead of Boost's 7rem
  strip, so a screenshot is shown whole rather than sliced through its headline.
  The theme ships `pix/screenshot.jpg`; Moodle's `theme/image.php` accepts
  `svg`, `png`, `gif` and `jpg`, and ignores a `.jpeg`.
- **Dashboard welcome** – picture, greeting, and four real links. The avatar is
  36px, and the initials core substitutes for a learner without a profile
  picture are drawn at the same size and on the same surface token, so the band
  reads the same either way.
- **Catalogue intro** – heading, description and the real top-level categories.
- **Calendar** – `/calendar/view.php` is a single surfaced card across all four
  views. The view selector, course filter and new-event action share a toolbar;
  month and day navigation are pills with masked Lucide chevrons that mirror in
  RTL, around a centred display-serif month name. The month grid uses a quiet
  uppercase weekday rule on a recessed band, hairline cells at 8.25rem instead
  of core's fixed 11.5em, a wash on weekends and a tint on the hovered day.
  Today is an ink disc with an accent halo — white on the accent only reaches
  about 3.3:1, while ink clears AA in both colour modes. Events are tinted pills
  in the hue of their type (site green, course red-orange, category purple,
  group amber, user blue, other grey), read from the `data-event-eventtype`
  attribute core prints, with a 0.4rem dot and a two-line clamp so a 99px cell
  shows a name rather than a fragment. On phones the toolbar becomes two rows —
  selector and course filter together, the new-event action spanning the width
  below — and a day with events marks itself with a 4px accent dot instead of
  core's 3em period. Upcoming, day and week views render event cards with a
  coloured leading edge and a display-serif title. The "Events key" block gets
  the same hues as tinted swatches. The page is a workspace rather than a
  reading column, so it opts out of Boost's 830px measure and uses the whole
  area between the drawers; the grid simply gains wider cells.
- **Calendar block** – the same component at rail width, not a second design.
  Core renders the block with the page's own `.maincalendar` markup, so the page
  rules have to be restated rather than written twice: the empty toolbar band is
  removed, the arrows become 1.65rem discs with the month on its own centred row,
  cells tighten to 2.4rem with 1.7rem discs, the weekday band flattens to a rule,
  and the hover moves from the cell to the disc so it stays round. Today keeps
  the ink disc and accent halo, a day with events keeps the accent dot under the
  disc instead of core's 3em period, and the footer becomes two quiet rows with
  a trailing chevron instead of core's bullet-separated links. Event cards in the
  list carry their type colour as a dashed rule under the header rather than a
  bar down the inline start, so the title stays flush with the card edge and the
  mark mirrors with the text direction.
- **Messaging** – `/message/index.php` is an app frame, not a page of cards: the
  contacts rail and the conversation share one 1rem-radius surface that fills
  the viewport. Core asks for `height: 100%`, but every ancestor is auto, so the
  percentage resolved to auto and the app stopped at its 400px minimum, leaving
  the rail halfway down the screen. The rail is a step lighter than the page
  canvas and the empty conversation pane is a step darker, so the frame reads as
  three depths; the search field and its submit button share one rounded box with an accent
  `:focus-within` ring, the settings gear and the conversation's back control
  are round ghost targets, and "Contacts" is a pill. The three overview
  sections are uppercase micro-labels separated by hairlines instead of three
  stacked cards with filled header bands. Contact rows take `--nl-radius-sm`
  with a warm wash on hover — core paints them solid primary with white text,
  which reads as a selection — and the section toggles take an inset accent ring
  on focus, because the browser's ring is drawn outside the button, clipped by
  the header, and surfaced as a black rule under the label. Sent bubbles are
  `--nl-primary` with `--nl-primary-ink`, received bubbles are
  `--nl-canvas-deep`, and the CSS-triangle tails are gone. On phones the 4/8
  split becomes one pane at a time: the rail until a conversation is opened,
  then the conversation with its back control.
- **Message drawer** – the navbar's message icon opens the same application at
  20rem, so it is described against both mounts (`$nl-msg`) rather than styled
  twice. The drawer adds only the chrome the page does not have: a quiet close
  disc with an even 0.5rem margin, one padding and one hairline for every
  header band (overview, contacts, search, settings, conversation), round ghost
  back and search controls, a segmented control for Contacts/Requests, the
  settings groups as the same uppercase micro-labels, and "See all" as a
  hairline footer link. The overview list takes the rail's tone so the drawer
  and the page read as one product. Section carets are masked Lucide chevrons,
  matching the administration tree. It sits on the header's line rather than on
  core's own navbar constant, which is 4px shorter here, and while it is open
  the header band runs to the right edge of the viewport: the docked blocks
  column would otherwise leave a carved-out corner above the drawer holding its
  own heading and collapse control. The band is translucent, so the column's
  head is hidden with the rest of it, and the header inner takes the column
  width back as padding so nothing inside the band moves.
- **Notifications page** – `/message/output/popup/notifications.php` is the same
  product as the popover at full width: one card with a warm rail of padded
  rows and a detail pane beside it. Core's 300px inline-block list and floated
  detail pane become a 20rem grid column and a flexible pane, so the two halves
  share one border, radius and shadow instead of sitting in a plain box. Unread
  rows take a 6% accent tint plus an accent dot on the subject (the tint alone
  disappears against the rail), the selected row lifts to the surface with a
  3px accent edge, and focus is drawn as an inset ring on the row rather than a
  box around the inner link. The detail pane carries a header band with the
  subject and timestamp, a 1.6 line-height body, a tinted footer link, and a
  centred empty state with the bell. Below 980px core's sliding panes are kept.
- **Course page** – the stock page heading is replaced by a full-width hero band
  that bleeds through the content gutter: a category eyebrow, the course title
  in the display face, the summary, a metadata row (sections, activities,
  teachers) and a 19rem panel holding the completion ring, the primary call to
  action and the next activity. The band is hidden entirely when the context
  cannot resolve (site home, non-course pages). Below it, each section is a card
  with a numbered header (`01`, `02`, … from a CSS counter on
  `li.section[data-number]`), a collapse chip, a tinted activity chip per
  purpose, a hover chevron and a one-pixel lift per row. Empty sections collapse
  to their header. Section cards are borderless: the surface and the soft shadow
  carry the edge, the header band keeps the card's top corners and drops its
  bottom rule, and the card no longer clips its overflow — the section action
  menu and the Add content menu open inside a section and were being cut off at
  its edge. Both menus take the surface panel and chip treatment described under
  Course management, with one hue per action: View blue, Edit settings amber,
  Highlight rose, Duplicate violet, Hide slate, Move teal, Assign roles green,
  Group mode indigo, Permalink steel, Delete danger, Activity or resource the
  accent and Subsection violet. Items sit inset from both menu edges: the theme
  sets `width: auto` because Bootstrap's `width: 100%` is measured against the
  menu's content box and ignores the inset margins, which left every item
  hanging past the right padding. The secondary navigation is a pill group on a card that spans
  the content column, so its edges line up with the sections below; core's 830px
  centring of the tab list is lifted. Inside the card the tab rail sits on its
  own inset strip — a near-white band in light, a shade below the surface in
  dark — so the pills read as one grouped control rather than as loose labels on
  the card. It covers both the legacy `nav-tabs` markup and Moodle 5's
  `mds-nav-pill`.
- **Course index** – collapsed on load, so the course takes the full width until
  the reader asks for the index. The toggle is a 40×40 tab docked in the gutter
  between the navigation rail and the page, level with the top of the page card
  and with the edge the panel expands from; it steps below the rail handle when
  the navigation rail is collapsed and mirrors in RTL. Opening the index stores
  the choice for that user. From 1200px up the panel is a full-height column,
  not a floating card: it runs from the top of the viewport to the bottom edge
  with no rounded corners, so its surface continues behind the translucent
  navbar and the page edge stays continuous. The body is the theme surface, the
  header is a warm band with the course name in the darkest ink, and a hairline
  seam closes the page side. The panel keeps the theme's row treatment: accent
  bar on the current section, accent wash on the current activity, and the same
  purpose-tinted glyphs as the course body. The current page is one pill — a 14%
  accent tint, a 36% accent hairline and the darkest ink — and the link and
  chevron colours are set with it rather than inherited, because core keeps a
  white foreground for an item it fills with the primary colour. The pill
  survives interaction: core repaints the selected row with the primary colour
  on hover and focus, so the hover, focus and focus-within states restate the
  tint one step stronger (18% fill, 45% hairline) and hold the label on ink; the
  label deepens towards the accent only when pointed at directly, through a mix
  that keeps 25% ink so it clears 4.5:1 on the tint. Completion is
  one 8px marker — a filled disc for done or failed in the semantic colour, a
  hairline ring for to do — so core's Font Awesome circle and SVG image finally
  match.
- **Login** – split layout with the branding panel on one side and the Moodle
  login form on the other, plus the language selector and a footer. The panel is
  a printed surface with its own small palette — warm paper, ink copy, and an
  illustration that fills the panel behind them. The legibility scrim is an
  ellipse anchored at the top-left corner, not a full-width band: opaque under
  the copy, dissolving towards the right edge and the foot, so the drawing reads
  from about halfway up instead of being flattened under a sheet of white. An
  accent wash from the same corner and a faint diagonal veil settle the art into
  the paper. Six `--nl-panel-*` tokens carry the paper, ink, body, note, icon and
  eyebrow colours; dark mode re-points them at the dark surface and holds the
  illustration at half opacity, so the panel joins the form column as a texture
  rather than sitting beside it as a bright slab. Three one-line statements sit
  under the intro as a
  checklist; the longer descriptions stay in the markup for screen readers. The
  panel repeats no brand the top bar already shows. The form is not a card: it
  sits directly on the canvas, so the two halves read as one composition. Only
  the fields, the divider and the primary button carry structure — hairline
  fields that take an accent ring on focus, the recovery link pulled up beside
  the password field, and a single
  forward mark on the primary button. The password control is core's own
  `toggle-sensitive` button, dressed by the theme — the glyph is a Lucide mask
  that follows the field's type, so it can never disagree with what the field
  shows. Below the split breakpoint core removes the panel outright, so it
  returns as a slim banner with the eyebrow and the headline — the phone keeps
  its identity instead of showing a bare form.
- **Profile** – the public profile is core's page header and myprofile tree, so
  the theme works with the markup it is given. The header (picture, name,
  message button) and a strip of facts — department and institution, city and
  country, month joined — share one band: a surfaced card with the accent glow
  in its corner, an 80px picture inside a hairline ring, and the facts under a
  single divider. The strip is built from the user record and filtered against
  the site's hidden profile fields, so it never reveals more than the tree. The
  tree itself becomes a responsive grid of cards: each heading is a small
  uppercase label with an accent tick, detail rows are a two-column definition
  grid with hairline separators, and a row that carries a list gives the list
  the full width, so course names read as chips rather than a column of links.
  On a phone the definition rows stack and the grid falls to one column.
  The edit action belongs to the band, at the far end of the facts row, and
  follows the same capability and authentication-plugin rules core applies
  before it adds the node to the tree; the card keeps a quiet row only for a
  viewer the band does not cover. The page opts out of the 830px reading width,
  as the site home and the dashboards do, so the grid uses the whole container.
- **Edit profile** – `/user/editadvanced.php` is core's mform: five collapsible
  fieldsets in a 3/9 label-control split, each control sized by its own markup,
  and the save bar fixed to the bottom of the viewport. It is restyled rather
  than rebuilt, because the field list is whatever the administrator enables.
  Each section becomes a surfaced card with the display serif heading and its
  position in the form (`01`–`05`) set in small muted type; the chevron is a ring
  on the header, which stays one big target because core's stretched link still
  covers it. Fields move to a two-column grid with the label above its control,
  full-width for anything that contains an editor, a file manager, a textarea or
  an autocomplete — matched with `:has()` rather than field ids, so a site with
  extra profile fields still lays out correctly. The password policy note hangs
  off a hairline instead of sitting in the label column. The save bar keeps
  core's fixed positioning and `hasstickyfooter` slide-in, but takes the page
  surface instead of `bg-white` (a dark grey in the dark palette) and a hairline
  with a soft shadow, so it reads as a shelf under the form. The page widens to
  72rem, and core's duplicate `<h2>` above the form is hidden because the page
  header already carries the name.
- **Preferences** – `/user/preferences.php` is four link groups in a Bootstrap
  row of three columns, so the fourth wrapped under the first and the page sat
  in an 830px measure inside a 1137px area. The groups are re-laid as two
  balanced columns of cards that take the full content width, balanced with CSS
  columns rather than a grid: the longest group is four times the shortest, and
  a grid would stretch the short cards to the height of "User account". Each
  link is a full-width row that takes a wash and a trailing chevron on hover, so
  the list reads as navigation. Below 768px it is one column.
- **Navigation rail** – the docked primary navigation has two states. Expanded
  it is an 18rem column with glyph and label; collapsed it is a 4.9rem icon
  strip, so navigation stays one click away instead of sliding off-canvas. The
  rail width, the page gutter, the navbar inset and the seam all animate on the
  same curve, and the labels are clipped rather than removed so each icon keeps
  its accessible name and `title` tooltip. The choice is a user preference, so
  the frame does not spring open on the next page.
- **Dialogs** – `.moodle-dialogue` is clipped to a 0.7rem radius, and its
  header carries an 8% tint of the palette accent over the surface colour, so a
  confirmation reads as part of the theme rather than as a browser prompt. The
  wrapper inherits the radius so no square hairline shows inside the shell;
  fullscreen dialogues are excluded to keep the file picker scrollable.
- **Breadcrumb trail** – core prints the trail as running text separated by
  slashes, which loses the page title beside it. Each step is a padded target,
  the separator is the theme's chevron, and the current page is a hairline chip
  with a marker at its head and the darkest ink in the trail. The chip is not a
  link, because the page you are on is not a destination. The chevron mirrors in
  RTL.
- **Collapse / expand all** – core offers the control twice, as a bare text link
  above an mform and a button above the course sections, and both only swap
  their label. They share one pill: a hairline border, the fold glyph, and a
  label that names the next action. On forms the label follows the first open
  section rather than waiting for every section to agree, so a form that opens
  with some sections expanded offers *Collapse all* and means it; the theme's
  script reads the sections on each click instead of trusting a class core only
  rewrites after every transition has finished. A section behind *Show more* is
  switched directly, because a hidden container cannot run a height transition
  and Bootstrap would leave it stuck mid-way.
- **Reports** – core prints `/report/view.php` as a bare list of links inside a
  fluid container. The theme's template override renders the same navigation
  node as a grid of cards: the report's glyph in a tinted chip, the plugin's own
  title, a one-line summary, and a chevron that leans in on hover. A tinted rule
  marks the leading edge at rest — a fifth of the card's height at 30% — and
  brightens to full strength on hover and keyboard focus. The whole
  card is the hit area, the tint map is keyed by the report's URL, and a report
  a plugin adds without a mapping still renders in the neutral tone, without a
  summary, rather than being dropped. The page opts out of the 830px reading
  width so the grid can run to four columns.
- **Course management** – `/course/management.php` is three cards of list rows.
  Core prints their titles as bare `h3` elements, so they inherited the
  page-heading scale and sat at 1.75rem in a column that can be 300px wide;
  they are 1.3rem now, in the display face with the tracking of a list label.
  Each card cap takes a 0.9rem × 1.15rem inset instead of core's 0.4rem ×
  0.6rem and an accent tick before the title, so the three columns read as one
  family. The detail card's name sits in the same cap and carries the same tick.
  The two listing panes run their body text at 0.95rem. Their key/value rows
  drop core's `col-md-3` label column — at pane width that column is 120px and
  split "Move selected courses to…" over four lines — for a wrapping row whose
  label keeps its natural width and never breaks. Each pane closes with a
  hairline above its bulk actions. The per-row action menu is a rounded surface
  panel with 0.9rem labels, every glyph in an accent chip that deepens on hover,
  and the Delete entry in the danger tint.
- **Front-page courses** – core prints the available courses as wide rows, a
  title above a thumbnail beside the summary. Next to the tile strips above and
  below it that reads like a directory, so the same markup becomes a tile grid:
  cover across the top, title, summary clamped to three lines, then the
  teachers behind a person mark. The clamp is what keeps a row of cards level.
- **Footer** – the colophon, and the one dark surface in light mode. The
  identity, tagline and description sit in the widest column; the three link
  columns each open with an accent tick; the social row is a line of hairline
  discs that fill with the accent on hover. A single hairline closes the page,
  carrying the accent from the reading edge. In dark mode the surface lifts one
  step off the canvas instead of inverting, so the page does not end on a flash
  of paper. Only the networks with an address are rendered.
- **Loading indicator** – core's `core/loading` template is overridden, so the
  Font Awesome dot sprite is gone. Every AJAX spinner is now one ring: a conic
  gradient that fades from transparent to the active accent, masked down to a
  hairline, so it re-tints with the colour theme and reads on the canvas and on
  a card. Inside a filled button the ring follows the label colour instead,
  because the accent there is the background. The overlay wash uses the themed
  surface with a small backdrop blur rather than core's flat white, which
  flashed in dark mode. The "Loading" label stays in the accessibility tree.
  Two paths bypass that template and are covered separately: a tree branch in
  the administration or navigation block, which core marks with a
  `i/loading_small` background image while it fetches its children, turns its
  caret chip into the ring; and any `i.icon.fa-spinner` printed by
  `pix_icon('i/loading')` in admin screens is repainted as the ring.
- **Forms, tables, alerts, modals, pagination, badges, progress** – inherited
  from Bootstrap with the theme's tokens applied.

## Icons

Three sets, split by who owns the markup.

- **Lucide** for everything the theme renders itself: the navigation rail, the
  drawer handles and close controls, the hero, the statistics strip, the
  feature grid, the dashboard quick links, the course header and the search
  field. The SVGs are inlined from `pix/lucide/` by
  `theme_nadine_icon()`, which adds `nl-icon` plus the caller's class, a
  pixel size and `aria-hidden="true"`. They are 24×24 on a 2px round stroke and
  inherit `currentColor`, so every existing colour rule keeps working.
- **Brand marks** in `pix/social/`, for the footer's social row. They are filled
  24×24 paths rather than strokes, because a network's mark is its logo and not
  an outline of one; they are inlined through the same helper with the `social`
  set, so they also take the link's colour.
- **Font Awesome** for everything the theme does not own: core and plugin
  `pix_icon` calls, activity module icons, and the glyphs core prints into its
  own markup (block controls, tree carets, activity chevrons). `$THEME->iconsystem`
  stays on `FONTAWESOME` for that reason.

`.nl-icon` sizes itself from `font-size` (`width: 1em`), so a container only
sets a font size and the glyph follows, the way Font Awesome did. A 14px icon
therefore draws a 1.17px stroke, which matches the hairline borders around it.
Directional glyphs take `nl-directional` and mirror under `[dir="rtl"]`; the
set is chosen so that non-directional icons never need mirroring.

### Glyphs on markup the theme does not render

The administration tab bar is a React island and the settings categories are
built from navigation nodes, so neither can carry an inlined SVG. Both are
given one in CSS instead: a `::before`/`::after` box sized in rem, filled with
`currentColor` and masked by the same Lucide file through
`mask-image: url([[pix:theme|lucide/…]])`. `[[pix:…]]` is resolved by
`theme_config::post_process()`, so the URL carries the site's own theme
revision and the theme can be installed at any path.

The icon is keyed on what core already renders — `#link<key>` for the tabs,
`category=<slug>` for the headings — which keeps the rule set free of any
template override and lets a plugin's category fall back to a default glyph.
Because the mask is filled with `currentColor`, the icon follows hover,
selected and dark-mode colours with no second asset.

The same technique draws the breadcrumb chevrons and its current-page marker,
and the fold glyph on the two collapse-all controls. Those glyphs belong to
markup core builds, so they are masks on `::before` rather than inlined SVGs;
the mask box is sized in rem so it tracks the label's font size.

## Right-to-left

The stylesheet uses logical properties throughout:

- `margin-inline`, `padding-inline`, `inset-inline`, `border-inline-start`.
- Icons that encode direction (arrows, chevrons in navigation) carry the
  `nl-directional` class and are mirrored with `[dir="rtl"] .nl-directional`.
- Non-directional icons (search, user, bell) are never mirrored.
- The login split layout swaps sides automatically because it is built with
  grid and logical placement.
- Course copy is user content and may not match the interface language, so the
  course hero summary, section summaries and activity names carry
  `unicode-bidi: plaintext`. A Latin sentence inside an Arabic page then keeps
  its own base direction and its full stop stays at the end of the line; the
  copy is still aligned to the right edge in an RTL page.

Arabic is a first-class language in the theme: `lang/ar` contains the same 158
strings as `lang/en`, including the settings descriptions.

## Dark mode

- The palette is defined once per mode under `:root` and
  `[data-bs-theme="dark"]`.
- Bootstrap variables (`--bs-body-bg`, `--bs-body-color`, `--bs-border-color`,
  `--bs-card-bg`, and the dropdown, modal and table variants) are bridged to the
  theme tokens, so core components follow without per-component overrides.
- Moodle writes `data-bs-theme` on `<html>` on the server for Light and Dark,
  and an inline script resolves `System` before the first paint, so there is no
  flash.

## Accessibility

- Focus is always visible: a 2 px accent outline with a 2 px offset. Two
  exceptions exist because the offset does not suit the shape. Pills and chips
  (`#linkroot` link chips) put the ring on the border instead, where the 1 px
  border and 2 px outline merge into one 3 px edge that follows the radius. And
  core's own focus treatment for unclassed, activity and arrow links — a blue
  fill with a bar above and below, drawn for inline text and oblivious to
  border radius — is replaced everywhere by a change of text colour to the
  strong accent. The label alone marks focus, with no fill, bar or reserved
  outline, so nothing rectangular is drawn around a shape it does not fit.
- The skip link is the first focusable element and targets `#region-main`,
  which is also `tabindex="-1"` so focus moves correctly. Core's own skip link
  points at an empty span at the top of the body, so the theme drops that
  duplicate and keeps only the link that reaches the main landmark.
- Headings follow the page hierarchy; the theme never introduces a second `h1`
  on a page.
- Decorative images use empty `alt` and `aria-hidden`; informative images use
  the description configured in the settings.
- All text colours meet WCAG 2.2 AA; most meet AAA. See `TESTING.md` for the
  measured ratios.
- Motion is disabled under `prefers-reduced-motion`.
