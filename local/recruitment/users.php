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
 * Users list for a direction.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/cohort/lib.php');

$did            = required_param('did', PARAM_INT);
$setdeclaration = optional_param('setdeclaration', 0, PARAM_INT);
$removeuser     = optional_param('removeuser', 0, PARAM_INT);

// Filters.
$resetfilters     = optional_param('resetfilters', 0, PARAM_BOOL);
$filterusername   = optional_param('filter_username',    '', PARAM_TEXT);
$filterfirstname  = optional_param('filter_firstname',   '', PARAM_TEXT);
$filterlastname   = optional_param('filter_lastname',    '', PARAM_TEXT);
$filteremail      = optional_param('filter_email',       '', PARAM_TEXT);
$filterphone      = optional_param('filter_phone',       '', PARAM_TEXT);
$filterdeclaration = optional_param('filter_declaration', '', PARAM_TEXT);
$filternotified   = optional_param('filter_notified',    '', PARAM_TEXT);

if ($resetfilters) {
    $filterusername = $filterfirstname = $filterlastname = '';
    $filteremail = $filterphone = $filterdeclaration = $filternotified = '';
}

$filters = [
    'username'    => $filterusername,
    'firstname'   => $filterfirstname,
    'lastname'    => $filterlastname,
    'email'       => $filteremail,
    'phone'       => $filterphone,
    'declaration' => $filterdeclaration,
    'notified'    => $filternotified,
];

admin_externalpage_setup('local_recruitment');

$direction = $DB->get_record('local_recruitment_course', ['id' => $did], '*', MUST_EXIST);
$recruitment = $DB->get_record('local_recruitment', ['id' => $direction->recruitmentid], '*', MUST_EXIST);

$urlparams = array_filter([
    'filter_username'    => $filterusername,
    'filter_firstname'   => $filterfirstname,
    'filter_lastname'    => $filterlastname,
    'filter_email'       => $filteremail,
    'filter_phone'       => $filterphone,
    'filter_declaration' => $filterdeclaration,
    'filter_notified'    => $filternotified,
]);
$pageurl = new moodle_url('/local/recruitment/users.php', array_merge(['did' => $did], $urlparams));

// Handle set declaration (irreversible: set to 1 + send notifications).
if ($setdeclaration && confirm_sesskey()) {
    $record = $DB->get_record('local_recruitment_user', ['id' => $setdeclaration, 'directionid' => $did], '*', MUST_EXIST);

    // Only process if declaration is currently 0.
    if (empty($record->declaration)) {
        $now = time();
        $record->declaration = 1;
        $record->timemodified = $now;
        $DB->update_record('local_recruitment_user', $record);

        // Send to WordPress immediately.
        if (class_exists('\local_support\wp_sync_service')) {
            $user = $DB->get_record('user', ['id' => $record->userid], '*', MUST_EXIST);
            \local_support\wp_sync_service::send($user, 'declaration_set');
        }

        // Queue async notification (email + SMS) via cron to avoid stack overflow.
        if (empty($record->notified)) {
            $task = new \local_recruitment\task\send_declaration_notification();
            $task->set_custom_data((object)[
                'recordid' => $record->id,
                'wp_sync' => false,
            ]);
            $task->set_userid($USER->id);
            \core\task\manager::queue_adhoc_task($task);
        }
    }

    redirect($pageurl, get_string('declarationset', 'local_recruitment'), null,
        \core\output\notification::NOTIFY_SUCCESS);
}

// Handle user removal from this direction.
if ($removeuser && confirm_sesskey()) {
    $record = $DB->get_record('local_recruitment_user',
        ['userid' => $removeuser, 'directionid' => $did], '*', MUST_EXIST);
    $DB->delete_records('local_recruitment_user', ['id' => $record->id]);

    // Remove from direction cohort if one exists.
    $directionrec = $DB->get_record('local_recruitment_course', ['id' => $did]);
    if ($directionrec && !empty($directionrec->cohortid)) {
        cohort_remove_member($directionrec->cohortid, $removeuser);
    }

    redirect($pageurl, get_string('userremoved', 'local_recruitment'), null,
        \core\output\notification::NOTIFY_SUCCESS);
}

