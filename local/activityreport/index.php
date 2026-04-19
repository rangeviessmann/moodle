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
 * Activity Report main page.
 *
 * @package    local_activityreport
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('local_activityreport');

$download          = optional_param('download', '', PARAM_ALPHA);
$filterfirstname   = optional_param('filter_firstname', '', PARAM_TEXT);
$filterlastname    = optional_param('filter_lastname', '', PARAM_TEXT);
$filteremail       = optional_param('filter_email', '', PARAM_TEXT);
$filterphone       = optional_param('filter_phone', '', PARAM_TEXT);
$filtereventname    = optional_param('filter_eventname', '', PARAM_ALPHANUMEXT);
$filteractivityname = optional_param('filter_activityname', '', PARAM_TEXT);
$filterdescription  = optional_param('filter_description', '', PARAM_TEXT);
$filterdatefrom    = optional_param('filter_datefrom', '', PARAM_TEXT);
$filterdateto      = optional_param('filter_dateto', '', PARAM_TEXT);
$resetfilters      = optional_param('resetfilters', 0, PARAM_BOOL);

if ($resetfilters) {
    $filterfirstname   = '';
    $filterlastname    = '';
    $filteremail       = '';
    $filterphone       = '';
    $filtereventname    = '';
    $filteractivityname = '';
    $filterdescription  = '';
    $filterdatefrom     = '';
    $filterdateto       = '';
}

// Convert date strings to timestamps.
$datefromts = 0;
$datetots   = 0;
if (!empty($filterdatefrom)) {
    $ts = strtotime($filterdatefrom);
    $datefromts = ($ts !== false) ? $ts : 0;
}
if (!empty($filterdateto)) {
    $ts = strtotime($filterdateto . ' 23:59:59');
    $datetots = ($ts !== false) ? $ts : 0;
}

$filters = [
    'filter_firstname'    => $filterfirstname,
    'filter_lastname'     => $filterlastname,
    'filter_email'        => $filteremail,
    'filter_phone'        => $filterphone,
    'filter_eventname'    => $filtereventname,
    'filter_activityname' => $filteractivityname,
    'filter_description'  => $filterdescription,
    'filter_datefrom'     => $datefromts,
    'filter_dateto'       => $datetots,
];

$urlparams = [
    'filter_firstname'    => $filterfirstname,
    'filter_lastname'     => $filterlastname,
    'filter_email'        => $filteremail,
    'filter_phone'        => $filterphone,
    'filter_eventname'    => $filtereventname,
    'filter_activityname' => $filteractivityname,
    'filter_description'  => $filterdescription,
    'filter_datefrom'     => $filterdatefrom,
    'filter_dateto'       => $filterdateto,
];

$baseurl = new moodle_url('/local/activityreport/index.php', array_filter($urlparams));

// Build table (filters applied in constructor).
$table = new \local_activityreport\output\activityreport_table('local_activityreport', $filters);
$table->define_baseurl($baseurl);

// CSV download mode — must be set before any output.
if ($download === 'csv') {
    $filename = 'activityreport_' . date('Y-m-d_His');
    $table->is_downloading('csv', $filename);
    $table->out(10000, true); // Fetch up to 10 000 rows for export.
    exit;
}
$PAGE->set_heading(get_string('activityreport', 'local_activityreport'));

// Normal HTML page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('activityreport', 'local_activityreport'));

