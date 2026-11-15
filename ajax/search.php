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
 * Live course search endpoint for the Nadine homepage search field.
 *
 * Accepts a POST with a sesskey and returns JSON. Every course is passed
 * through Moodle's own visibility rules, so a visitor only ever sees courses
 * they are allowed to see.
 *
 * @package   theme_nadine
 * @copyright 2026 EduPro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

/** @var int Maximum number of results returned to the panel. */
const THEME_NADINE_SEARCH_LIMIT = 8;

/** @var int Minimum number of characters before the query is run. */
const THEME_NADINE_SEARCH_MINCHARS = 2;

/** @var int Longest query string accepted, in characters. */
const THEME_NADINE_SEARCH_MAXCHARS = 64;

/** @var int Requests allowed per session per minute. */
const THEME_NADINE_SEARCH_RATE = 40;

/**
 * Send a JSON body and stop.
 *
 * @param array $payload
 * @param int $status HTTP status code.
 * @return void
 */
function theme_nadine_search_reply(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store, max-age=0');
    header('Referrer-Policy: same-origin');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    theme_nadine_search_reply(['error' => 'methodnotallowed'], 405);
}

// CSRF: the field ships a sesskey and the browser sends it back in the body.
require_sesskey();

// A site that forces login should not answer guests, even on a public page.
if (!empty($CFG->forcelogin) && !isloggedin()) {
    theme_nadine_search_reply(['error' => 'requirelogin'], 403);
}

// Cheap per-session throttle so a held key cannot turn into a query flood.
$now = time();
$hits = isset($SESSION->nl_search_hits) ? (array) $SESSION->nl_search_hits : [];
$hits = array_values(array_filter($hits, static fn($stamp) => $stamp > $now - 60));
if (count($hits) >= THEME_NADINE_SEARCH_RATE) {
    theme_nadine_search_reply(['error' => 'ratelimited'], 429);
}
$hits[] = $now;
$SESSION->nl_search_hits = $hits;

// PARAM_RAW keeps "R&D" searchable; the value never leaves this script as
// markup, it is only bound as a SQL parameter and JSON-encoded.
$raw = optional_param('q', '', PARAM_RAW);
$raw = strip_tags((string) $raw);
$query = trim(preg_replace('/\s+/u', ' ', core_text::substr($raw, 0, THEME_NADINE_SEARCH_MAXCHARS)));
$query = trim($query, " \t\n\r\0\x0B-+");

$moreurl = (new moodle_url('/course/search.php', ['search' => $query]))->out(false);

if (core_text::strlen($query) < THEME_NADINE_SEARCH_MINCHARS) {
    theme_nadine_search_reply([
        'query' => $query,
        'total' => 0,
        'results' => [],
        'moreurl' => $moreurl,
    ]);
}

$terms = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
$total = 0;
// The core helper applies the same visibility rules as /course/search.php,
// including hidden courses, hidden categories and enrolment-only courses.
$courses = get_courses_search($terms, 'c.sortorder ASC', 0, THEME_NADINE_SEARCH_LIMIT, $total);

$categorynames = [];
if (!empty($courses)) {
    [$insql, $params] = $DB->get_in_or_equal(array_unique(array_map(
        static fn($course) => (int) $course->category,
        $courses
    )), SQL_PARAMS_NAMED);
    $categorynames = $DB->get_records_select_menu(
        'course_categories',
        "id $insql",
        $params,
        '',
        'id, name'
    );
}

$results = [];
foreach ($courses as $course) {
    $context = context_course::instance($course->id);

    // The string is escaped here and decoded after, so the JSON carries plain
    // text and the client can write it with textContent alone.
    $name = html_to_text(format_string($course->fullname, true, ['context' => $context]), 0, false);

    $summary = '';
    if (!empty($course->summary)) {
        $summary = html_to_text(
            format_text($course->summary, $course->summaryformat, ['context' => $context, 'filter' => false]),
            0,
            false
        );
        $summary = trim(preg_replace('/\s+/u', ' ', $summary));
        if (core_text::strlen($summary) > 120) {
            $cut = core_text::substr($summary, 0, 120);
            $space = core_text::strrpos($cut, ' ');
            if ($space !== false && $space > 80) {
                $cut = core_text::substr($cut, 0, $space);
            }
            $summary = rtrim($cut, " ,.;:") . '...';
        }
    }

    $categoryname = isset($categorynames[$course->category]) ? $categorynames[$course->category] : '';
    $category = '';
    if ($categoryname !== '') {
        $categorycontext = context_coursecat::instance($course->category);
        $category = html_to_text(format_string($categoryname, true, ['context' => $categorycontext]), 0, false);
    }

    $results[] = [
        'name' => $name,
        'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
        'category' => $category,
        'summary' => $summary,
    ];
}

theme_nadine_search_reply([
    'query' => $query,
    'total' => (int) $total,
    'results' => $results,
    'moreurl' => $moreurl,
]);