$PAGE->set_url($pageurl);
$pagetitle = get_string('users', 'local_recruitment') . ': ' . format_string($direction->name);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

// Header buttons.
$importurl = new moodle_url('/local/recruitment/users_import.php', ['did' => $did]);
$exporturl = new moodle_url('/local/recruitment/users_export.php', ['did' => $did]);
$backurl = new moodle_url('/local/recruitment/courses.php', ['rid' => $direction->recruitmentid]);

echo html_writer::start_div('mb-3');
echo html_writer::link($importurl, get_string('importusers', 'local_recruitment'), [
    'class' => 'btn btn-primary mr-2',
]);
echo html_writer::link($exporturl, get_string('exportusers', 'local_recruitment'), [
    'class' => 'btn btn-primary mr-2',
]);
echo html_writer::link($backurl, get_string('backtousers', 'local_recruitment'), [
    'class' => 'btn btn-secondary',
]);
echo html_writer::end_div();

// Filter form.
$baseurl    = new moodle_url('/local/recruitment/users.php', ['did' => $did]);
$reseturl   = new moodle_url('/local/recruitment/users.php', ['did' => $did, 'resetfilters' => 1]);
$filterdata = [
    'actionurl'     => $baseurl->out(false),
    'reseturl'      => $reseturl->out(false),
    'exporturl'     => '',
    'hidden_fields' => [
        ['name' => 'did', 'value' => $did],
    ],
    'fields'    => [
        ['id' => 'filter_username',  'name' => 'filter_username',  'label' => get_string('username'),    'type' => 'text', 'value' => s($filterusername)],
        ['id' => 'filter_firstname', 'name' => 'filter_firstname', 'label' => get_string('firstname'),   'type' => 'text', 'value' => s($filterfirstname)],
        ['id' => 'filter_lastname',  'name' => 'filter_lastname',  'label' => get_string('lastname'),    'type' => 'text', 'value' => s($filterlastname)],
        ['id' => 'filter_email',     'name' => 'filter_email',     'label' => get_string('email'),       'type' => 'text', 'value' => s($filteremail)],
        ['id' => 'filter_phone',     'name' => 'filter_phone',     'label' => get_string('phone'),       'type' => 'text', 'value' => s($filterphone)],
        ['id' => 'filter_declaration', 'name' => 'filter_declaration', 'label' => get_string('declaration', 'local_recruitment'),
            'type' => 'select', 'is_select' => true, 'value' => s($filterdeclaration),
            'options' => [
                ['value' => '',  'label' => get_string('all'),                                     'selected' => ($filterdeclaration === '')],
                ['value' => '1', 'label' => get_string('declarationyes', 'local_recruitment'),     'selected' => ($filterdeclaration === '1')],
                ['value' => '0', 'label' => get_string('declarationno', 'local_recruitment'),      'selected' => ($filterdeclaration === '0')],
            ],
        ],
        ['id' => 'filter_notified', 'name' => 'filter_notified', 'label' => get_string('notificationstatus', 'local_recruitment'),
            'type' => 'select', 'is_select' => true, 'value' => s($filternotified),
            'options' => [
                ['value' => '',  'label' => get_string('all'),                               'selected' => ($filternotified === '')],
                ['value' => '1', 'label' => get_string('notifiedyes', 'local_recruitment'),  'selected' => ($filternotified === '1')],
                ['value' => '0', 'label' => get_string('notifiedno', 'local_recruitment'),   'selected' => ($filternotified === '0')],
            ],
        ],
    ],
    'str_filter'    => get_string('filter', 'local_recruitment'),
    'str_reset'     => get_string('resetfilters', 'local_recruitment'),
    'str_exportcsv' => '',
];
echo $OUTPUT->render_from_template('local_activityreport/filter_form', $filterdata);

// Display users table.
$table = new \local_recruitment\output\users_table('local-recruitment-users', $pageurl, $did, $filters);
$table->out(50, true);

echo $OUTPUT->footer();
