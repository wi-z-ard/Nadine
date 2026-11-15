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

namespace theme_nadine;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/theme/nadine/lib.php');

/**
 * Tests for the theme's pure helpers.
 *
 * The colour maths and the icon loader are the two places where a silent
 * mistake reaches every page, so they are covered here. Anything that needs a
 * rendered page belongs in the Behat suite instead.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::theme_nadine_mix_color
 * @covers     ::theme_nadine_get_color_themes
 * @covers     ::theme_nadine_icon
 */
final class lib_test extends advanced_testcase {
    /**
     * Relative luminance of a hex colour, per WCAG 2.2.
     *
     * @param string $hex Colour in `#rrggbb` form.
     * @return float
     */
    protected function luminance(string $hex): float {
        $hex = ltrim($hex, '#');
        $channels = [];
        foreach ([0, 2, 4] as $offset) {
            $value = hexdec(substr($hex, $offset, 2)) / 255;
            $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    /**
     * WCAG contrast ratio between two hex colours.
     *
     * @param string $one First colour.
     * @param string $two Second colour.
     * @return float A ratio between 1 and 21.
     */
    protected function contrast(string $one, string $two): float {
        $a = $this->luminance($one);
        $b = $this->luminance($two);
        $light = max($a, $b);
        $dark = min($a, $b);

        return ($light + 0.05) / ($dark + 0.05);
    }

    /**
     * Mixing at the extremes returns the inputs unchanged.
     *
     * @covers ::theme_nadine_mix_color
     */
    public function test_mix_color_endpoints(): void {
        $this->assertSame('#000000', theme_nadine_mix_color('#000000', '#ffffff', 0));
        $this->assertSame('#ffffff', theme_nadine_mix_color('#000000', '#ffffff', 100));
    }

    /**
     * A half mix lands in the middle of the channel range.
     *
     * @covers ::theme_nadine_mix_color
     */
    public function test_mix_color_midpoint(): void {
        // 0 and 255 average to 127.5, which rounds to 128.
        $this->assertSame('#808080', theme_nadine_mix_color('#000000', '#ffffff', 50));
        $this->assertSame('#804020', theme_nadine_mix_color('#000000', '#ff8040', 50));
    }

    /**
     * Weights outside 0-100 are clamped rather than extrapolated.
     *
     * @covers ::theme_nadine_mix_color
     */
    public function test_mix_color_clamps_weight(): void {
        $this->assertSame('#123456', theme_nadine_mix_color('#123456', '#ffffff', -40));
        $this->assertSame('#ffffff', theme_nadine_mix_color('#123456', '#ffffff', 400));
    }

    /**
     * A leading hash is optional on either input.
     *
     * @covers ::theme_nadine_mix_color
     */
    public function test_mix_color_accepts_bare_hex(): void {
        $this->assertSame(
            theme_nadine_mix_color('#112233', '#445566', 30),
            theme_nadine_mix_color('112233', '445566', 30)
        );
    }

    /**
     * The mix result is always a lower-case six-digit hex string.
     *
     * @covers ::theme_nadine_mix_color
     */
    public function test_mix_color_output_format(): void {
        $result = theme_nadine_mix_color('#EF5E00', '#01275A', 37);
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $result);
    }

    /**
     * Every shipped palette is a complete, well-formed pair.
     *
     * @covers ::theme_nadine_get_color_themes
     */
    public function test_color_themes_are_well_formed(): void {
        $themes = theme_nadine_get_color_themes();

        $this->assertArrayHasKey('default', $themes);
        $this->assertCount(8, $themes);

        foreach ($themes as $name => $pair) {
            $this->assertArrayHasKey('accent', $pair, "Palette {$name} has no accent.");
            $this->assertArrayHasKey('primary', $pair, "Palette {$name} has no primary.");
            $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $pair['accent'], "Accent of {$name}.");
            $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $pair['primary'], "Primary of {$name}.");
        }
    }

    /**
     * The primary colour is used for links and must clear AAA on white.
     *
     * @covers ::theme_nadine_get_color_themes
     */
    public function test_primary_contrast_on_white(): void {
        foreach (theme_nadine_get_color_themes() as $name => $pair) {
            $ratio = $this->contrast($pair['primary'], '#ffffff');
            $this->assertGreaterThanOrEqual(
                7.0,
                $ratio,
                "Primary of {$name} only reaches " . round($ratio, 2) . ':1 on white.'
            );
        }
    }

    /**
     * The accent is used for large text, icons and UI boundaries, where WCAG
     * 2.2 asks for 3:1 rather than 4.5:1.
     *
     * @covers ::theme_nadine_get_color_themes
     */
    public function test_accent_contrast_on_white(): void {
        foreach (theme_nadine_get_color_themes() as $name => $pair) {
            $ratio = $this->contrast($pair['accent'], '#ffffff');
            $this->assertGreaterThanOrEqual(
                3.0,
                $ratio,
                "Accent of {$name} only reaches " . round($ratio, 2) . ':1 on white.'
            );
        }
    }

    /**
     * The accent as a button surface clears AA for every shipped palette.
     *
     * @covers ::theme_nadine_action_color
     */
    public function test_action_color_meets_aa_for_every_palette(): void {
        foreach (theme_nadine_get_color_themes() as $name => $pair) {
            $action = theme_nadine_action_color($pair['accent']);

            $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $action, "Action of {$name}.");

            $onwhite = $this->contrast($action, '#ffffff');
            $this->assertGreaterThanOrEqual(
                4.5,
                $onwhite,
                "White text on the {$name} button only reaches " . round($onwhite, 2) . ':1.'
            );

            $oncanvas = $this->contrast($action, '#f6f4ef');
            $this->assertGreaterThanOrEqual(
                4.5,
                $oncanvas,
                "The {$name} outline label only reaches " . round($oncanvas, 2) . ':1 on the canvas.'
            );
        }
    }

    /**
     * A palette that already clears AA is returned unchanged, so the fix only
     * touches the accents that need it.
     *
     * @covers ::theme_nadine_action_color
     */
    public function test_action_color_leaves_passing_accent_alone(): void {
        foreach (['#2f7d4f', '#0e7490', '#8e3b6b', '#b04a2a', '#3f5ea8', '#b23a5b'] as $accent) {
            $this->assertSame($accent, theme_nadine_action_color($accent));
        }
    }

    /**
     * The default orange is too bright for white text, so it is stepped down.
     *
     * @covers ::theme_nadine_action_color
     */
    public function test_action_color_darkens_bright_accent(): void {
        $action = theme_nadine_action_color('#ef5e00');

        $this->assertNotSame('#ef5e00', $action);
        $this->assertLessThan(
            $this->luminance('#ef5e00'),
            $this->luminance($action),
            'The action colour should be darker than the brand accent.'
        );
    }

    /**
     * Contrast maths matches the WCAG reference values.
     *
     * @covers ::theme_nadine_contrast_ratio
     * @covers ::theme_nadine_relative_luminance
     */
    public function test_contrast_ratio_reference_values(): void {
        $this->assertEqualsWithDelta(21.0, theme_nadine_contrast_ratio('#000000', '#ffffff'), 0.01);
        $this->assertEqualsWithDelta(1.0, theme_nadine_contrast_ratio('#ef5e00', '#ef5e00'), 0.001);
        $this->assertEqualsWithDelta(3.35, theme_nadine_contrast_ratio('#ef5e00', '#ffffff'), 0.01);
    }

    /**
     * A known glyph is returned as inline SVG carrying the theme's classes,
     * the requested size and the decorative ARIA attributes.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_returns_svg_markup(): void {
        $markup = theme_nadine_icon('bell');

        $this->assertStringStartsWith('<svg ', $markup);
        $this->assertStringContainsString('class="nl-icon nl-icon--lucide"', $markup);
        $this->assertStringContainsString('width="20"', $markup);
        $this->assertStringContainsString('height="20"', $markup);
        $this->assertStringContainsString('aria-hidden="true"', $markup);
        $this->assertStringContainsString('focusable="false"', $markup);
    }

    /**
     * Extra classes are appended, not replaced.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_appends_classes(): void {
        $markup = theme_nadine_icon('bell', 'nl-icon--lg');

        $this->assertStringContainsString('class="nl-icon nl-icon--lucide nl-icon--lg"', $markup);
    }

    /**
     * The size argument is clamped to the range the set is drawn for.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_clamps_size(): void {
        $this->assertStringContainsString('width="8"', theme_nadine_icon('bell', '', 1));
        $this->assertStringContainsString('width="96"', theme_nadine_icon('bell', '', 500));
    }

    /**
     * An unknown glyph returns an empty string so callers can fall back.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_unknown_name_is_empty(): void {
        $this->assertSame('', theme_nadine_icon('definitely-not-an-icon'));
    }

    /**
     * A path in the name cannot escape the icon directory.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_strips_path_traversal(): void {
        $this->assertSame('', theme_nadine_icon('../../config'));
        $this->assertStringStartsWith('<svg ', theme_nadine_icon('../bell'));
    }

    /**
     * The social set resolves and is labelled as such.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_social_set(): void {
        $markup = theme_nadine_icon('github', '', 18, 'social');

        $this->assertStringContainsString('nl-icon--social', $markup);
        $this->assertStringContainsString('width="18"', $markup);
    }

    /**
     * An unknown set falls back to the interface glyphs.
     *
     * @covers ::theme_nadine_icon
     */
    public function test_icon_unknown_set_falls_back(): void {
        $markup = theme_nadine_icon('bell', '', 20, 'nonsense');

        $this->assertStringContainsString('nl-icon--lucide', $markup);
    }

    /**
     * The SCSS prelude carries the accent, the primary and the type stack, and
     * the values are the ones the palette defines.
     *
     * @covers ::theme_nadine_get_pre_scss
     */
    public function test_pre_scss_emits_tokens(): void {
        $this->resetAfterTest();

        $theme = \theme_config::load('nadine');
        $scss = theme_nadine_get_pre_scss($theme);

        $this->assertStringContainsString('$nl-accent: ', $scss);
        $this->assertStringContainsString('$nl-primary: ', $scss);
        $this->assertStringContainsString('$font-family-base: $font-family-sans-serif;', $scss);
        $this->assertStringContainsString('$headings-font-weight: 600;', $scss);
    }

    /**
     * A custom accent set in the settings reaches the compiled SCSS, and an
     * invalid one falls back to the shipped default rather than emitting junk.
     *
     * @covers ::theme_nadine_get_accent_color
     */
    public function test_custom_accent_is_used_and_validated(): void {
        $theme = \theme_config::load('nadine');
        $theme->settings->colortheme = 'custom';

        $theme->settings->accentcolor = '#123456';
        $this->assertSame('#123456', theme_nadine_get_accent_color($theme));

        $theme->settings->accentcolor = 'not-a-colour';
        $this->assertSame('#ef5e00', theme_nadine_get_accent_color($theme));
    }
}
