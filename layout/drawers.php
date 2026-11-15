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
 * Drawer based layout for the Nadine theme.
 *
 * The context is identical to the Boost drawer layout so that the course
 * index, block drawers, secondary navigation and editing controls continue to
 * work. The template is the theme's own copy.
 *
 * @package   theme_nadine
 * @copyright 2026 EduPro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

// Homepage counters count up once they scroll into view. The markup ships the
// final figure as its text, so the page is correct without JavaScript and this
// only replaces it with the same number mid-animation.
if ($PAGE->pagelayout === 'frontpage') {
    $counterjs = <<<'JS'
(function() {
    var nodes = document.querySelectorAll('[data-nl-counter]');
    if (!nodes.length) {
        return;
    }
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var format = new Intl.NumberFormat(document.documentElement.lang || 'en');
    var animate = function(el) {
        var target = parseInt(el.getAttribute('data-nl-counter'), 10);
        if (isNaN(target)) {
            return;
        }
        if (reduce) {
            el.textContent = format.format(target);
            return;
        }
        var started = null;
        var duration = 1500;
        var step = function(now) {
            if (started === null) {
                started = now;
            }
            var progress = Math.min(1, (now - started) / duration);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = format.format(Math.round(target * eased));
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    observer.unobserve(entry.target);
                    animate(entry.target);
                }
            });
        }, {threshold: 0.35});
        nodes.forEach(function(node) {
            observer.observe(node);
        });
    } else {
        nodes.forEach(animate);
    }
})();
JS;
    $PAGE->requires->js_init_code($counterjs);
}

// The live search field only exists on the site home and the course discovery
// pages, so the script is only requested there.
$nadinesearchpage = $PAGE->pagelayout === 'frontpage'
    || str_starts_with($PAGE->pagetype, 'course-index')
    || $PAGE->pagetype === 'course-search';
if ($nadinesearchpage) {
    $PAGE->requires->js(new moodle_url('/theme/nadine/js/search.js'));
}

// The form "Expand all / Collapse all" control is on every mform, so the fix
// for its state handling ships with the layout rather than one page.
$PAGE->requires->js(new moodle_url('/theme/nadine/js/collapse-all.js'));

if (isloggedin()) {
    // The course index starts collapsed so the course takes the full width.
    // Once a user opens it their choice is stored and wins on later visits.
    $courseindexopen = (get_user_preferences('drawer-open-index', false) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
    // The navigation rail starts expanded; the icon-rail state is remembered so
    // the frame does not widen again on the next page.
    $navrailcollapsed = (get_user_preferences('navrail-collapsed', false) == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
    $navrailcollapsed = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

// The rail state is a checkbox, so it is instant; this only writes the choice
// back so the next page renders in the same state.
if (isloggedin()) {
    $PAGE->requires->js(new moodle_url('/theme/nadine/js/rail.js'));
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

// The site home can bring its sections in as the visitor scrolls. The mode is
// a theme setting, carried on the body as a class so the stylesheet stays
// static, and the script is requested in the head so it can arm the hidden
// state before the first paint instead of flashing the page in and out.
if ($PAGE->pagelayout === 'frontpage') {
    $scrollanimation = get_config('theme_nadine', 'homescrollanimation');
    if (in_array($scrollanimation, ['reveal', 'aos'], true)) {
        $extraclasses[] = 'nl-scroll--' . $scrollanimation;
        $PAGE->requires->js(new moodle_url('/theme/nadine/js/scroll-reveal.js'), true);
    }
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
} else {
    // The index is collapsed by default, so the reader's choice is written back
    // by the theme rather than through core's REST route, which is not present
    // on every build.
    $PAGE->requires->js(new moodle_url('/theme/nadine/js/drawer.js'));
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $selectmenu = new \core\output\select_menu(
            'tertiarynavigation',
            $overflowdata->urls,
            $overflowdata->selected,
        );
        $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
        $overflow = $selectmenu->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$coursefullname = ($PAGE->course?->fullname) ? format_string(
    $PAGE->course->fullname,
    true,
    ['context' => context_course::instance($PAGE->course->id), 'escape' => false],
) : '';
$courseurl = $PAGE->course ? new \core\url('/course/view.php', ['id' => $PAGE->course->id]) : null;

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'coursefullname' => $coursefullname,
    'courseurl' => $courseurl ? $courseurl->out(false) : null,
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'navrailcollapsed' => $navrailcollapsed,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'panelcollapseicon' => theme_nadine_icon('chevron-right', 'nl-directional', 14),
    'drawerlefticon' => theme_nadine_icon('panel-left', 'nl-directional', 20),
    'drawerrighticon' => theme_nadine_icon('panel-right', 'nl-directional', 20),
    'railtoggleicon' => theme_nadine_icon('chevron-right', 'nl-directional', 14),
];

echo $OUTPUT->render_from_template('theme_nadine/drawers', $templatecontext);
