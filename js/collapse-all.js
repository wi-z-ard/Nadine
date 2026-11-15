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
 * Makes the form's "Expand all / Collapse all" control follow the sections.
 *
 * Core's `core_form/collapsesections` decides the direction of the toggle from
 * a class that it only rewrites once every transition has finished, and it only
 * rewrites it when every section agrees. A form that opens with some sections
 * expanded therefore keeps offering "Expand all", and a second click expands
 * instead of collapsing. This module reads the sections themselves, so the
 * label always names the action the next click performs.
 *
 * The click is intercepted in the capture phase, before core's own listener on
 * the link, so the two never both act on one click.
 *
 * @module     theme_nadine/collapse-all
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    'use strict';

    var MENU = '.mform .collapsible-actions .collapseexpand';
    var CONTAINER = 'fieldset > .fcontainer';

    /**
     * The collapsible bodies of one form, in document order.
     *
     * @param {Element} form
     * @returns {Array}
     */
    var containers = function(form) {
        return Array.prototype.slice.call(form.querySelectorAll(CONTAINER));
    };

    /**
     * @param {Element} container
     * @returns {Boolean}
     */
    var isOpen = function(container) {
        return container.classList.contains('show');
    };

    /**
     * Open or close one section through its own header link.
     *
     * Going through the header keeps Bootstrap's transition, the chevron and the
     * header's `aria-expanded` in step with the container, which a direct class
     * change would not.
     *
     * @param {Element} container
     * @param {Boolean} open
     */
    var setOpen = function(container, open) {
        var fieldset = container.parentElement;
        var header = fieldset ? fieldset.querySelector('.fheader[data-bs-toggle="collapse"]') : null;

        // A section behind "Show more" is not laid out, so it cannot run a
        // height transition: Bootstrap would leave it stuck half-open. Its
        // state is written directly instead, ready for when it is revealed.
        if (!header || fieldset.offsetParent === null) {
            container.classList.toggle('show', open);
            container.classList.remove('collapsing');
            container.style.height = '';
            if (header) {
                header.setAttribute('aria-expanded', open ? 'true' : 'false');
                header.classList.toggle('collapsed', !open);
            }
            return;
        }

        if ((header.getAttribute('aria-expanded') === 'true') !== open) {
            header.click();
        }
    };

    /**
     * Paint the control for a known state.
     *
     * @param {Element} menu
     * @param {Boolean} open
     */
    var setMenu = function(menu, open) {
        menu.classList.toggle('collapsed', !open);
        menu.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    /**
     * Re-read the sections and paint the control.
     *
     * @param {Element} menu
     */
    var refresh = function(menu) {
        var form = menu.closest('.mform');
        if (!form) {
            return;
        }
        var list = containers(form);
        if (list.length) {
            setMenu(menu, list.some(isOpen));
        }
    };

    document.addEventListener('click', function(event) {
        var menu = event.target.closest ? event.target.closest(MENU) : null;
        if (!menu) {
            return;
        }

        // Core's listener is on the link itself; stopping the event here keeps
        // it from running after this one.
        event.preventDefault();
        event.stopPropagation();

        var form = menu.closest('.mform');
        var list = form ? containers(form) : [];
        if (!list.length) {
            return;
        }

        // Any open section means the control offers to close them all. With
        // none open there is only one useful action left.
        var open = !list.some(isOpen);
        list.forEach(function(container) {
            setOpen(container, open);
        });
        setMenu(menu, open);
    }, true);

    // A section toggled from its own header changes the answer too.
    ['shown.bs.collapse', 'hidden.bs.collapse'].forEach(function(type) {
        document.addEventListener(type, function(event) {
            var form = event.target.closest ? event.target.closest('.mform') : null;
            if (!form) {
                return;
            }
            var menu = form.querySelector('.collapsible-actions .collapseexpand');
            if (menu) {
                refresh(menu);
            }
        }, true);
    });

    var init = function() {
        document.querySelectorAll(MENU).forEach(refresh);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
