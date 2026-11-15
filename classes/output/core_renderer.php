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

namespace theme_nadine\output;

use html_writer;
use moodle_url;

/**
 * Core renderer for the Nadine theme.
 *
 * The renderer adds a small number of presentation helpers which are called
 * from the layout templates. It deliberately extends the Boost renderer so
 * that every core rendering path keeps working unchanged.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /** @var array<string, string|null> Memoised rendered fragments. */
    private array $nlcache = [];

    /**
     * Return the configured theme logo URL, falling back to the site logo.
     *
     * @param int|null $maxwidth
     * @param int $maxheight
     * @return moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $url = theme_nadine_get_setting_file_url('logo', 'logo');
        if (!empty($url)) {
            return new moodle_url($url);
        }

        return parent::get_logo_url($maxwidth, $maxheight);
    }

    /**
     * Return the configured theme logo URL, falling back to the compact site logo.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $url = theme_nadine_get_setting_file_url('logo', 'logo');
        if (!empty($url)) {
            return new moodle_url($url);
        }

        return parent::get_compact_logo_url($maxwidth, $maxheight);
    }

    /**
     * Return the configured favicon URL, falling back to the site favicon.
     *
     * @return string
     */
    public function favicon(): string {
        $url = theme_nadine_get_setting_file_url('favicon', 'favicon');
        if (!empty($url)) {
            return $url;
        }

        return parent::favicon();
    }

    /**
     * Append the rest of the theme's icon set to the page head.
     *
     * Core prints one `rel="shortcut icon"` link, built from {@see favicon()}.
     * Browsers also look for an SVG icon, a 96px PNG for pinned tabs, an Apple
     * touch icon for the home screen and a web app manifest. Those are added
     * here rather than in a template, where every layout would repeat them.
     *
     * The packaged files are defaults. Once an administrator uploads a favicon
     * in the theme settings that file is the whole identity, so the defaults are
     * dropped instead of competing with it.
     *
     * @return string
     */
    public function standard_head_html() {
        $html = parent::standard_head_html();

        if (!empty(get_config('theme_nadine', 'favicon'))) {
            return $html;
        }

        $revision = theme_get_revision();
        $icon = function (string $file) use ($revision): string {
            return (new moodle_url('/theme/nadine/pix/' . $file, ['rev' => $revision]))->out(false);
        };

        $links = [
            ['rel' => 'icon', 'type' => 'image/svg+xml', 'href' => $icon('favicon.svg')],
            ['rel' => 'icon', 'type' => 'image/png', 'sizes' => '96x96', 'href' => $icon('favicon-96x96.png')],
            ['rel' => 'apple-touch-icon', 'sizes' => '180x180', 'href' => $icon('apple-touch-icon.png')],
            ['rel' => 'manifest', 'href' => $icon('site.webmanifest')],
        ];

        foreach ($links as $attributes) {
            $html .= html_writer::empty_tag('link', $attributes) . "\n";
        }

        return $html;
    }

    /**
     * Mark the course reports page so its own layout rules can target it.
     *
     * Core sets the pagetype of /report/view.php to `course-view-<format>`, so
     * the body classes cannot tell it apart from the course home page. The URL
     * can.
     *
     * @param array|string $additionalclasses Classes to append.
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }

        if (strpos($this->page->url->get_path(), '/report/view.php') !== false) {
            $additionalclasses[] = 'nl-report-hub';
        }

        return parent::body_attributes($additionalclasses);
    }

    /**
     * Enrich the course reports template with the card data.
     *
     * Core hands `core/report_link_page` the raw navigation node, which carries
     * only a name and a URL per report. The theme's override of that template
     * renders a card grid, so the extra fields are added here; the node itself
     * stays in the context so an unknown template can still fall back to the
     * core list.
     *
     * @param string $templatename The template to render.
     * @param array|\stdClass $context Context containing data for the template.
     * @return string|bool
     */
    public function render_from_template($templatename, $context) {
        if (
            $templatename === 'core/report_link_page' && is_array($context)
                && !empty($context['node']) && $context['node'] instanceof \navigation_node
        ) {
            $context = array_merge($context, theme_nadine_get_reports_context($context['node']));
        }

        return parent::render_from_template($templatename, $context);
    }

    /**
     * Render the site brand: uploaded logo, or the theme logo with the site name.
     *
     * @param string $variant Optional variant: "footer" or "login".
     * @return string
     */
    public function nadine_brand(string $variant = ''): string {
        $key = 'brand_' . $variant;
        if (array_key_exists($key, $this->nlcache)) {
            return $this->nlcache[$key];
        }

        $logo = theme_nadine_get_logo_url($this);
        $sitename = format_string(get_site()->fullname, true, ['context' => \context_system::instance()]);
        $classes = 'nl-brand';
        if ($variant !== '') {
            $classes .= ' nl-brand--' . $variant;
        }

        if (!empty($logo)) {
            // The site name follows the mark, so the image is decorative to
            // avoid announcing the same name twice.
            $image = html_writer::empty_tag('img', [
                'src' => $logo,
                'alt' => '',
                'aria-hidden' => 'true',
                'class' => 'nl-brand__logo',
                'height' => '40',
            ]);
            $content = $image . html_writer::span($sitename, 'nl-brand__name');
        } else {
            $mark = $this->image_url('logo', 'theme_nadine');
            $image = html_writer::empty_tag('img', [
                'src' => $mark->out(false),
                'alt' => '',
                'aria-hidden' => 'true',
                'class' => 'nl-brand__mark',
                'width' => '34',
                'height' => '34',
            ]);
            $content = $image . html_writer::span($sitename, 'nl-brand__name');
        }

        $this->nlcache[$key] = html_writer::link(
            new moodle_url('/'),
            $content,
            ['class' => $classes, 'title' => $sitename]
        );

        return $this->nlcache[$key];
    }

    /**
     * Render the public homepage hero.
     *
     * @return string Empty string when the current page is not the site home.
     */
    public function nadine_hero(): string {
        if (array_key_exists('hero', $this->nlcache)) {
            return $this->nlcache['hero'] ?? '';
        }

        $this->nlcache['hero'] = null;
        if ($this->page->pagelayout !== 'frontpage' || !theme_nadine_hero_enabled()) {
            return '';
        }

        $context = theme_nadine_get_hero_context($this);
        if ($context === null) {
            return '';
        }

        $this->nlcache['hero'] = $this->render_from_template('theme_nadine/hero', $context);

        return $this->nlcache['hero'];
    }

    /**
     * Render the course header band.
     *
     * @return string Empty string outside course home pages.
     */
    public function nadine_course_hero(): string {
        if (array_key_exists('coursehero', $this->nlcache)) {
            return $this->nlcache['coursehero'] ?? '';
        }

        $this->nlcache['coursehero'] = null;
        $context = theme_nadine_get_course_hero_context($this);
        if ($context === null) {
            return '';
        }

        $this->nlcache['coursehero'] = $this->render_from_template('theme_nadine/course_hero', $context);

        return $this->nlcache['coursehero'];
    }

    /**
     * Render the homepage counters strip, directly under the hero.
     *
     * @return string Empty string outside the site home.
     */
    public function nadine_frontpage_intro(): string {
        if ($this->page->pagelayout !== 'frontpage') {
            return '';
        }

        return $this->render_from_template(
            'theme_nadine/frontpage_stats',
            theme_nadine_get_frontpage_stats($this)
        );
    }

    /**
     * Render the homepage feature grid and closing call to action.
     *
     * @return string Empty string outside the site home.
     */
    public function nadine_frontpage_outro(): string {
        if ($this->page->pagelayout !== 'frontpage') {
            return '';
        }

        return $this->render_from_template(
            'theme_nadine/frontpage_outro',
            theme_nadine_get_frontpage_outro($this)
        );
    }

    /**
     * Render the signed-in learner's own courses on the site home.
     *
     * Core already prints every available course further down the page, so this
     * only ever shows the viewer's own enrolments and stays out of the way for
     * guests.
     *
     * @return string Empty string for guests, on other layouts, or when off.
     */
    public function nadine_frontpage_continue(): string {
        if ($this->page->pagelayout !== 'frontpage') {
            return '';
        }

        if (array_key_exists('frontpagecontinue', $this->nlcache)) {
            return $this->nlcache['frontpagecontinue'];
        }

        $this->nlcache['frontpagecontinue'] = '';

        if (!isloggedin() || isguestuser()) {
            return '';
        }

        $settings = \theme_config::load('nadine')->settings;
        if (isset($settings->showfrontpagecontinue) && empty($settings->showfrontpagecontinue)) {
            return '';
        }

        $context = theme_nadine_get_continue_courses($this, $GLOBALS['USER']->id);
        if (empty($context['hascourses'])) {
            return '';
        }

        $this->nlcache['frontpagecontinue'] = $this->render_from_template(
            'theme_nadine/frontpage_continue',
            $context
        );

        return $this->nlcache['frontpagecontinue'];
    }

    /**
     * Render the dashboard welcome panel.
     *
     * Only the dashboard itself gets the greeting. Other pages that ask for
     * the mydashboard layout - the message index is the one that matters -
     * are applications, not dashboards, and the panel pushed their own
     * content a third of the way down the screen.
     *
     * @return string Empty string outside the dashboard.
     */
    public function nadine_dashboard_welcome(): string {
        if (array_key_exists('dashboard', $this->nlcache)) {
            return $this->nlcache['dashboard'] ?? '';
        }

        $this->nlcache['dashboard'] = null;
        if ($this->page->pagelayout !== 'mydashboard' || $this->page->pagetype !== 'my-index') {
            return '';
        }

        $context = theme_nadine_get_dashboard_context($this);
        if ($context === null) {
            return '';
        }

        $this->nlcache['dashboard'] = $this->render_from_template('theme_nadine/dashboard_welcome', $context);

        return $this->nlcache['dashboard'];
    }

    /**
     * Render the course catalogue introduction.
     *
     * @return string Empty string outside course discovery pages.
     */
    public function nadine_catalogue_intro(): string {
        if (array_key_exists('catalogue', $this->nlcache)) {
            return $this->nlcache['catalogue'] ?? '';
        }

        $this->nlcache['catalogue'] = null;
        $pagetype = $this->page->pagetype;
        $iscatalogue = str_starts_with($pagetype, 'course-index') || $pagetype === 'course-search';

        if (!$iscatalogue) {
            return '';
        }

        $this->nlcache['catalogue'] = $this->render_from_template(
            'theme_nadine/catalogue_intro',
            theme_nadine_get_catalogue_context($this)
        );

        return $this->nlcache['catalogue'];
    }

    /**
     * Render the identity strip under the page header on a public profile.
     *
     * @return string Empty string on every other page.
     */
    public function nadine_profile_meta(): string {
        if (array_key_exists('profile', $this->nlcache)) {
            return $this->nlcache['profile'] ?? '';
        }

        $this->nlcache['profile'] = null;
        $context = theme_nadine_get_profile_context();
        if ($context === null) {
            return '';
        }

        $this->nlcache['profile'] = $this->render_from_template('theme_nadine/profile_meta', $context);

        return $this->nlcache['profile'];
    }

    /**
     * Render the theme footer.
     *
     * @return string
     */
    public function nadine_footer(): string {
        if (!array_key_exists('footer', $this->nlcache)) {
            $this->nlcache['footer'] = $this->render_from_template(
                'theme_nadine/footer',
                theme_nadine_get_footer_context($this)
            );
        }

        return $this->nlcache['footer'] ?? '';
    }

    /**
     * Render the branding panel of the split login layout.
     *
     * @return string
     */
    public function nadine_login_panel(): string {
        if (!array_key_exists('loginpanel', $this->nlcache)) {
            $this->nlcache['loginpanel'] = $this->render_from_template(
                'theme_nadine/login_panel',
                theme_nadine_get_login_panel_context($this)
            );
        }

        return $this->nlcache['loginpanel'] ?? '';
    }

    /**
     * Render sign-in and registration actions for visitors.
     *
     * @return string
     */
    public function nadine_auth_actions(): string {
        if (isloggedin() && !isguestuser()) {
            return '';
        }

        global $CFG;

        // Each action carries its own glyph so the pair reads as a door and a
        // person, not as two identical pills. The icons are decorative: the
        // link text already names the action.
        $html = html_writer::link(
            new moodle_url('/login/index.php'),
            theme_nadine_icon('log-in', 'nl-auth-login__icon nl-directional', 16)
                . html_writer::span(get_string('signin', 'theme_nadine'), 'nl-auth-login__label'),
            ['class' => 'btn btn-outline-secondary btn-sm nl-auth-login']
        );

        if (!empty($CFG->registerauth)) {
            $html .= html_writer::link(
                new moodle_url('/login/signup.php'),
                theme_nadine_icon('user-plus', 'nl-auth-signup__icon', 16)
                    . html_writer::span(get_string('createaccount', 'theme_nadine'), 'nl-auth-signup__label'),
                ['class' => 'btn btn-primary btn-sm nl-auth-signup']
            );
        }

        return html_writer::div($html, 'nl-auth-actions');
    }

    /**
     * Render the docked primary navigation rail.
     *
     * The rail is a theme-level quick navigation for signed-in users. It is
     * hidden below 1200px, where the stock Boost primary drawer takes over, so
     * the markup is always rendered and CSS decides which one is in play.
     *
     * @return string
     */
    public function nadine_primary_rail(): string {
        if (array_key_exists('primaryrail', $this->nlcache)) {
            return $this->nlcache['primaryrail'];
        }

        if (!isloggedin() || isguestuser()) {
            $this->nlcache['primaryrail'] = '';
            return '';
        }

        $canmanage = has_capability('moodle/site:config', \context_system::instance());

        $main = [
            ['/my/', get_string('myhome', 'moodle'), theme_nadine_icon('gauge'), '#0d9488'],
            ['/my/courses.php', get_string('mycourses', 'moodle'), theme_nadine_icon('book-open'), '#0284c7'],
            ['/calendar/view.php', get_string('calendar', 'calendar'), theme_nadine_icon('calendar-days'), '#d97706'],
            ['/grade/report/overview/index.php', get_string('grades', 'moodle'), theme_nadine_icon('table-2'), '#7c3aed'],
            ['/message/index.php', get_string('messages', 'message'), theme_nadine_icon('message-square-text'), '#db2777'],
        ];

        $manage = [];
        if ($canmanage) {
            $manage = [
                ['/admin/user.php', get_string('users', 'admin'), theme_nadine_icon('users'), '#0d9488'],
                ['/course/management.php', get_string('courses', 'moodle'), theme_nadine_icon('graduation-cap'), '#4f46e5'],
                ['/admin/reports.php', get_string('reports', 'moodle'), theme_nadine_icon('chart-column'), '#0891b2'],
                ['/badges/index.php?type=1', get_string('badges', 'badges'), theme_nadine_icon('award'), '#ca8a04'],
            ];
        }

        $groups = [
            ['title' => '', 'items' => $this->nadine_rail_items($main)],
        ];
        if (!empty($manage)) {
            $groups[] = [
                'title' => get_string('administration', 'theme_nadine'),
                'items' => $this->nadine_rail_items($manage),
            ];
        }

        $footer = [];
        if ($canmanage) {
            $footer[] = [
                'url' => (new moodle_url('/admin/search.php'))->out(false),
                'label' => get_string('administrationsite', 'moodle'),
                'icon' => theme_nadine_icon('settings'),
                'color' => '#475569',
                'active' => false,
            ];
        }
        $footer[] = [
            'url' => (new moodle_url('/user/contactsitesupport.php'))->out(false),
            'label' => get_string('helpandsupport', 'theme_nadine'),
            'icon' => theme_nadine_icon('circle-help'),
            'color' => '#64748b',
            'active' => false,
        ];

        $this->nlcache['primaryrail'] = $this->render_from_template('theme_nadine/primary_rail', [
            'brand' => $this->nadine_brand(),
            'collapseicon' => theme_nadine_icon('chevron-left', 'nl-directional', 14),
            'groups' => $groups,
            'footer' => $footer,
            'user' => [
                'name' => fullname($GLOBALS['USER']),
                'url' => (new moodle_url('/user/profile.php', ['id' => $GLOBALS['USER']->id]))->out(false),
                // Drawn at 1.9rem but requested at 50, the smallest size that
                // returns the 100px icon; below that core serves a 35px file
                // that is soft once the browser scales it for a retina screen.
                'picture' => $this->user_picture($GLOBALS['USER'], ['size' => 50, 'link' => false, 'class' => 'nl-rail__avatar']),
            ],
        ]);

        return $this->nlcache['primaryrail'];
    }

    /**
     * Normalise rail item definitions and flag the current page.
     *
     * @param array $items List of [path, label, icon, colour] tuples.
     * @return array
     */
    private function nadine_rail_items(array $items): array {
        $current = $this->page->url->get_path();
        $out = [];

        foreach ($items as [$path, $label, $icon, $color]) {
            $target = (new moodle_url($path))->get_path();
            $active = $target === '/my/'
                ? strpos($current, '/my/') === 0
                : ($current === $target || strpos($current, rtrim($target, '/') . '/') === 0);

            $out[] = [
                'url' => (new moodle_url($path))->out(false),
                'label' => $label,
                'icon' => $icon,
                'color' => $color,
                'active' => $active,
            ];
        }

        return $out;
    }

    /**
     * Render the page skip links.
     *
     * Core always adds a link to the empty #maincontent span at the top of the
     * body. The theme renders its own skip link that targets the main landmark
     * (#region-main), so the core duplicate is dropped. Skip links registered
     * by other components are preserved.
     *
     * @param array $links List of skip links, keyed by target.
     * @return string
     */
    public function render_skip_links($links) {
        unset($links['maincontent']);
        return parent::render_skip_links($links);
    }
}
