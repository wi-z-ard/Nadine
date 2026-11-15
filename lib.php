<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme functions for Nadine.
 *
 * Nadine is a child theme of Boost. It keeps every Moodle component,
 * layout and JavaScript module intact and changes the presentation through
 * SCSS, a small number of template overrides and a few renderer helpers.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Render one of the theme's own icons as an inline SVG.
 *
 * The theme only owns the chrome it renders itself - the navigation rail, the
 * drawer handles, the marketing sections and the quick links. Those use Lucide
 * because it is a stroke set that matches the hairline borders and 1px rules
 * used everywhere else, and because inlining keeps them on `currentColor` so
 * the existing colour rules keep working.
 *
 * Moodle's icon system stays on Font Awesome. Every core and plugin `pix_icon`
 * still resolves through it, and the icons that core prints into markup the
 * theme does not own (block controls, tree carets, activity chevrons, admin
 * category tiles) keep their Font Awesome glyphs.
 *
 * @param string $name File name in the set, without the .svg extension.
 * @param string $class Extra CSS classes appended to `nl-icon`.
 * @param int $size Rendered width and height in pixels.
 * @param string $set Sub-directory of pix holding the set, `lucide` or `social`.
 * @return string SVG markup, or an empty string when the icon does not exist.
 */
function theme_nadine_icon(string $name, string $class = '', int $size = 20, string $set = 'lucide'): string {
    static $cache = [];

    $set = $set === 'social' ? 'social' : 'lucide';
    $name = basename($name);
    $key = $set . '/' . $name;
    if (!array_key_exists($key, $cache)) {
        $file = __DIR__ . '/pix/' . $set . '/' . $name . '.svg';
        $cache[$key] = is_readable($file) ? trim(file_get_contents($file)) : '';
    }

    if ($cache[$key] === '') {
        return '';
    }

    $classes = trim('nl-icon nl-icon--' . $set . ' ' . $class);
    $size = max(8, min(96, $size));
    $attributes = 'class="' . s($classes) . '" width="' . $size . '" height="' . $size
        . '" aria-hidden="true" focusable="false"';

    // Positional replace rather than a regex: the SVG body may contain
    // characters that are special in a replacement string.
    $position = strpos($cache[$key], '<svg');
    if ($position === false) {
        return '';
    }

    return substr_replace($cache[$key], '<svg ' . $attributes, $position, 4);
}

/**
 * Return the main SCSS content.
 *
 * The Moodle Design System preset shipped with Boost is used so that every
 * core component keeps its expected structure and the theme stays compatible
 * with future Moodle releases.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_nadine_get_main_scss_content($theme): string {
    global $CFG;

    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}

/**
 * Return SCSS variables which are prepended to the Moodle Design System.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_nadine_get_pre_scss($theme): string {
    $accent = theme_nadine_get_accent_color($theme);
    $accentdark = theme_nadine_mix_color($accent, '#ffffff', 38);
    $accentaction = theme_nadine_action_color($accent);
    $primary = theme_nadine_get_primary_color($theme);
    $primarydark = theme_nadine_mix_color($primary, '#ffffff', 38);

    $scss = '';

    // Typography. These are locally available families; the theme never loads
    // a font from a third-party CDN.
    $scss .= '$nl-accent: ' . $accent . ';' . "\n";
    $scss .= '$nl-accent-dark: ' . $accentdark . ';' . "\n";
    $scss .= '$nl-accent-action: ' . $accentaction . ';' . "\n";
    $scss .= '$nl-primary: ' . $primary . ';' . "\n";
    $scss .= '$nl-primary-dark: ' . $primarydark . ';' . "\n";
    $sans = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, '
        . '"Noto Sans", "Noto Kufi Arabic", Tahoma, sans-serif';
    $serif = 'Georgia, "Noto Naskh Arabic", "Times New Roman", serif';
    $scss .= '$font-family-sans-serif: ' . $sans . ';' . "\n";
    $scss .= '$font-family-base: $font-family-sans-serif;' . "\n";
    $scss .= '$headings-font-family: ' . $serif . ';' . "\n";
    $scss .= '$headings-font-weight: 600;' . "\n";
    $scss .= '$headings-line-height: 1.14;' . "\n";
    $scss .= '$headings-color: #14201f;' . "\n";
    $scss .= '$font-size-base: 1rem;' . "\n";
    $scss .= '$line-height-base: 1.62;' . "\n";

    // Palette.
    $scss .= '$primary: $nl-primary;' . "\n";
    $scss .= '$secondary: #5f6a66;' . "\n";
    $scss .= '$success: #2e6f4e;' . "\n";
    $scss .= '$info: #2c6e7f;' . "\n";
    $scss .= '$warning: #96621a;' . "\n";
    $scss .= '$danger: #a33a3a;' . "\n";
    $scss .= '$light: #f6f4ef;' . "\n";
    $scss .= '$dark: #14201f;' . "\n";

    $scss .= '$body-bg: #f6f4ef;' . "\n";
    $scss .= '$body-color: #14201f;' . "\n";
    $scss .= '$body-secondary-color: #5f6a66;' . "\n";
    $scss .= '$body-secondary-bg: #ece8e0;' . "\n";
    $scss .= '$body-tertiary-bg: #f1eee8;' . "\n";
    $scss .= '$body-emphasis-color: #0d1715;' . "\n";
    $scss .= '$border-color: #d9d4ca;' . "\n";
    $scss .= '$border-color-translucent: rgba(20, 32, 31, 0.12);' . "\n";
    $scss .= '$link-color: $primary;' . "\n";
    $scss .= '$link-hover-color: mix(#000000, $primary, 22%);' . "\n";
    $scss .= '$link-decoration: none;' . "\n";
    $scss .= '$link-hover-decoration: underline;' . "\n";

    // Surfaces.
    $scss .= '$border-radius: 0.55rem;' . "\n";
    $scss .= '$border-radius-sm: 0.4rem;' . "\n";
    $scss .= '$border-radius-lg: 0.85rem;' . "\n";
    $scss .= '$border-radius-xl: 1.15rem;' . "\n";
    $scss .= '$border-radius-pill: 50rem;' . "\n";
    $scss .= '$card-border-radius: 0.85rem;' . "\n";
    $scss .= '$card-bg: #ffffff;' . "\n";
    $scss .= '$card-border-color: #ded9cf;' . "\n";
    $scss .= '$card-cap-bg: transparent;' . "\n";
    $scss .= '$card-inner-border-color: #ece7dd;' . "\n";

    // Forms.
    $scss .= '$input-bg: #ffffff;' . "\n";
    $scss .= '$input-border-color: #cfc9be;' . "\n";
    $scss .= '$input-focus-border-color: $primary;' . "\n";
    $scss .= '$input-focus-box-shadow: 0 0 0 0.2rem rgba(red($primary), green($primary), blue($primary), 0.16);' . "\n";
    $scss .= '$input-placeholder-color: #7b8580;' . "\n";
    $scss .= '$form-label-font-weight: 600;' . "\n";
    $scss .= '$form-text-color: #5f6a66;' . "\n";

    // Focus, buttons.
    $scss .= '$focus-ring-color: rgba(red($primary), green($primary), blue($primary), 0.3);' . "\n";
    $scss .= '$focus-ring-width: 0.2rem;' . "\n";
    $scss .= '$btn-font-weight: 600;' . "\n";
    $scss .= '$btn-padding-y: 0.6rem;' . "\n";
    $scss .= '$btn-padding-x: 1.1rem;' . "\n";
    $scss .= '$btn-border-radius: 0.55rem;' . "\n";
    $scss .= '$btn-border-radius-sm: 0.45rem;' . "\n";
    $scss .= '$btn-border-radius-lg: 0.7rem;' . "\n";

    // Menus and overlays.
    $scss .= '$dropdown-bg: #ffffff;' . "\n";
    $scss .= '$dropdown-border-color: #ded9cf;' . "\n";
    $scss .= '$dropdown-border-radius: 0.7rem;' . "\n";
    $scss .= '$dropdown-link-color: #14201f;' . "\n";
    $scss .= '$dropdown-link-hover-bg: #eef4f1;' . "\n";
    $scss .= '$dropdown-link-hover-color: #0d1715;' . "\n";
    $scss .= '$dropdown-link-active-bg: $primary;' . "\n";
    $scss .= '$dropdown-link-active-color: #ffffff;' . "\n";
    $scss .= '$dropdown-divider-bg: #e7e2d8;' . "\n";

    $scss .= '$modal-content-bg: #ffffff;' . "\n";
    $scss .= '$modal-content-border-color: #ded9cf;' . "\n";
    $scss .= '$modal-content-border-radius: 0.85rem;' . "\n";
    $scss .= '$offcanvas-bg-color: #ffffff;' . "\n";
    $scss .= '$popover-bg: #ffffff;' . "\n";
    $scss .= '$popover-border-color: #ded9cf;' . "\n";
    $scss .= '$popover-border-radius: 0.7rem;' . "\n";

    // Tables.
    $scss .= '$table-color: #14201f;' . "\n";
    $scss .= '$table-border-color: #e4dfd6;' . "\n";
    $scss .= '$table-hover-bg: rgba(13, 92, 77, 0.05);' . "\n";
    $scss .= '$table-striped-bg: rgba(20, 32, 31, 0.025);' . "\n";

    // Navigation, progress, pagination.
    $scss .= '$nav-link-font-weight: 600;' . "\n";
    $scss .= '$nav-tabs-link-active-color: #14201f;' . "\n";
    $scss .= '$nav-tabs-link-active-border-color: #f6f4ef #f6f4ef $primary;' . "\n";
    $scss .= '$pagination-border-radius: 0.5rem;' . "\n";
    $scss .= '$pagination-active-bg: $primary;' . "\n";
    $scss .= '$pagination-active-border-color: $primary;' . "\n";
    $scss .= '$progress-bg: #ece8e0;' . "\n";
    $scss .= '$progress-bar-bg: $primary;' . "\n";
    $scss .= '$badge-font-weight: 600;' . "\n";
    $scss .= '$breadcrumb-divider-color: #7b8580;' . "\n";
    $scss .= '$breadcrumb-active-color: #5f6a66;' . "\n";
    $scss .= '$breadcrumb-border-radius: 0;' . "\n";
    $scss .= '$grid-gutter-width: 1.5rem;' . "\n";
    $scss .= '$enable-shadows: false;' . "\n";
    $scss .= '$enable-gradients: false;' . "\n";
    $scss .= '$enable-dark-mode: true;' . "\n";
    $scss .= '$navbar-height: 68px;' . "\n";

    if (defined('BEHAT_SITE_RUNNING')) {
        $scss .= '$behatsite: true;' . "\n";
    }

    return $scss;
}

/**
 * Return SCSS appended after the Moodle Design System.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_nadine_get_extra_scss($theme): string {
    global $CFG;

    $scss = file_get_contents($CFG->dirroot . '/theme/nadine/scss/nadine.scss');

    // The header presentation is driven by an administrator setting rather
    // than a duplicated stylesheet.
    $headerstyle = theme_nadine_get_header_style();
    $scss .= "\n" . 'body {' . "\n";
    if ($headerstyle === 'solid') {
        $scss .= '    --nl-header-bg: var(--nl-surface);' . "\n";
        $scss .= '    --nl-header-blur: 0px;' . "\n";
    } else {
        $scss .= '    --nl-header-bg: var(--nl-header-translucent);' . "\n";
        $scss .= '    --nl-header-blur: 14px;' . "\n";
    }
    $scss .= '}' . "\n";

    if (!empty($theme->settings->customscss)) {
        $scss .= "\n" . $theme->settings->customscss;
    }

    return $scss;
}

/**
 * Record Boost's colour mode settings before the theme first overwrites them.
 *
 * The theme mirrors its own two colour mode controls into the parent theme's
 * configuration, because Moodle resolves light, dark and automatic modes in
 * the Boost renderer. That makes the parent's values the theme's to change,
 * and also its to put back: `db/uninstall.php` reads this snapshot and
 * restores it, so removing Nadine leaves the site as it was found.
 *
 * The snapshot is taken once. A later save must not overwrite it with the
 * values the theme itself just wrote, or the restore would be a no-op.
 *
 * @return void
 */
