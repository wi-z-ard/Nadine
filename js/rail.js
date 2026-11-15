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
 * Remembers whether the navigation rail is collapsed to an icon strip.
 *
 * The rail state is a checkbox, so the layout change is pure CSS. This script
 * only writes the choice back, so the next page renders at the same width
 * instead of springing open again.
 *
 * @module     theme_nadine/rail
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    'use strict';

    var input = document.getElementById('nl-left-rail-toggle');

    if (!input || !window.M || !M.cfg || !M.cfg.sesskey) {
        return;
    }

    var endpoint = M.cfg.wwwroot + '/theme/nadine/ajax/rail.php';
    var state = input.checked;
    var inflight = false;
    var queued = null;

    /**
     * Post the state, collapsing rapid toggles into the last value.
     *
     * @param {Boolean} collapsed
     */
    var save = function(collapsed) {
        if (inflight) {
            queued = collapsed;
            return;
        }
        inflight = true;

        var body = new URLSearchParams();
        body.set('collapsed', collapsed ? '1' : '0');
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

    input.addEventListener('change', function() {
        if (input.checked === state) {
            return;
        }
        state = input.checked;
        save(state);
    });
})();
