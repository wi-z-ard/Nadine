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

namespace theme_nadine\setting;

/**
 * Colour theme setting.
 *
 * A plain select asks an administrator to imagine what "Ocean" looks like. This
 * renders the same choice as a grid of swatch cards, so the palette is visible
 * before it is applied, and keeps the native radio group underneath: the value
 * still posts as `s_theme_nadine_colortheme`, keyboard arrow navigation
 * still works, and the custom colours remain a dependency of the same field.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class colourtheme extends \core_admin\setting\setting\configselect {
    /**
     * The setting renders its own control, so the form prints the title as a
     * paragraph rather than a `<label for>` pointing at nothing.
     *
     * @return bool
     */
    public function has_custom_form_control(): bool {
        return true;
    }

    /**
     * Return the swatch grid.
     *
     * @param string $data Current value passed by the form.
     * @param string $query Admin search query.
     * @return string
     */
    public function output_html($data, $query = '') {
        $themes = theme_nadine_get_color_themes();

        $current = $this->get_setting();
        if ($current === null || $current === false) {
            $current = $this->get_defaultsetting();
        }
        $current = (string) $current;

        $name = $this->get_full_name();
        $id = $this->get_id();

        $cards = '';
        foreach ($this->choices as $key => $label) {
            $key = (string) $key;
            $checked = $current === $key;

            $attributes = [
                'class' => 'nl-ct__card',
            ];
            if ($key === 'custom') {
                $attributes['class'] .= ' nl-ct__card--custom';
            } else {
                $palette = $themes[$key] ?? $themes['default'];
                // Custom properties rather than a background colour: the strip
                // shape stays in the stylesheet and the card only supplies data.
                $attributes['style'] = '--nl-ct-a: ' . $palette['accent'] . '; --nl-ct-b: ' . $palette['primary'] . ';';
            }

            $input = \html_writer::empty_tag('input', [
                'type' => 'radio',
                'class' => 'nl-ct__input',
                'name' => $name,
                'value' => $key,
                'id' => $id . '_' . $key,
            ] + ($checked ? ['checked' => 'checked'] : []));

            $meta = \html_writer::span(s($label), 'nl-ct__name');
            $meta .= \html_writer::span(theme_nadine_icon('check', 'nl-ct__tick-icon', 11), 'nl-ct__tick');

            $face = \html_writer::span('', 'nl-ct__strip');
            $face .= \html_writer::span($meta, 'nl-ct__meta');

            $cards .= \html_writer::tag('label', $input . \html_writer::span($face, 'nl-ct__face'), $attributes);
        }

        $element = \html_writer::tag('div', $cards, [
            'class' => 'nl-ct',
            'role' => 'radiogroup',
            'aria-label' => strip_tags($this->visiblename),
        ]);

        // No "Default: …" line: the checked card already says which palette is
        // in use, and repeating its name underneath adds a second label to the
        // same fact.
        return format_admin_setting(
            $this,
            $this->visiblename,
            $element,
            $this->description,
            false,
            '',
            null,
            $query
        );
    }
}
