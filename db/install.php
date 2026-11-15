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
 * Install steps for the Nadine theme.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Enable the parent theme's colour mode feature so that the light and dark
 * appearance is available as soon as Nadine is selected.
 *
 * The value is only written when the site has not made a choice yet, so an
 * administrator who deliberately disabled colour modes keeps that decision.
 *
 * @return void
 */
function xmldb_theme_nadine_install(): void {
    // Snapshot the parent's settings before this plugin writes to them, so
    // uninstall can put the site back the way it was found.
    theme_nadine_backup_colour_modes();

    if (get_config('theme_boost', 'enablecolourmodes') === false) {
        set_config('enablecolourmodes', 1, 'theme_boost');
    }
}
