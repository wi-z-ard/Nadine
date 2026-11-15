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
 * Live course search for the Nadine hero and catalogue fields.
 *
 * Plain ES5 on purpose: it must run without Moodle's AMD loader, which is not
 * available to the public front page. Every value coming back from the server
 * is written with textContent, never as markup.
 *
 * @package   theme_nadine
 * @copyright 2026 EduPro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function () {
    'use strict';

    var DEBOUNCE = 220;
    var instances = [];

    /**
     * Build one result row.
     *
     * @param {Object} item
     * @param {String} id
     * @returns {HTMLElement}
     */
    function buildRow(item, id) {
        var row = document.createElement('li');
        row.className = 'nl-result';
        row.id = id;
        row.setAttribute('role', 'option');
        row.setAttribute('aria-selected', 'false');

        var link = document.createElement('a');
        link.className = 'nl-result__link';
        link.href = item.url;

        var icon = document.createElement('span');
        icon.className = 'nl-result__icon';
        icon.setAttribute('aria-hidden', 'true');
        var glyph = document.createElement('i');
        glyph.className = 'fa fa-graduation-cap';
        icon.appendChild(glyph);

        var body = document.createElement('span');
        body.className = 'nl-result__body';

        var title = document.createElement('span');
        title.className = 'nl-result__title';
        title.textContent = item.name || '';

        var meta = document.createElement('span');
        meta.className = 'nl-result__meta';
        var bits = [];
        if (item.category) {
            bits.push(item.category);
        }
        if (item.summary) {
            bits.push(item.summary);
        }
        meta.textContent = bits.join(' \u00b7 ');

        body.appendChild(title);
        body.appendChild(meta);
        link.appendChild(icon);
        link.appendChild(body);
        row.appendChild(link);

        return row;
    }

    /**
     * Wire one search form.
     *
     * @param {HTMLFormElement} form
     */
    function init(form) {
        var input = form.querySelector('[data-nl-search-input]');
        var panel = form.querySelector('[data-nl-search-panel]');
        var list = form.querySelector('[data-nl-search-results]');
        var status = form.querySelector('[data-nl-search-status]');
        var allLink = form.querySelector('[data-nl-search-all]');
        var clearButton = form.querySelector('[data-nl-search-clear]');
        var spinner = form.querySelector('[data-nl-search-spinner]');

        if (!input || !panel || !list || !status) {
            return;
        }

        var endpoint = form.getAttribute('data-nl-endpoint');
        var sesskey = form.getAttribute('data-nl-sesskey');
        if (!endpoint || !sesskey) {
            return;
        }

        var minChars = parseInt(form.getAttribute('data-nl-minchars'), 10) || 2;
        var strings = {
            none: form.getAttribute('data-nl-str-none') || '',
            count: form.getAttribute('data-nl-str-count') || '',
            error: form.getAttribute('data-nl-str-error') || '',
            loading: form.getAttribute('data-nl-str-loading') || ''
        };

        var timer = null;
        var controller = null;
        var rows = [];
        var active = -1;
        var pending = '';
        var busy = false;

        function optionId(index) {
            return list.id + '-option-' + index;
        }

        function setBusy(state) {
            busy = state;
            form.classList.toggle('is-busy', state);
            panel.setAttribute('aria-busy', state ? 'true' : 'false');
            if (spinner) {
                spinner.hidden = !state;
            }
        }

        function openPanel() {
            if (!panel.hidden) {
                return;
            }
            panel.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        }

        function closePanel() {
            panel.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            input.removeAttribute('aria-activedescendant');
            active = -1;
            rows.forEach(function (row) {
                row.classList.remove('is-active');
                row.setAttribute('aria-selected', 'false');
            });
        }

        function resetRows() {
            while (list.firstChild) {
                list.removeChild(list.firstChild);
            }
            rows = [];
            active = -1;
            input.removeAttribute('aria-activedescendant');
        }

        function setActive(index) {
            if (!rows.length) {
                return;
            }
            if (active >= 0 && rows[active]) {
                rows[active].classList.remove('is-active');
                rows[active].setAttribute('aria-selected', 'false');
            }
            active = (index + rows.length) % rows.length;
            var row = rows[active];
            row.classList.add('is-active');
            row.setAttribute('aria-selected', 'true');
            input.setAttribute('aria-activedescendant', row.id);
            if (row.scrollIntoView) {
                row.scrollIntoView({block: 'nearest'});
            }
        }

        function render(data, value) {
            resetRows();

            var results = data.results || [];
            if (!results.length) {
                status.textContent = strings.none.replace('{$a}', value);
                if (allLink) {
                    allLink.hidden = true;
                }
                openPanel();
                return;
            }

            results.forEach(function (item, index) {
                var row = buildRow(item, optionId(index));
                list.appendChild(row);
                rows.push(row);
            });

            var total = typeof data.total === 'number' ? data.total : results.length;
            status.textContent = strings.count.replace('{$a}', String(total));

            if (allLink) {
                allLink.hidden = total <= results.length;
                if (data.moreurl) {
                    allLink.href = data.moreurl;
                }
            }

            openPanel();
        }

        function abort() {
            if (controller) {
                controller.abort();
                controller = null;
            }
        }

        function query(value) {
            abort();
            pending = value;
            setBusy(true);
            openPanel();
            resetRows();
            status.textContent = strings.loading;

            var body = 'q=' + encodeURIComponent(value) + '&sesskey=' + encodeURIComponent(sesskey);
            var options = {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body
            };

            if (window.AbortController) {
                controller = new AbortController();
                options.signal = controller.signal;
            }

            window.fetch(endpoint, options)
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (value !== pending) {
                        return;
                    }
                    render(data, value);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    if (value !== pending) {
                        return;
                    }
                    resetRows();
                    status.textContent = strings.error;
                    openPanel();
                })
                .then(function () {
                    if (value === pending) {
                        setBusy(false);
                    }
                });
        }

        function schedule() {
            var value = input.value.trim();
            window.clearTimeout(timer);

            if (clearButton) {
                clearButton.hidden = !input.value.length;
            }

            if (value.length < minChars) {
                abort();
                pending = '';
                resetRows();
                closePanel();
                status.textContent = '';
                setBusy(false);
                return;
            }

            if (value === pending) {
                return;
            }

            timer = window.setTimeout(function () {
                query(value);
            }, DEBOUNCE);
        }

        input.addEventListener('input', schedule);

        input.addEventListener('focus', function () {
            if (rows.length && input.value.trim().length >= minChars) {
                openPanel();
            }
        });

        input.addEventListener('keydown', function (event) {
            var key = event.key;

            if (key === 'ArrowDown') {
                event.preventDefault();
                if (panel.hidden && input.value.trim().length >= minChars) {
                    schedule();
                    return;
                }
                setActive(active + 1);
            } else if (key === 'ArrowUp') {
                event.preventDefault();
                setActive(active - 1);
            } else if (key === 'Home' && !panel.hidden) {
                event.preventDefault();
                setActive(0);
            } else if (key === 'End' && !panel.hidden) {
                event.preventDefault();
                setActive(rows.length - 1);
            } else if (key === 'Enter') {
                if (!panel.hidden && active >= 0 && rows[active]) {
                    event.preventDefault();
                    var link = rows[active].querySelector('a');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            } else if (key === 'Escape') {
                if (!panel.hidden) {
                    event.preventDefault();
                    closePanel();
                }
            }
        });

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                abort();
                pending = '';
                input.value = '';
                input.focus();
                resetRows();
                closePanel();
                status.textContent = '';
                clearButton.hidden = true;
                setBusy(false);
            });
        }

        document.addEventListener('click', function (event) {
            if (!form.contains(event.target)) {
                closePanel();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }
            var target = event.target;
            var tag = target && target.tagName ? target.tagName.toLowerCase() : '';
            if (tag === 'input' || tag === 'textarea' || tag === 'select' || (target && target.isContentEditable)) {
                return;
            }
            event.preventDefault();
            input.focus();
        });

        form.dataset.nlSearchReady = '1';
        instances.push({form: form, input: input});
    }

    function boot() {
        var forms = document.querySelectorAll('form[data-nl-search]');
        Array.prototype.forEach.call(forms, init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.themeNadineSearch = {instances: instances};
})();