function theme_nadine_backup_colour_modes(): void {
    if (get_config('theme_nadine', 'colourmodesbackup') !== false) {
        return;
    }

    $backup = [];
    foreach (['enablecolourmodes', 'defaultcolourmode'] as $name) {
        $value = get_config('theme_boost', $name);
        $backup[$name] = [
            'set' => $value !== false,
            'value' => $value === false ? null : $value,
        ];
    }

    set_config('colourmodesbackup', json_encode($backup), 'theme_nadine');
}

/**
 * Keep Boost's colour mode feature in step with the theme settings.
 *
 * Moodle 5.3 resolves light, dark and automatic colour modes in the Boost
 * renderer and stores the choice as a user preference. The theme exposes the
 * controls on its own settings page, so this callback mirrors the values into
 * the parent theme's configuration.
 *
 * @return void
 */
function theme_nadine_sync_colour_modes(): void {
    theme_nadine_backup_colour_modes();

    $enabled = get_config('theme_nadine', 'enabledarkmode');
    set_config('enablecolourmodes', $enabled === false ? 1 : (int) !empty($enabled), 'theme_boost');

    $default = get_config('theme_nadine', 'defaultcolourmode');
    if (!in_array($default, ['light', 'dark', 'auto'], true)) {
        $default = 'light';
    }
    set_config('defaultcolourmode', $default, 'theme_boost');
}

/**
 * The colour themes shipped with the theme.
 *
 * Each theme is a curated accent and primary pair, not a full palette. The
 * neutrals, the warm paper and the type are the theme's identity and stay put,
 * so a colour theme only recolours what carries the brand: buttons, links,
 * active navigation, focus rings, progress fills, the hero washes and the
 * section marks. Everything else is derived from the pair at build time
 * (`mix()` for the strong and dark-mode variants), which is why one entry can
 * drive the whole page.
 *
 * Every pair is checked for contrast before it is added; the measured ratios
 * are in TESTING.md.
 *
 * @return array<string, array{accent: string, primary: string}>
 */
function theme_nadine_get_color_themes(): array {
    return [
        // Nadine's own palette.
        'default' => ['accent' => '#ef5e00', 'primary' => '#01275a'],
        // Deep green and forest ink.
        'forest' => ['accent' => '#2f7d4f', 'primary' => '#123a2b'],
        // Teal and a night-blue.
        'ocean' => ['accent' => '#0e7490', 'primary' => '#0b2e4a'],
        // Aubergine and blackcurrant.
        'plum' => ['accent' => '#8e3b6b', 'primary' => '#2b1033'],
        // Terracotta and burnt umber.
        'clay' => ['accent' => '#b04a2a', 'primary' => '#3a2318'],
        // Steel blue and graphite.
        'slate' => ['accent' => '#3f5ea8', 'primary' => '#16233f'],
        // Dusty rose and oxblood.
        'rose' => ['accent' => '#b23a5b', 'primary' => '#33101f'],
        // Saffron and dark tobacco.
        'amber' => ['accent' => '#a8681a', 'primary' => '#3b2a0e'],
    ];
}

/**
 * Return the selected colour theme.
 *
 * @param theme_config|null $theme The theme config object.
 * @return string A key of theme_nadine_get_color_themes(), or "custom".
 */
