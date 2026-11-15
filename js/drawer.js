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
 * Remembers the course index state for the signed-in reader.
 *
 * The index is collapsed by default. Core stores the same preference through a
 * REST route that is missing on some builds, so the state is written here
 * instead, with the same preference name, whenever the drawer opens or closes.
 *
 * @module     theme_nadine/drawer
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    'use strict';

    var page = document.getElementById('page');
    var drawer = document.getElementById('theme_boost-drawers-courseindex');

    if (!page || !drawer || !window.M || !M.cfg || !M.cfg.sesskey) {
        return;
    }

    var endpoint = M.cfg.wwwroot + '/theme/nadine/ajax/drawer.php';
    var state = page.classList.contains('show-drawer-left');
    var inflight = false;
    var queued = null;

    /**
     * Post the state, collapsing rapid toggles into the last value.
     *
     * @param {Boolean} open
     */
    var save = function(open) {
        if (inflight) {
            queued = open;
            return;
        }
        inflight = true;

        var body = new URLSearchParams();
        body.set('open', open ? '1' : '0');
        body.set('sesskey', M.cfg.sesskey);

        fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body.toString()
        }).catch(function() {
            // A failed write only means the default applies on the next page.
        }).then(function() {
            inflight = false;
            if (queued !== null && queued !== state) {
                var next = queued;
                queued = null;
                save(next);
            } else {
                queued = null;
            }
        });
    };

    // The server already rendered the stored state, so only a change made on
    // this page is worth writing back.
    var sync = function() {
        var open = page.classList.contains('show-drawer-left');
        if (open === state) {
            return;
        }
        state = open;
        save(open);
    };

    if (typeof MutationObserver === 'undefined') {
        return;
    }

    new MutationObserver(sync).observe(page, {attributes: true, attributeFilter: ['class']});
})();
