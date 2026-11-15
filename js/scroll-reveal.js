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
 * Homepage scroll reveal for the Nadine theme.
 *
 * Loaded only on the site home, and only when the "Homepage scroll animation"
 * setting is on. The mode arrives as a body class (nl-scroll--reveal or
 * nl-scroll--aos) so the stylesheet stays static and this file only has to
 * decide when an element is on screen.
 *
 * The script is requested in the head, which Moodle prints at the top of the
 * body, so it arms the hidden state before any page content is parsed: the
 * stylesheet hides a revealable element only while nl-reveal-ready is on the
 * root element. If this file never loads, or throws, that class is absent and
 * the page is simply visible.
 *
 * Visibility is measured on scroll rather than observed with
 * IntersectionObserver. An instant jump (an anchor, End, a restored scroll
 * position, find-in-page) can take an element from below the fold to above it
 * between two frames without ever intersecting, and an observer would then
 * leave it hidden for good.
 *
 * @module     theme_nadine/scroll-reveal
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    'use strict';

    var root = document.documentElement;
    var reduce = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;

    if ((reduce && reduce.matches) || !window.requestAnimationFrame) {
        return;
    }

    root.classList.add('nl-reveal-ready');

    /**
     * Reveals one element, then drops the markers so the element goes back to
     * its own transitions (card hover lifts, for example) once it is on screen.
     *
     * @param {HTMLElement} el
     */
    var reveal = function(el) {
        var done = function() {
            el.removeAttribute('data-nl-reveal');
            el.classList.remove('nl-reveal-in');
            el.style.removeProperty('--nl-reveal-delay');
        };

        el.classList.add('nl-reveal-in');
        el.addEventListener('transitionend', function handler(event) {
            if (event.propertyName !== 'opacity' || event.target !== el) {
                return;
            }
            el.removeEventListener('transitionend', handler);
            done();
        });
        // A transition that never fires (element hidden by an ancestor, or
        // transitions switched off) must not strand the marker.
        window.setTimeout(done, 2000);
    };

    var pending = [];
    var ticking = false;

    var check = function() {
        // A little short of the fold, so an element starts moving just before
        // its top edge would appear rather than after it has been read.
        var fold = window.innerHeight * 0.94;
        for (var i = pending.length - 1; i >= 0; i--) {
            var node = pending[i];
            // A negative top means the element is above the viewport: it was
            // scrolled past, so it is revealed without waiting for a frame it
            // will never get.
            if (node.getBoundingClientRect().top < fold) {
                pending.splice(i, 1);
                reveal(node);
            }
        }
        if (!pending.length) {
            window.removeEventListener('scroll', schedule);
            window.removeEventListener('resize', schedule);
        }
    };

    var schedule = function() {
        if (ticking) {
            return;
        }
        ticking = true;
        window.requestAnimationFrame(function() {
            ticking = false;
            check();
        });
    };

    var start = function() {
        var seen = new Map();
        document.querySelectorAll('[data-nl-reveal]').forEach(function(node) {
            // Items that share a parent animate in sequence, so a row of cards
            // or counters reads as one movement rather than a dozen separate
            // ones. The cap keeps the last card of a long row from arriving
            // late enough to look broken.
            var parent = node.parentElement;
            var index = seen.get(parent) || 0;
            seen.set(parent, index + 1);
            if (index > 0) {
                node.style.setProperty('--nl-reveal-delay', Math.min(index, 5) * 70 + 'ms');
            }
            pending.push(node);
        });

        if (!pending.length) {
            return;
        }

        window.addEventListener('scroll', schedule, {passive: true});
        window.addEventListener('resize', schedule, {passive: true});
        check();
    };

    try {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', start, {once: true});
        } else {
            start();
        }
    } catch (error) {
        // Never leave the page blank because of an animation.
        root.classList.remove('nl-reveal-ready');
    }
})();
