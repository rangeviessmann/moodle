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
 * AJAX endpoint to log a YouTube video play event.
 *
 * @package    local_activityreport
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

header('Content-Type: application/json; charset=utf-8');

// Must be logged in and have a valid sesskey.
require_login(null, false);

if (!confirm_sesskey()) {
    echo json_encode(['error' => 'Invalid sesskey']);
    die();
}

$courseid  = required_param('courseid', PARAM_INT);
$videourl  = optional_param('videourl', '', PARAM_TEXT);
$pagetitle = optional_param('pagetitle', '', PARAM_TEXT);
$cmid      = optional_param('cmid', 0, PARAM_INT);

// Validate course access.
$course = $DB->get_record('course', ['id' => $courseid]);
if (!$course) {
    echo json_encode(['error' => 'Invalid course']);
    die();
}

$context = context_course::instance($courseid);

// Only log if user has access to the course.
if (!is_enrolled($context, $USER->id) && !has_capability('moodle/course:view', $context)) {
    echo json_encode(['error' => 'Not enrolled']);
    die();
}

// Sanitize videourl — keep only youtube domain URLs.
if (!empty($videourl)) {
    $parsed = parse_url($videourl);
    $host = $parsed['host'] ?? '';
    if (strpos($host, 'youtube.com') === false && strpos($host, 'youtu.be') === false && strpos($host, 'youtube-nocookie.com') === false) {
        $videourl = ''; // Not a YouTube URL, discard.
    }
    // Strip tracking parameters.
    $videourl = strtok($videourl, '?');
}

// Resolve activity name from cmid if provided.
$activityname = '';
if ($cmid > 0) {
    $cm = $DB->get_record('course_modules', ['id' => $cmid, 'course' => $courseid], 'id, instance, module');
    if ($cm) {
        $moduletype = $DB->get_field('modules', 'name', ['id' => $cm->module]);
        if ($moduletype) {
            $activityname = $DB->get_field($moduletype, 'name', ['id' => $cm->instance]) ?: '';
        }
    }
}

// Trigger the event (it will be written to logstore_standard_log automatically).
$event = \local_activityreport\event\youtube_video_played::create([
    'context'  => $context,
    'courseid' => $courseid,
    'userid'   => $USER->id,
    'other'    => [
        'videourl'     => $videourl,
        'pagetitle'    => $pagetitle,
        'cmid'         => $cmid,
        'activityname' => $activityname,
    ],
]);
$event->trigger();

echo json_encode(['success' => true]);
