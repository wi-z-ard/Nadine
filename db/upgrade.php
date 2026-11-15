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
 * Upgrade steps for the Nadine theme.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the theme.
 *
 * @param int $oldversion The version being upgraded from.
 * @return bool
 */
function xmldb_theme_nadine_upgrade($oldversion) {
    if ($oldversion < 2026110707) {
        // Seed the editorial settings with the copy the theme prints today.
        // An empty field still falls back to the language string, but starting
        // from the live text means an administrator edits what they can see
        // instead of typing into a blank box.
        $defaults = [
            'heroeyebrow' => get_string('hero_eyebrow_default', 'theme_nadine'),
            'herotitle' => get_string('hero_title_default', 'theme_nadine'),
            'herotext' => get_string('hero_text_default', 'theme_nadine'),
            'heroimagealt' => get_string('hero_image_alt', 'theme_nadine'),
            'heroprimarylabel' => get_string('browsecourses', 'theme_nadine'),
            'heropanelkicker' => get_string('hero_art_title', 'theme_nadine'),
            'heropaneltext' => get_string('hero_art_text', 'theme_nadine'),
            'catalogueeyebrow' => get_string('catalogue_eyebrow', 'theme_nadine'),
            'cataloguetitle' => get_string('catalogue_title', 'theme_nadine'),
            'catalogueintro' => get_string('catalogue_intro', 'theme_nadine'),
            'featureseyebrow' => get_string('frontpage_features_eyebrow', 'theme_nadine'),
            'featurestitle' => get_string('frontpage_features_title', 'theme_nadine'),
            'feature1title' => get_string('frontpage_feature_1_title', 'theme_nadine'),
            'feature1text' => get_string('frontpage_feature_1_text', 'theme_nadine'),
            'feature2title' => get_string('frontpage_feature_2_title', 'theme_nadine'),
            'feature2text' => get_string('frontpage_feature_2_text', 'theme_nadine'),
            'feature3title' => get_string('frontpage_feature_3_title', 'theme_nadine'),
            'feature3text' => get_string('frontpage_feature_3_text', 'theme_nadine'),
            'ctatitle' => get_string('frontpage_cta_title', 'theme_nadine'),
            'ctatext' => get_string('frontpage_cta_text', 'theme_nadine'),
            'ctabrowselabel' => get_string('frontpage_cta_browse', 'theme_nadine'),
            'ctajoinlabel' => get_string('frontpage_cta_join', 'theme_nadine'),
            'dashboardeyebrow' => get_string('yourlearning', 'theme_nadine'),
            'dashboardgreeting' => 'Welcome back, {name}',
            'dashboardintro' => get_string('dashboard_intro', 'theme_nadine'),
            'logineyebrow' => get_string('login_eyebrow', 'theme_nadine'),
            'loginheading' => get_string('login_heading_default', 'theme_nadine'),
            'loginintro' => get_string('login_intro_default', 'theme_nadine'),
            'loginimagealt' => get_string('login_image_alt', 'theme_nadine'),
            'footertagline' => get_string('footer_tagline_default', 'theme_nadine'),
            'footerabout' => get_string('footer_about_default', 'theme_nadine'),
        ];

        foreach ($defaults as $name => $value) {
            if (!get_config('theme_nadine', $name)) {
                set_config($name, $value, 'theme_nadine');
            }
        }

        upgrade_plugin_savepoint(true, 2026110707, 'theme', 'nadine');
    }

    return true;
}
