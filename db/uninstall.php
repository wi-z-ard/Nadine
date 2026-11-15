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
 * Uninstall steps for the Nadine theme.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Put Boost's colour mode settings back to what they were before Nadine.
 *
 * The theme copies its own colour mode controls into `theme_boost` so the
 * parent renderer resolves the mode. Those keys belong to the parent, so
 * removing Nadine has to restore them rather than leave the site carrying a
 * setting that only existed because of a theme that is no longer installed.
 *
 * @return bool
 */
function xmldb_theme_nadine_uninstall(): bool {
    $raw = get_config('theme_nadine', 'colourmodesbackup');

    if ($raw === false) {
        return true;
    }

    $backup = json_decode($raw, true);

    if (!is_array($backup)) {
        return true;
    }

    foreach (['enablecolourmodes', 'defaultcolourmode'] as $name) {
        if (empty($backup[$name]['set'])) {
            unset_config($name, 'theme_boost');
            continue;
        }

        set_config($name, $backup[$name]['value'], 'theme_boost');
    }

    return true;
}
