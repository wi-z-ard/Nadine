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
 * Login page layout for the Nadine theme.
 *
 * @package   theme_nadine
 * @copyright 2026 EduPro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes();

// Core's login form prints its own <h1> ("Welcome back"), but when a signed-in
// user opens /login/index.php core renders the "you are already logged in"
// notice instead, which has no heading. Supply one so every page the theme
// owns has exactly one h1.
$loggedinheading = '';
if (isloggedin() && !isguestuser()) {
    $loggedinheading = s($PAGE->heading);
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'backicon' => theme_nadine_icon('arrow-left', 'nl-directional', 16),
    'loggedinheading' => $loggedinheading,
];

echo $OUTPUT->render_from_template('theme_nadine/login', $templatecontext);
