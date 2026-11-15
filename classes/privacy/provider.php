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

namespace theme_nadine\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy provider for the Nadine theme.
 *
 * The theme has no tables and stores no content. It writes two user
 * preferences: the course index state, from `ajax/drawer.php`, and whether the
 * navigation rail is collapsed to an icon strip, from `ajax/rail.php`.
 *
 * The drawer preference name is the one Boost uses. Nadine reuses it
 * deliberately: both themes drive the same `theme_boost-drawers-courseindex`
 * drawer, so a value written here has to be readable there. The parent theme
 * declares and exports the same key, which is why this provider describes the
 * preference again rather than inventing a second, divergent one.
 *
 * The rail preference is Nadine's own; Boost has no equivalent.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin stores personal data.
    \core_privacy\local\metadata\provider,
    // The data is a user preference, exported with the rest of the user's preferences.
    \core_privacy\local\request\user_preference_provider {
    /** The user preference holding the course index drawer state. */
    public const DRAWER_OPEN_INDEX = 'drawer-open-index';

    /** The user preference holding the navigation rail state. */
    public const NAV_RAIL_COLLAPSED = 'navrail-collapsed';

    /**
     * Describe the data this plugin stores.
     *
     * @param collection $items The initialised item collection to add items to.
     * @return collection
     */
    public static function get_metadata(collection $items): collection {
        $items->add_user_preference(
            self::DRAWER_OPEN_INDEX,
            'privacy:metadata:preference:draweropenindex'
        );
        $items->add_user_preference(
            self::NAV_RAIL_COLLAPSED,
            'privacy:metadata:preference:navrailcollapsed'
        );

        return $items;
    }

    /**
     * Export the user's course index and navigation rail preferences.
     *
     * @param int $userid The user whose data is being exported.
     */
    public static function export_user_preferences(int $userid) {
        $open = get_user_preferences(self::DRAWER_OPEN_INDEX, null, $userid);

        if ($open !== null) {
            writer::export_user_preference(
                'theme_nadine',
                self::DRAWER_OPEN_INDEX,
                $open,
                get_string($open ? 'privacy:drawerindexopen' : 'privacy:drawerindexclosed', 'theme_nadine')
            );
        }

        $collapsed = get_user_preferences(self::NAV_RAIL_COLLAPSED, null, $userid);

        if ($collapsed !== null) {
            writer::export_user_preference(
                'theme_nadine',
                self::NAV_RAIL_COLLAPSED,
                $collapsed,
                get_string($collapsed ? 'privacy:navrailcollapsed' : 'privacy:navrailexpanded', 'theme_nadine')
            );
        }
    }
}