function theme_nadine_get_color_theme(?theme_config $theme = null): string {
    if ($theme === null) {
        $theme = theme_config::load('nadine');
    }

    $selected = $theme->settings->colortheme ?? 'default';
    if ($selected === 'custom') {
        return 'custom';
    }

    return array_key_exists($selected, theme_nadine_get_color_themes()) ? $selected : 'default';
}

/**
 * Return the accent colour configured by the administrator.
 *
 * @param theme_config|null $theme The theme config object.
 * @return string A hex colour.
 */
function theme_nadine_get_accent_color(?theme_config $theme = null): string {
    if ($theme === null) {
        $theme = theme_config::load('nadine');
    }

    $themes = theme_nadine_get_color_themes();
    $selected = theme_nadine_get_color_theme($theme);
    if ($selected !== 'custom') {
        return $themes[$selected]['accent'];
    }

    $accent = $theme->settings->accentcolor ?? '';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $accent)) {
        $accent = $themes['default']['accent'];
    }

    return $accent;
}

/**
 * Return the primary colour configured by the administrator.
 *
 * For a shipped colour theme this is the theme's own primary. For the custom
 * theme it is the picker value, falling back to the accent so a site that only
 * sets an accent keeps a coherent palette.
 *
 * @param theme_config|null $theme The theme config object.
 * @return string A hex colour.
 */
function theme_nadine_get_primary_color(?theme_config $theme = null): string {
    if ($theme === null) {
        $theme = theme_config::load('nadine');
    }

    $themes = theme_nadine_get_color_themes();
    $selected = theme_nadine_get_color_theme($theme);
    if ($selected !== 'custom') {
        return $themes[$selected]['primary'];
    }

    $primary = $theme->settings->primarycolor ?? '';
    if (preg_match('/^#[0-9a-fA-F]{6}$/', $primary)) {
        return $primary;
    }

    return theme_nadine_get_accent_color($theme);
}

/**
 * Mix two hex colours.
 *
 * Used to derive a readable accent for the dark colour mode without shipping
 * a second palette that could drift from the configured brand colour.
 *
 * @param string $color1 The base colour.
 * @param string $color2 The colour to mix in.
 * @param int $weight The percentage of $color2.
 * @return string
 */