// Filter form.
$filterdata = [
    'actionurl' => (new moodle_url('/local/activityreport/index.php'))->out(false),
    'reseturl'  => (new moodle_url('/local/activityreport/index.php'))->out(false),
    'fields'    => [
        ['id' => 'filter_firstname',   'name' => 'filter_firstname',   'label' => get_string('filter_firstname', 'local_activityreport'),   'type' => 'text', 'value' => s($filterfirstname)],
        ['id' => 'filter_lastname',    'name' => 'filter_lastname',    'label' => get_string('filter_lastname', 'local_activityreport'),    'type' => 'text', 'value' => s($filterlastname)],
        ['id' => 'filter_email',       'name' => 'filter_email',       'label' => get_string('filter_email', 'local_activityreport'),       'type' => 'text', 'value' => s($filteremail)],
        ['id' => 'filter_phone',       'name' => 'filter_phone',       'label' => get_string('filter_phone', 'local_activityreport'),       'type' => 'text', 'value' => s($filterphone)],
        ['id' => 'filter_eventname', 'name' => 'filter_eventname', 'label' => get_string('filter_eventname', 'local_activityreport'), 'type' => 'select', 'is_select' => true, 'value' => s($filtereventname), 'options' => [
            ['value' => '', 'label' => get_string('filter_eventname_all', 'local_activityreport'), 'selected' => ($filtereventname === '')],
            ['value' => 'user_loggedin',                    'label' => get_string('eventname_user_loggedin', 'local_activityreport'),                    'selected' => ($filtereventname === 'user_loggedin')],
            ['value' => 'course_module_viewed',             'label' => get_string('eventname_course_module_viewed', 'local_activityreport'),             'selected' => ($filtereventname === 'course_module_viewed')],
            ['value' => 'course_module_completion_updated', 'label' => get_string('eventname_course_module_completion_updated', 'local_activityreport'), 'selected' => ($filtereventname === 'course_module_completion_updated')],
            ['value' => 'course_completed',                 'label' => get_string('eventname_course_completed', 'local_activityreport'),                 'selected' => ($filtereventname === 'course_completed')],
            ['value' => 'attempt_started',                  'label' => get_string('eventname_attempt_started', 'local_activityreport'),                  'selected' => ($filtereventname === 'attempt_started')],
            ['value' => 'attempt_submitted',                'label' => get_string('eventname_attempt_submitted', 'local_activityreport'),                'selected' => ($filtereventname === 'attempt_submitted')],
            ['value' => 'attempt_reviewed',                 'label' => get_string('eventname_attempt_reviewed', 'local_activityreport'),                 'selected' => ($filtereventname === 'attempt_reviewed')],
            ['value' => 'attempt_viewed',                   'label' => get_string('eventname_attempt_viewed', 'local_activityreport'),                   'selected' => ($filtereventname === 'attempt_viewed')],
            ['value' => 'youtube_video_played',             'label' => get_string('eventname_youtube_played', 'local_activityreport'),                   'selected' => ($filtereventname === 'youtube_video_played')],
        ]],
        ['id' => 'filter_activityname', 'name' => 'filter_activityname', 'label' => get_string('filter_activityname', 'local_activityreport'), 'type' => 'text', 'value' => s($filteractivityname)],
        ['id' => 'filter_description',  'name' => 'filter_description',  'label' => get_string('filter_description', 'local_activityreport'),  'type' => 'text', 'value' => s($filterdescription)],
        ['id' => 'filter_datefrom',    'name' => 'filter_datefrom',    'label' => get_string('filter_datefrom', 'local_activityreport'),    'type' => 'date', 'value' => s($filterdatefrom)],
        ['id' => 'filter_dateto',      'name' => 'filter_dateto',      'label' => get_string('filter_dateto', 'local_activityreport'),      'type' => 'date', 'value' => s($filterdateto)],
    ],
    'str_filter'    => get_string('filter', 'local_activityreport'),
    'str_reset'     => get_string('resetfilters', 'local_activityreport'),
    'str_exportcsv' => get_string('exportcsv', 'local_activityreport'),
    'exporturl'     => (new moodle_url('/local/activityreport/index.php', array_merge(array_filter($urlparams), ['download' => 'csv'])))->out(false),
];
echo $OUTPUT->render_from_template('local_activityreport/filter_form', $filterdata);

// Render the table.
$table->out(50, true);

echo $OUTPUT->footer();
