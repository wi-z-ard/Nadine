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
 * Stores whether the reader collapsed the navigation rail.
 *
 * The rail collapses to an icon strip through a checkbox, so the change is
 * instant in the browser; this endpoint only makes the choice survive the next
 * page load.
 *
 * @package   theme_nadine
 * @copyright 2026 EduPro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

require_sesskey();

if (!isloggedin() || isguestuser()) {
    http_response_code(403);
    exit;
}

$collapsed = optional_param('collapsed', 0, PARAM_BOOL);

set_user_preference('navrail-collapsed', $collapsed ? 1 : 0);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');

echo json_encode(['collapsed' => (bool) $collapsed]);