function theme_nadine_mix_color(string $color1, string $color2, int $weight): string {
    $weight = max(0, min(100, $weight));
    $c1 = sscanf(ltrim($color1, '#'), '%2x%2x%2x');
    $c2 = sscanf(ltrim($color2, '#'), '%2x%2x%2x');

    $r = (int) round($c1[0] * (100 - $weight) / 100 + $c2[0] * $weight / 100);
    $g = (int) round($c1[1] * (100 - $weight) / 100 + $c2[1] * $weight / 100);
    $b = (int) round($c1[2] * (100 - $weight) / 100 + $c2[2] * $weight / 100);

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * WCAG 2.2 relative luminance of a hex colour.
 *
 * @param string $hex Colour in `#rrggbb` form.
 * @return float Between 0 and 1.
 */
function theme_nadine_relative_luminance(string $hex): float {
    $hex = ltrim($hex, '#');
    $channels = [];

    foreach ([0, 2, 4] as $offset) {
        $value = hexdec(substr($hex, $offset, 2)) / 255;
        $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
    }

    return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * WCAG 2.2 contrast ratio between two hex colours.
 *
 * @param string $one First colour.
 * @param string $two Second colour.
 * @return float Between 1 and 21.
 */
function theme_nadine_contrast_ratio(string $one, string $two): float {
    $a = theme_nadine_relative_luminance($one);
    $b = theme_nadine_relative_luminance($two);

    return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
}

/**
 * Return the accent as a surface that can carry white text and sit as a label.
 *
 * The brand accent is chosen for how it looks, not for its contrast: #ef5e00
 * reaches only 3.35:1 against white, which fails WCAG AA for button text. The
 * decorative tokens keep the accent untouched, and this helper walks the
 * accent towards black in one-percent steps until it clears 4.5:1 in both of
 * the roles a button puts it in — a fill under white text, and a label on the
 * canvas. A palette that already passes is returned unchanged, which is why
 * seven of the eight shipped palettes are unaffected.
 *
 * @param string $accent The configured accent colour.
 * @return string A hex colour.
 */
function theme_nadine_action_color(string $accent): string {
    $floor = 4.5;
    $canvas = '#f6f4ef';

    for ($weight = 0; $weight <= 60; $weight++) {
        $candidate = $weight === 0 ? $accent : theme_nadine_mix_color($accent, '#000000', $weight);

        if (
            theme_nadine_contrast_ratio($candidate, '#ffffff') >= $floor
                && theme_nadine_contrast_ratio($candidate, $canvas) >= $floor
        ) {
            return $candidate;
        }
    }

    return theme_nadine_mix_color($accent, '#000000', 60);
}

/**
 * Serve files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_nadine_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $fileareas = ['logo', 'favicon', 'heroimage', 'loginimage'];

    if ($context->contextlevel !== CONTEXT_SYSTEM || !in_array($filearea, $fileareas, true)) {
        send_file_not_found();
    }

    $theme = theme_config::load('nadine');
    if (!array_key_exists('cacheability', $options)) {
        $options['cacheability'] = 'public';
    }

    return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
}

/**
 * Return a theme setting file URL.
 *
 * @param string $setting The setting name.
 * @param string $filearea The file area.
 * @return string|null
 */
function theme_nadine_get_setting_file_url(string $setting, string $filearea): ?string {
    $theme = theme_config::load('nadine');

    return $theme->setting_file_url($setting, $filearea);
}

/**
 * Return the brand logo URL.
 *
 * @param renderer_base $output
 * @return string
 */
function theme_nadine_get_logo_url(renderer_base $output): string {
    $url = theme_nadine_get_setting_file_url('logo', 'logo');
    if (!empty($url)) {
        return $url;
    }

    $logo = $output->get_logo_url(null, 80);
    if ($logo === false) {
        $logo = $output->get_compact_logo_url(300, 80);
    }

    return $logo === false ? '' : $logo->out(false);
}

/**
 * Return the configured header style.
 *
 * @return string Either "solid" or "translucent".
 */
function theme_nadine_get_header_style(): string {
    $style = get_config('theme_nadine', 'headerstyle');

    return in_array($style, ['solid', 'translucent'], true) ? $style : 'translucent';
}

/**
 * Whether the public homepage hero is enabled.
 *
 * @return bool
 */
function theme_nadine_hero_enabled(): bool {
    $setting = get_config('theme_nadine', 'enablehero');

    return $setting === false || !empty($setting);
}

/**
 * Return footer context.
 *
 * @param renderer_base $output
 * @return array
 */
function theme_nadine_get_footer_context(renderer_base $output): array {
    global $CFG, $USER;

    $settings = theme_config::load('nadine')->settings;
    $tagline = !empty($settings->footertagline)
        ? $settings->footertagline
        : get_string('footer_tagline_default', 'theme_nadine');
    $about = !empty($settings->footerabout)
        ? $settings->footerabout
        : get_string('footer_about_default', 'theme_nadine');

    $loggedin = isloggedin() && !isguestuser();

    $explore = [
        [
            'url' => (new moodle_url('/'))->out(false),
            'label' => get_string('home', 'theme_nadine'),
        ],
        [
            'url' => (new moodle_url('/course/index.php'))->out(false),
            'label' => get_string('browsecourses', 'theme_nadine'),
        ],
    ];

    $account = [];
    if ($loggedin) {
        $account[] = [
            'url' => (new moodle_url('/my/'))->out(false),
            'label' => get_string('dashboard', 'theme_nadine'),
        ];
        $account[] = [
            'url' => (new moodle_url('/my/courses.php'))->out(false),
            'label' => get_string('mycourses', 'theme_nadine'),
        ];
        $account[] = [
            'url' => (new moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
            'label' => get_string('calendar', 'theme_nadine'),
        ];
        $account[] = [
            'url' => (new moodle_url('/user/profile.php'))->out(false),
            'label' => get_string('profile', 'theme_nadine'),
        ];
    } else {
        $account[] = [
            'url' => (new moodle_url('/login/index.php'))->out(false),
            'label' => get_string('signin', 'theme_nadine'),
        ];
        if (!empty($CFG->registerauth)) {
            $account[] = [
                'url' => (new moodle_url('/login/signup.php'))->out(false),
                'label' => get_string('createaccount', 'theme_nadine'),
            ];
        }
    }

    $support = [];
    if (!empty($CFG->supportemail)) {
        $support[] = [
            'url' => (new moodle_url('/user/contacts.php'))->out(false),
            'label' => get_string('contact_support', 'theme_nadine'),
        ];
    }
    $support[] = [
        'url' => 'https://docs.moodle.org/',
        'label' => get_string('moodledocs', 'theme_nadine'),
        'external' => true,
    ];

    $year = date('Y');

    // The social row is data, not chrome: a network only appears when the site
    // has entered an address for it, so the footer never advertises an account
    // that does not exist. The addresses are cleaned again here because a
    // theme setting can be written by an importer as well as by the form.
    $networks = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'x' => 'X',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
        'github' => 'GitHub',
    ];
    $social = [];
    foreach ($networks as $network => $label) {
        $url = clean_param(trim((string) ($settings->{'footersocial' . $network} ?? '')), PARAM_URL);
        if ($url === '') {
            continue;
        }
        $social[] = [
            'url' => $url,
            'label' => $label,
            'arialabel' => get_string('footer_social_link', 'theme_nadine', $label),
            'icon' => theme_nadine_icon($network, 'nl-footer__social-icon', 18, 'social'),
        ];
    }

    return [
        'brand' => $output->nadine_brand('footer'),
        'sitename' => format_string(get_site()->fullname, true, ['context' => context_system::instance()]),
        'tagline' => format_string($tagline, true, ['context' => context_system::instance()]),
        'about' => format_text($about, FORMAT_HTML, ['context' => context_system::instance()]),
        'hassocial' => !empty($social),
        'sociallabel' => get_string('footer_social', 'theme_nadine'),
        'social' => $social,
        'columns' => [
            [
                'title' => get_string('footer_explore', 'theme_nadine'),
                'links' => $explore,
            ],
            [
                'title' => get_string('footer_account', 'theme_nadine'),
                'links' => $account,
            ],
            [
                'title' => get_string('footer_support', 'theme_nadine'),
                'links' => $support,
            ],
        ],
        'year' => $year,
        'copyright' => get_string('footer_copyright', 'theme_nadine', [
            'year' => $year,
            'sitename' => format_string(get_site()->fullname, true, ['context' => context_system::instance()]),
        ]),
        'moodleurl' => 'https://moodle.org/',
        'moodlelabel' => get_string('footer_moodle', 'theme_nadine'),
        'totoplabel' => get_string('footer_totop', 'theme_nadine'),
        'totopicon' => theme_nadine_icon('arrow-up', 'nl-footer__totop-icon', 14),
    ];
}

/**
 * Context for the live search field.
 *
 * The field works without JavaScript: the form still submits to Moodle's own
 * course search page. The data attributes only feed the progressive panel.
 *
 * @param string $id Unique input id on the page.
 * @param bool $compact Render the narrower catalogue variant.
 * @return array
 */
function theme_nadine_get_search_context(string $id, bool $compact = false): array {
    return [
        'id' => $id,
        'compact' => $compact,
        'action' => (new moodle_url('/course/search.php'))->out(false),
        'endpoint' => (new moodle_url('/theme/nadine/ajax/search.php'))->out(false),
        'sesskey' => sesskey(),
        'label' => get_string('searchcourses', 'theme_nadine'),
        'placeholder' => get_string('searchcourses_placeholder', 'theme_nadine'),
        'viewall' => get_string('search_viewall', 'theme_nadine'),
        'clearlabel' => get_string('search_clear', 'theme_nadine'),
        'strnone' => get_string('search_none', 'theme_nadine'),
        'strcount' => get_string('search_count', 'theme_nadine'),
        'strerror' => get_string('search_error', 'theme_nadine'),
        'strloading' => get_string('search_loading', 'theme_nadine'),
        'searchicon' => theme_nadine_icon('search', '', 18),
        'clearicon' => theme_nadine_icon('circle-x', '', 18),
    ];
}

/**
 * Teachers assigned to a course, capped so the header never grows unbounded.
 *
 * @param stdClass $course
 * @param context_course $context
 * @param int $limit
 * @return array List of ['name' => string].
 */
function theme_nadine_get_course_teachers(stdClass $course, context_course $context, int $limit = 3): array {
    global $DB;

    $roles = $DB->get_records_select('role', "archetype IN ('teacher', 'editingteacher')", null, '', 'id');
    if (empty($roles)) {
        return [];
    }

    [$insql, $params] = $DB->get_in_or_equal(array_keys($roles), SQL_PARAMS_NAMED, 'role');
    $params['ctxid'] = $context->id;

    $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                   u.middlename, u.alternatename
              FROM {user} u
              JOIN {role_assignments} ra ON ra.userid = u.id
             WHERE ra.roleid $insql
                   AND ra.contextid = :ctxid
                   AND u.deleted = 0
                   AND u.suspended = 0
          ORDER BY u.lastname, u.firstname";

    $users = $DB->get_records_sql($sql, $params, 0, $limit);

    $teachers = [];
    foreach ($users as $user) {
        $teachers[] = ['name' => fullname($user)];
    }

    return $teachers;
}

/**
 * Context for the course header band.
 *
 * Returns null on anything that is not a course home page, so the stock Moodle
 * header keeps rendering everywhere else.
 *
 * @param renderer_base $output
 * @return array|null
 */
function theme_nadine_get_course_hero_context(renderer_base $output): ?array {
    global $PAGE, $USER, $CFG;

    $course = $PAGE->course;
    if ($PAGE->pagelayout !== 'course' || empty($course) || $course->id == SITEID) {
        return null;
    }
    if (!str_starts_with($PAGE->pagetype, 'course-view')) {
        return null;
    }

    $context = context_course::instance($course->id);
    $modinfo = get_fast_modinfo($course);

    $sections = 0;
    foreach ($modinfo->get_section_info_all() as $section) {
        if ($section->section > 0 && $section->uservisible) {
            $sections++;
        }
    }

    $activities = 0;
    $firsturl = '';
    $firstname = '';
    foreach ($modinfo->get_cms() as $cm) {
        if (!$cm->uservisible) {
            continue;
        }
        $activities++;
        if ($firsturl === '' && $cm->url && $cm->has_view()) {
            $firsturl = $cm->url->out(false);
            $firstname = $cm->name;
        }
    }

    if ($firsturl === '') {
        return null;
    }

    $summary = '';
    if (trim((string) $course->summary) !== '') {
        $summary = format_text($course->summary, $course->summaryformat, [
            'context' => $context,
            'filter' => true,
        ]);
    }

    $category = '';
    $categoryurl = '';
    $coursecat = core_course_category::get($course->category, IGNORE_MISSING);
    if ($coursecat) {
        $category = $coursecat->get_formatted_name();
        $categoryurl = (new moodle_url('/course/index.php', ['categoryid' => $coursecat->id]))->out(false);
    }

    $progress = null;
    if (isloggedin() && !isguestuser()) {
        require_once($CFG->dirroot . '/completion/classes/progress.php');
        $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
    }

    if ($progress === null) {
        $ctalabel = get_string('course_hero_start', 'theme_nadine');
    } else if ($progress >= 100) {
        $ctalabel = get_string('course_hero_review', 'theme_nadine');
    } else if ($progress > 0) {
        $ctalabel = get_string('course_hero_continue', 'theme_nadine');
    } else {
        $ctalabel = get_string('course_hero_start', 'theme_nadine');
    }

    $teachers = theme_nadine_get_course_teachers($course, $context);

    return [
        'fullname' => format_string($course->fullname, true, ['context' => $context]),
        'category' => $category,
        'categoryurl' => $categoryurl,
        'hascategory' => $category !== '',
        'summary' => $summary,
        'hassummary' => $summary !== '',
        'sections' => get_string('course_meta_sections', 'theme_nadine', $sections),
        'activities' => get_string('course_meta_activities', 'theme_nadine', $activities),
        'teachers' => $teachers,
        'teachernames' => implode(', ', array_column($teachers, 'name')),
        'hasteachers' => !empty($teachers),
        'hasprogress' => $progress !== null,
        'progress' => $progress === null ? 0 : (int) round($progress),
        'progresslabel' => get_string('course_hero_progress', 'theme_nadine'),
        'ctaurl' => $firsturl,
        'ctalabel' => $ctalabel,
        'ctaicon' => theme_nadine_icon('arrow-right', 'nl-directional', 18),
        'sectionsicon' => theme_nadine_icon('layers', '', 16),
        'activitiesicon' => theme_nadine_icon('boxes', '', 16),
        'teachersicon' => theme_nadine_icon('graduation-cap', '', 16),
        'nextname' => $firstname,
        'nextlabel' => get_string('course_hero_next', 'theme_nadine'),
    ];
}

/**
 * Return homepage hero context.
 *
 * @param renderer_base $output
 * @return array|null
 */
function theme_nadine_get_hero_context(renderer_base $output): ?array {
    global $CFG, $USER;

    if (!theme_nadine_hero_enabled()) {
        return null;
    }

    $settings = theme_config::load('nadine')->settings;
    $imageurl = theme_nadine_get_setting_file_url('heroimage', 'heroimage');
    $imagealt = !empty($settings->heroimagealt)
        ? $settings->heroimagealt
        : get_string('hero_image_alt', 'theme_nadine');

    $loggedin = isloggedin() && !isguestuser();
    $primaryurl = (new moodle_url('/course/index.php'))->out(false);
    $secondaryurl = $loggedin
        ? (new moodle_url('/my/'))->out(false)
        : (new moodle_url('/login/index.php'))->out(false);
    $secondarylabel = $loggedin
        ? get_string('dashboard', 'theme_nadine')
        : get_string('signin', 'theme_nadine');

    $signupurl = '';
    if (!$loggedin && !empty($CFG->registerauth)) {
        $signupurl = (new moodle_url('/login/signup.php'))->out(false);
    }

    $showsearch = !isset($settings->enableherosearch) || !empty($settings->enableherosearch);

    return [
        'eyebrow' => !empty($settings->heroeyebrow)
            ? format_string($settings->heroeyebrow, true, ['context' => context_system::instance()])
            : get_string('hero_eyebrow_default', 'theme_nadine'),
        'title' => !empty($settings->herotitle)
            ? format_string($settings->herotitle, true, ['context' => context_system::instance()])
            : get_string('hero_title_default', 'theme_nadine'),
        'text' => !empty($settings->herotext)
            ? format_text($settings->herotext, FORMAT_HTML, ['context' => context_system::instance()])
            : get_string('hero_text_default', 'theme_nadine'),
        'welcome' => $loggedin ? get_string('welcomeback', 'theme_nadine', fullname($USER)) : '',
        'primaryurl' => $primaryurl,
        'primarylabel' => !empty($settings->heroprimarylabel)
            ? format_string($settings->heroprimarylabel, true, ['context' => context_system::instance()])
            : get_string('browsecourses', 'theme_nadine'),
        'secondaryurl' => $secondaryurl,
        'secondarylabel' => $secondarylabel,
        'signupurl' => $signupurl,
        'signuplabel' => get_string('createaccount', 'theme_nadine'),
        'showsearch' => $showsearch,
        'search' => theme_nadine_get_search_context('nl-hero-search'),
        'ctaicon' => theme_nadine_icon('arrow-right', 'nl-directional', 18),
        'points' => [
            [
                'icon' => theme_nadine_icon('graduation-cap'),
                'label' => get_string('hero_point_learn', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('chart-line'),
                'label' => get_string('hero_point_progress', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('clock'),
                'label' => get_string('hero_point_flexible', 'theme_nadine'),
            ],
        ],
        'imageurl' => $imageurl,
        'imagealt' => $imagealt,
        'hasimage' => !empty($imageurl),
        'arttitle' => !empty($settings->heropanelkicker)
            ? format_string($settings->heropanelkicker, true, ['context' => context_system::instance()])
            : get_string('hero_art_title', 'theme_nadine'),
        'arttext' => !empty($settings->heropaneltext)
            ? format_string($settings->heropaneltext, true, ['context' => context_system::instance()])
            : get_string('hero_art_text', 'theme_nadine'),
        'artsteps' => [
            [
                'icon' => theme_nadine_icon('book-open'),
                'title' => get_string('hero_art_step_1', 'theme_nadine'),
                'text' => get_string('hero_art_step_1_desc', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('pencil-ruler'),
                'title' => get_string('hero_art_step_2', 'theme_nadine'),
                'text' => get_string('hero_art_step_2_desc', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('trending-up'),
                'title' => get_string('hero_art_step_3', 'theme_nadine'),
                'text' => get_string('hero_art_step_3_desc', 'theme_nadine'),
            ],
        ],
    ];
}

/**
 * Courses a user is enrolled in, most recently accessed first.
 *
 * "Continue" has to mean the course the learner was last in, and Moodle's own
 * enrolment helpers return courses in creation order. The last access table is
 * the only record of what they actually opened, so it drives the sort; courses
 * never opened fall back to newest first.
 *
 * @param int $userid
 * @param int $limit Zero for no limit.
 * @return stdClass[] Course records keyed by course id.
 */
function theme_nadine_get_user_courses(int $userid, int $limit = 0): array {
    global $DB;

    $courses = enrol_get_users_courses($userid, true);
    if (empty($courses)) {
        return [];
    }

    $rows = $DB->get_records_list('user_lastaccess', 'courseid', array_keys($courses), '', 'id, courseid, timeaccess');
    $access = [];
    foreach ($rows as $row) {
        $access[(int) $row->courseid] = (int) $row->timeaccess;
    }

    uasort($courses, function ($a, $b) use ($access) {
        $atime = $access[$a->id] ?? 0;
        $btime = $access[$b->id] ?? 0;
        if ($atime === $btime) {
            return ($b->timecreated ?? 0) <=> ($a->timecreated ?? 0);
        }
        return $btime <=> $atime;
    });

    if ($limit > 0) {
        $courses = array_slice($courses, 0, $limit, true);
    }

    return $courses;
}

/**
 * Build one course card for the theme's own course surfaces.
 *
 * Progress is opt-in because it needs a module info build per course: the
 * catalogue grid never shows it, and paying for it on a signed-out page would
 * be wasted work.
 *
 * @param renderer_base $output
 * @param stdClass|core_course_list_element $course Course record or the list
 *        element that core_course_category::get_courses() returns.
 * @param string $cta Label for the card action.
 * @param int|null $userid Whose progress to read. Null means the current user.
 * @param bool $withprogress Whether to resolve completion progress at all.
 * @return array
 */
function theme_nadine_course_card(
    renderer_base $output,
    $course,
    string $cta,
    ?int $userid = null,
    bool $withprogress = false
): array {
    global $CFG;

    $category = '';
    $coursecat = \core_course_category::get($course->category, IGNORE_MISSING);
    if ($coursecat) {
        $category = $coursecat->get_formatted_name();
    }

    $progress = null;
    if ($withprogress) {
        require_once($CFG->dirroot . '/completion/classes/progress.php');
        $progress = \core_completion\progress::get_course_progress_percentage($course, $userid);
    }

    $percent = $progress === null ? 0 : (int) round($progress);

    // The course's own featured image when it has one, exactly as core's course
    // cards do, and the generated pattern when it does not. The URL comes from
    // core's course_image cache, so this is one cached read per card rather
    // than a file lookup; the cache stores false for a course with no overview
    // image, which is what makes the fallback cheap too.
    $imageurl = $output->get_generated_image_for_id($course->id);
    $featured = \cache::make('core', 'course_image')->get($course->id);
    if (!empty($featured)) {
        $imageurl = (new moodle_url($featured))->out(false);
    }

    return [
        'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
        'name' => get_course_display_name_for_list($course),
        'category' => $category,
        'hascategory' => $category !== '',
        'imageurl' => $imageurl,
        'progress' => $percent,
        'hasprogress' => $progress !== null,
        'progresslabel' => get_string('courseprogress', 'theme_nadine', $percent),
        'progresscaption' => get_string('courseprogresscaption', 'theme_nadine'),
        'percentshort' => get_string('coursepercent', 'theme_nadine', $percent),
        'cta' => $cta,
        'icon' => theme_nadine_icon('book-open', '', 18),
        'arrowicon' => theme_nadine_icon('arrow-right', 'nl-directional', 16),
    ];
}

/**
 * The single course a learner should pick up next.
 *
 * Preference order: a course genuinely in progress, then one enrolled but not
 * started, then anything they can open. Returns null when the user has no
 * courses at all, so the welcome panel can offer the catalogue instead.
 *
 * @param renderer_base $output
 * @param int $userid
 * @return array|null
 */
function theme_nadine_get_continue_context(renderer_base $output, int $userid): ?array {
    $courses = theme_nadine_get_user_courses($userid, 8);
    if (empty($courses)) {
        return null;
    }

    $cards = [];
    foreach ($courses as $course) {
        $cards[] = theme_nadine_course_card(
            $output,
            $course,
            get_string('resume', 'theme_nadine'),
            $userid,
            true
        );
    }

    $pick = null;
    foreach ($cards as $card) {
        if ($card['hasprogress'] && $card['progress'] > 0 && $card['progress'] < 100) {
            $pick = $card;
            break;
        }
    }

    if ($pick === null) {
        foreach ($cards as $card) {
            if ($card['hasprogress'] && $card['progress'] === 0) {
                $card['cta'] = get_string('startcourse', 'theme_nadine');
                $pick = $card;
                break;
            }
        }
    }

    if ($pick === null) {
        $pick = $cards[0];
        $pick['cta'] = get_string('opencourse', 'theme_nadine');
    }

    $pick['label'] = get_string('pickupwhereyouleft', 'theme_nadine');

    return $pick;
}

/**
 * The signed-in learner's own courses, for the homepage strip.
 *
 * The site home already ends with core's "Available courses" list, so a second
 * catalogue here would just repeat it. This returns the learner's own courses
 * instead, which is the one thing that list cannot show.
 *
 * @param renderer_base $output
 * @param int $userid
 * @param int $limit How many cards to show.
 * @return array
 */
function theme_nadine_get_continue_courses(renderer_base $output, int $userid, int $limit = 3): array {
    $courses = theme_nadine_get_user_courses($userid, $limit);

    $cards = [];
    foreach ($courses as $course) {
        $card = theme_nadine_course_card(
            $output,
            $course,
            get_string('resume', 'theme_nadine'),
            $userid,
            true
        );
        if ($card['hasprogress'] && $card['progress'] === 0) {
            $card['cta'] = get_string('startcourse', 'theme_nadine');
        }
        $cards[] = $card;
    }

    return [
        'courses' => $cards,
        'hascourses' => !empty($cards),
        // One card in a three-up grid leaves two thirds of the row empty, so
        // the template switches to a wide single card instead.
        'singlecard' => count($cards) === 1,
        'eyebrow' => get_string('yourlearning', 'theme_nadine'),
        'title' => get_string('frontpage_continue_title', 'theme_nadine'),
        'allcoursesurl' => (new moodle_url('/my/courses.php'))->out(false),
        'allcourseslabel' => get_string('mycourses', 'theme_nadine'),
        'arrowicon' => theme_nadine_icon('arrow-right', 'nl-directional', 18),
    ];
}

/**
 * Return the real site counters shown in the homepage stats strip.
 *
 * Every figure comes from the database, so a fresh install shows small
 * numbers rather than invented marketing copy.
 *
 * @param renderer_base $output
 * @return array
 */
function theme_nadine_get_frontpage_stats(renderer_base $output): array {
    global $DB, $SITE;

    $courses = $DB->count_records_select('course', 'visible = 1 AND id <> ?', [$SITE->id]);
    $learners = $DB->count_records_select('user', 'deleted = 0 AND suspended = 0 AND confirmed = 1');
    $categories = $DB->count_records('course_categories');
    $enrolments = $DB->count_records('user_enrolments');

    $stats = [
        [
            'value' => $courses,
            'rawvalue' => $courses,
            'label' => get_string('frontpage_stats_courses', 'theme_nadine'),
            'icon' => theme_nadine_icon('graduation-cap'),
            'accent' => '#2563eb',
        ],
        [
            'value' => $learners,
            'rawvalue' => $learners,
            'label' => get_string('frontpage_stats_learners', 'theme_nadine'),
            'icon' => theme_nadine_icon('users'),
            'accent' => '#0d9488',
        ],
        [
            'value' => $categories,
            'rawvalue' => $categories,
            'label' => get_string('frontpage_stats_categories', 'theme_nadine'),
            'icon' => theme_nadine_icon('layers'),
            'accent' => '#7c3aed',
        ],
        [
            'value' => $enrolments,
            'rawvalue' => $enrolments,
            'label' => get_string('frontpage_stats_enrolments', 'theme_nadine'),
            'icon' => theme_nadine_icon('user-check'),
            'accent' => '#ea580c',
        ],
    ];

    foreach ($stats as $index => $stat) {
        $stats[$index]['value'] = number_format($stat['value']);
    }

    return [
        'stats' => $stats,
        'label' => get_string('frontpage_stats_label', 'theme_nadine'),
    ];
}

/**
 * Return the homepage closing sections: the feature grid and the call to action.
 *
 * @param renderer_base $output
 * @return array
 */
function theme_nadine_get_frontpage_outro(renderer_base $output): array {
    global $CFG;

    $settings = theme_config::load('nadine')->settings;
    $text = static function (string $name, string $default) use ($settings): string {
        return !empty($settings->$name)
            ? format_string($settings->$name, true, ['context' => context_system::instance()])
            : $default;
    };

    $loggedin = isloggedin() && !isguestuser();
    $browseurl = (new moodle_url('/course/index.php'))->out(false);

    $joinurl = '';
    $joinlabel = '';
    if (!$loggedin && !empty($CFG->registerauth)) {
        $joinurl = (new moodle_url('/login/signup.php'))->out(false);
        $joinlabel = $text('ctajoinlabel', get_string('frontpage_cta_join', 'theme_nadine'));
    } else if (!$loggedin) {
        $joinurl = (new moodle_url('/login/index.php'))->out(false);
        $joinlabel = get_string('signin', 'theme_nadine');
    }

    return [
        'eyebrow' => $text('featureseyebrow', get_string('frontpage_features_eyebrow', 'theme_nadine')),
        'title' => $text('featurestitle', get_string('frontpage_features_title', 'theme_nadine')),
        'features' => [
            [
                'icon' => theme_nadine_icon('layers'),
                'title' => $text('feature1title', get_string('frontpage_feature_1_title', 'theme_nadine')),
                'text' => $text('feature1text', get_string('frontpage_feature_1_text', 'theme_nadine')),
            ],
            [
                'icon' => theme_nadine_icon('chart-bar'),
                'title' => $text('feature2title', get_string('frontpage_feature_2_title', 'theme_nadine')),
                'text' => $text('feature2text', get_string('frontpage_feature_2_text', 'theme_nadine')),
            ],
            [
                'icon' => theme_nadine_icon('smartphone'),
                'title' => $text('feature3title', get_string('frontpage_feature_3_title', 'theme_nadine')),
                'text' => $text('feature3text', get_string('frontpage_feature_3_text', 'theme_nadine')),
            ],
        ],
        'ctatitle' => $text('ctatitle', get_string('frontpage_cta_title', 'theme_nadine')),
        'ctatext' => $text('ctatext', get_string('frontpage_cta_text', 'theme_nadine')),
        'browseurl' => $browseurl,
        'browselabel' => $text('ctabrowselabel', get_string('frontpage_cta_browse', 'theme_nadine')),
        'joinurl' => $joinurl,
        'joinlabel' => $joinlabel,
    ];
}

/**
 * Return dashboard welcome context.
 *
 * @param renderer_base $output
 * @return array|null
 */
function theme_nadine_get_dashboard_context(renderer_base $output): ?array {
    global $CFG, $USER;

    if (!isloggedin() || isguestuser()) {
        return null;
    }

    $settings = theme_config::load('nadine')->settings;
    if (isset($settings->showdashboardwelcome) && empty($settings->showdashboardwelcome)) {
        return null;
    }

    $username = fullname($USER);
    $greeting = !empty($settings->dashboardgreeting)
        ? str_replace('{name}', $username, $settings->dashboardgreeting)
        : get_string('welcomeback', 'theme_nadine', $username);

    $links = [
        [
            'url' => (new moodle_url('/my/courses.php'))->out(false),
            'label' => get_string('mycourses', 'theme_nadine'),
            'icon' => theme_nadine_icon('book-open'),
        ],
        [
            'url' => (new moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
            'label' => get_string('calendar', 'theme_nadine'),
            'icon' => theme_nadine_icon('calendar-days'),
        ],
    ];

    if (!empty($CFG->messaging)) {
        $links[] = [
            'url' => (new moodle_url('/message/index.php'))->out(false),
            'label' => get_string('messages', 'theme_nadine'),
            'icon' => theme_nadine_icon('message-square-text'),
        ];
    }

    $links[] = [
        'url' => (new moodle_url('/course/index.php'))->out(false),
        'label' => get_string('browsecourses', 'theme_nadine'),
        'icon' => theme_nadine_icon('compass'),
    ];

    return [
        'fullname' => fullname($USER),
        'eyebrow' => !empty($settings->dashboardeyebrow)
            ? format_string($settings->dashboardeyebrow, true, ['context' => context_system::instance()])
            : get_string('yourlearning', 'theme_nadine'),
        'greeting' => format_string($greeting, true, ['context' => context_system::instance()]),
        'intro' => !empty($settings->dashboardintro)
            ? format_text($settings->dashboardintro, FORMAT_HTML, ['context' => context_system::instance()])
            : get_string('dashboard_intro', 'theme_nadine'),
        // Rendered at 36px (see nadine.scss) but requested at 50: core serves
        // f2, a 35px file, for any size below 50 and only f1 (100px) above it,
        // so 36 would leave the avatar soft on a retina screen. The class this
        // puts on the initials is neutralised in the same stylesheet.
        'userpicture' => $output->user_picture($USER, [
            'size' => 50,
            'link' => false,
            'alttext' => true,
            'class' => 'nl-avatar',
        ]),
        'links' => $links,
        'quicklinkstitle' => get_string('quicklinks', 'theme_nadine'),
        'nextcourse' => theme_nadine_get_continue_context($output, $USER->id),
        'browseurl' => (new moodle_url('/course/index.php'))->out(false),
        'browselabel' => get_string('browsecourses', 'theme_nadine'),
        'emptylabel' => get_string('dashboard_no_courses', 'theme_nadine'),
    ];
}

/**
 * Return course catalogue intro context.
 *
 * @param renderer_base $output
 * @return array|null
 */
function theme_nadine_get_catalogue_context(renderer_base $output): ?array {
    $settings = theme_config::load('nadine')->settings;
    $categories = [];
    $top = core_course_category::top();

    foreach ($top->get_children() as $category) {
        if (!$category->is_uservisible()) {
            continue;
        }

        $categories[] = [
            'name' => $category->get_formatted_name(),
            'url' => (new moodle_url('/course/index.php', ['categoryid' => $category->id]))->out(false),
        ];

        if (count($categories) >= 8) {
            break;
        }
    }

    return [
        'eyebrow' => !empty($settings->catalogueeyebrow)
            ? format_string($settings->catalogueeyebrow, true, ['context' => context_system::instance()])
            : get_string('catalogue_eyebrow', 'theme_nadine'),
        'title' => !empty($settings->cataloguetitle)
            ? format_string($settings->cataloguetitle, true, ['context' => context_system::instance()])
            : get_string('catalogue_title', 'theme_nadine'),
        'intro' => !empty($settings->catalogueintro)
            ? format_text($settings->catalogueintro, FORMAT_HTML, ['context' => context_system::instance()])
            : get_string('catalogue_intro', 'theme_nadine'),
        'search' => theme_nadine_get_search_context('nl-catalogue-search', true),
        'allcoursesurl' => (new moodle_url('/course/index.php'))->out(false),
        'allcourseslabel' => get_string('allcourses', 'theme_nadine'),
        'categories' => $categories,
        'hascategories' => !empty($categories),
        'categorieslabel' => get_string('catalogue_categories', 'theme_nadine'),
        'arrowicon' => theme_nadine_icon('arrow-right', 'nl-directional', 18),
    ];
}

/**
 * Return the identity strip for a public profile.
 *
 * Core prints the page header (picture, name, message button) and the profile
 * tree. This adds the quiet row of facts under the name. Nothing is invented:
 * every chip comes from the user record and respects the site's hidden profile
 * fields, so the strip never shows more than the tree below it.
 *
 * @return array|null Null on every page except a user profile.
 */
function theme_nadine_get_profile_context(): ?array {
    global $CFG, $DB, $PAGE, $USER;

    if ($PAGE->pagetype !== 'user-profile' || !($PAGE->context instanceof context_user)) {
        return null;
    }

    $user = $DB->get_record('user', ['id' => $PAGE->context->instanceid, 'deleted' => 0]);
    if (!$user) {
        return null;
    }

    $hidden = [];
    if (!has_capability('moodle/user:viewhiddendetails', $PAGE->context)) {
        $hidden = array_flip(explode(',', (string) $CFG->hiddenuserfields));
    }

    $chips = [];

    $affiliation = array_filter([
        trim((string) $user->department),
        trim((string) $user->institution),
    ]);
    if ($affiliation) {
        $chips[] = [
            'icon' => theme_nadine_icon('users'),
            'label' => implode(' · ', $affiliation),
        ];
    }

    $country = '';
    if (!empty($user->country) && !isset($hidden['country'])) {
        $country = get_string($user->country, 'countries');
    }
    $place = array_filter([
        isset($hidden['city']) ? '' : trim((string) $user->city),
        $country,
    ]);
    if ($place) {
        $chips[] = [
            'icon' => theme_nadine_icon('map-pin'),
            'label' => implode(', ', $place),
        ];
    }

    if (!empty($user->timecreated)) {
        $chips[] = [
            'icon' => theme_nadine_icon('calendar-days'),
            'label' => get_string(
                'profile_member_since',
                'theme_nadine',
                userdate($user->timecreated, get_string('strftimemonthyear'))
            ),
        ];
    }

    $editurl = theme_nadine_get_profile_edit_url($user);

    return [
        'chips' => $chips,
        'haschips' => !empty($chips),
        'editurl' => $editurl,
        'editlabel' => get_string('editmyprofile'),
        'editicon' => theme_nadine_icon('pencil-ruler', '', 16),
        'hasmeta' => !empty($chips) || $editurl !== '',
    ];
}

/**
 * Return the profile edit URL, or an empty string when the viewer may not edit.
 *
 * This mirrors the rules core applies before it adds the edit node to the
 * myprofile tree: the identity band offers the same action the card used to
 * carry, in the place a reader looks for it.
 *
 * @param stdClass $user The profile being viewed.
 * @return string Empty when the viewer cannot edit this profile.
 */
function theme_nadine_get_profile_edit_url(stdClass $user): string {
    global $PAGE, $USER;

    if (!isloggedin() || isguestuser($user) || is_mnet_remote_user($user)) {
        return '';
    }

    $systemcontext = context_system::instance();
    $iscurrentuser = $USER->id == $user->id;

    if (
        ($iscurrentuser || is_siteadmin($USER) || !is_siteadmin($user))
            && has_capability('moodle/user:update', $systemcontext)
    ) {
        return (new moodle_url('/user/editadvanced.php', ['id' => $user->id, 'returnto' => 'profile']))->out(false);
    }

    $canedit = (has_capability('moodle/user:editprofile', $PAGE->context) && !is_siteadmin($user))
        || ($iscurrentuser && has_capability('moodle/user:editownprofile', $systemcontext));
    if (!$canedit) {
        return '';
    }

    $authplugin = \core\di::get(\core\authentication::class)->get_plugin($user->auth);
    if (!$authplugin || !$authplugin->can_edit_profile()) {
        return '';
    }

    $url = $authplugin->edit_profile_url();

    return $url ? $url->out(false) : (new moodle_url('/user/edit.php', ['id' => $user->id, 'returnto' => 'profile']))->out(false);
}

/**
 * Return login panel context.
 *
 * The core login layout renders a left panel with placeholder statistics. The
 * theme replaces it with the site's own branding and value statements so that
 * no invented figures are presented to learners.
 *
 * @param renderer_base $output
 * @return array
 */
function theme_nadine_get_login_panel_context(renderer_base $output): array {
    global $CFG;

    $settings = theme_config::load('nadine')->settings;
    $imageurl = theme_nadine_get_setting_file_url('loginimage', 'loginimage');

    // An uploaded image wins; otherwise the theme ships its own illustration so
    // that the panel is never a flat colour out of the box.
    if (empty($imageurl)) {
        $imageurl = $output->image_url('login-art', 'theme_nadine')->out(false);
    }

    $title = !empty($settings->loginheading)
        ? format_string($settings->loginheading, true, ['context' => context_system::instance()])
        : get_string('login_heading_default', 'theme_nadine');

    $intro = !empty($settings->loginintro)
        ? format_text($settings->loginintro, FORMAT_HTML, ['context' => context_system::instance()])
        : get_string('login_intro_default', 'theme_nadine');

    return [
        'brand' => $output->nadine_brand('login'),
        'eyebrow' => !empty($settings->logineyebrow)
            ? format_string($settings->logineyebrow, true, ['context' => context_system::instance()])
            : get_string('login_eyebrow', 'theme_nadine'),
        'title' => $title,
        'intro' => $intro,
        'hasimage' => !empty($imageurl),
        'imageurl' => $imageurl,
        'imagealt' => !empty($settings->loginimagealt)
            ? format_string($settings->loginimagealt, true, ['context' => context_system::instance()])
            : get_string('login_image_alt', 'theme_nadine'),
        'points' => [
            [
                'icon' => theme_nadine_icon('layers'),
                'title' => get_string('login_point_1', 'theme_nadine'),
                'text' => get_string('login_point_1_desc', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('route'),
                'title' => get_string('login_point_2', 'theme_nadine'),
                'text' => get_string('login_point_2_desc', 'theme_nadine'),
            ],
            [
                'icon' => theme_nadine_icon('shield-check'),
                'title' => get_string('login_point_3', 'theme_nadine'),
                'text' => get_string('login_point_3_desc', 'theme_nadine'),
            ],
        ],
        'sitename' => format_string(get_site()->fullname, true, ['context' => context_system::instance()]),
        'homeurl' => (new moodle_url('/'))->out(false),
        'supporturl' => !empty($CFG->supportemail) ? (new moodle_url('/user/contacts.php'))->out(false) : '',
        'supportlabel' => get_string('contact_support', 'theme_nadine'),
        'note' => get_string('login_panel_note', 'theme_nadine'),
    ];
}

/**
 * Return the presentation data for one course report link.
 *
 * The reports page lists whatever plugins add to the coursereports
 * navigation node, so the set changes with the plugins a site installs. The
 * path is the only stable identifier each link carries, so it decides the
 * glyph and the tint; anything unrecognised still gets a neutral icon and no
 * summary rather than being dropped.
 *
 * @param string $path Request path of the report URL.
 * @return array{icon: string, tone: string, description: string}
 */
function theme_nadine_get_report_meta(string $path): array {
    $map = [
        '/report/log/index.php' => ['scroll-text', 'log', 'report_summary_log'],
        '/report/loglive/index.php' => ['zap', 'live', 'report_summary_loglive'],
        '/report/outline/index.php' => ['list', 'outline', 'report_summary_outline'],
        '/report/participation/index.php' => ['users', 'participation', 'report_summary_participation'],
        '/report/competency/index.php' => ['target', 'competency', 'report_summary_competency'],
        '/report/aiusage/index.php' => ['sparkles', 'ai', 'report_summary_aiusage'],
        '/admin/tool/monitor/managerules.php' => ['gauge', 'monitor', 'report_summary_monitor'],
        '/report/progress/index.php' => ['chart-line', 'progress', 'report_summary_progress'],
        '/report/completion/index.php' => ['chart-bar', 'completion', 'report_summary_completion'],
        '/report/stats/index.php' => ['chart-column', 'stats', 'report_summary_stats'],
        '/report/usersessions/index.php' => ['clock', 'sessions', 'report_summary_sessions'],
    ];

    foreach ($map as $suffix => $meta) {
        if (substr($path, -strlen($suffix)) === $suffix) {
            return ['icon' => $meta[0], 'tone' => $meta[1], 'description' => $meta[2]];
        }
    }

    return ['icon' => 'chart-column', 'tone' => 'default', 'description' => ''];
}

/**
 * Build the card grid shown on the course reports page.
 *
 * Core renders /report/view.php as a bare list of links. The name and the URL
 * stay exactly as the plugin supplied them; this adds the icon, the tint and
 * the one-line summary so the page reads as a hub rather than a menu.
 *
 * @param \navigation_node $node The coursereports container.
 * @return array Mustache context for core/report_link_page.
 */
function theme_nadine_get_reports_context(\navigation_node $node): array {
    $reports = [];

    foreach ($node->children as $child) {
        if (empty($child->display) || empty($child->action)) {
            continue;
        }

        $url = $child->action instanceof \moodle_url
            ? $child->action->out(false)
            : (string) $child->action;
        $path = (string) parse_url($url, PHP_URL_PATH);
        $meta = theme_nadine_get_report_meta($path);

        $reports[] = [
            'url' => $url,
            'name' => $child->text,
            'summary' => $meta['description'] !== ''
                ? get_string($meta['description'], 'theme_nadine')
                : '',
            'icon' => theme_nadine_icon($meta['icon'], 'nl-report-card__glyph', 22),
            'tone' => $meta['tone'],
        ];
    }

    return [
        'reports' => $reports,
        'hasreports' => !empty($reports),
        'arrow' => theme_nadine_icon('arrow-right', 'nl-report-card__arrow-icon nl-directional', 18),
    ];
}
