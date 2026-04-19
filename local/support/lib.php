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
 * Library functions for local_support.
 *
 * @package    local_support
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Send email using the SMTP server appropriate for the given direction theme.
 *
 * Temporarily overrides $CFG smtp settings when the theme is 'green' and the
 * Gardener SMTP is configured, then restores original values.
 *
 * @param \stdClass|string $userto   Recipient user object (or email string).
 * @param \stdClass        $userfrom Sender user object.
 * @param string           $subject  Email subject.
 * @param string           $messagetext  Plain-text body.
 * @param string           $messagehtml  HTML body (optional).
 * @param string           $theme    Direction theme: 'red' or 'green'.
 * @return bool True on success.
 */
function local_support_email_to_user(
    $userto,
    $userfrom,
    string $subject,
    string $messagetext,
    string $messagehtml = '',
    string $theme = 'red'
): bool {
    global $CFG;

    // For red theme or unconfigured green, use the platform default SMTP.
    if ($theme !== 'green') {
        return email_to_user($userto, $userfrom, $subject, $messagetext, $messagehtml);
    }

    $smtphosts = get_config('local_support', 'gardener_smtphosts');
    if (empty(trim($smtphosts))) {
        // Gardener SMTP not configured — fall back to default.
        return email_to_user($userto, $userfrom, $subject, $messagetext, $messagehtml);
    }

    // Save original CFG values (some may not exist — use null-coalesce).
    $orig = [
        'smtphosts'      => $CFG->smtphosts      ?? '',
        'smtpuser'       => $CFG->smtpuser        ?? '',
        'smtppass'       => $CFG->smtppass        ?? '',
        'smtpport'       => $CFG->smtpport        ?? '',
        'smtpsecure'     => $CFG->smtpsecure      ?? '',
        'noreplyaddress' => $CFG->noreplyaddress  ?? '',
    ];

    // Apply Gardener SMTP settings.
    $CFG->smtphosts  = $smtphosts;
    $CFG->smtpuser   = (string) (get_config('local_support', 'gardener_smtpuser')   ?: '');
    $CFG->smtppass   = (string) (get_config('local_support', 'gardener_smtppass')   ?: '');
    $CFG->smtpport   = (int)   (get_config('local_support', 'gardener_smtpport')   ?: 25);
    $CFG->smtpsecure = (string) (get_config('local_support', 'gardener_smtpsecure') ?: '');

    $fromemail = (string) (get_config('local_support', 'gardener_smtp_fromemail') ?: '');
    $fromname  = (string) (get_config('local_support', 'gardener_smtp_fromname')  ?: '');

    if ($fromemail) {
        $CFG->noreplyaddress = $fromemail;
        // Clone sender so we don't mutate the original object.
        $userfrom = clone $userfrom;
        $userfrom->email = $fromemail;
        if ($fromname) {
            $userfrom->firstname = $fromname;
            $userfrom->lastname  = '';
        }
    }

    try {
        $result = email_to_user($userto, $userfrom, $subject, $messagetext, $messagehtml);
    } finally {
        // Always restore original settings, even if email_to_user throws.
        foreach ($orig as $key => $value) {
            $CFG->$key = $value;
        }
    }

    return $result;
}

/**
 * Inject internal test status badges on course view pages.
 *
 * For each quiz in the current course that is marked as internal test,
 * determines the user's status (not attempted / passed / failed) and
 * passes that data to a JS module that renders badges next to activity names.
 */
function local_support_inject_internaltest_badges() {
    global $DB, $USER, $PAGE, $COURSE;

    if (empty($COURSE->id) || $COURSE->id <= 1) {
        return;
    }

    // Get all quiz course modules in this course that are internal tests.
    $sql = "SELECT cm.id AS cmid, q.id AS quizid, q.name, q.grade AS maxgrade, q.sumgrades,
                   q.timeopen, q.timeclose
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz'
              JOIN {quiz} q ON q.id = cm.instance
              JOIN {quizaccess_internaltest} qi ON qi.quizid = q.id AND qi.internaltest = 1
             WHERE cm.course = :courseid AND cm.deletioninprogress = 0";
    $quizzes = $DB->get_records_sql($sql, ['courseid' => $COURSE->id]);

    if (empty($quizzes)) {
        return;
    }

    // Get grade pass for each quiz from grade_items.
    $gradepassmap = [];
    $quizids = array_column(array_values($quizzes), 'quizid');
    if (!empty($quizids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($quizids, SQL_PARAMS_NAMED);
        $inparams['courseid'] = $COURSE->id;
        $gradeitems = $DB->get_records_sql(
            "SELECT iteminstance, gradepass FROM {grade_items}
              WHERE itemmodule = 'quiz' AND courseid = :courseid AND iteminstance $insql",
            $inparams
        );
        foreach ($gradeitems as $gi) {
            $gradepassmap[$gi->iteminstance] = (float)$gi->gradepass;
        }
    }

    // For each internal test quiz, determine the user's status.
    $statuses = [];
    foreach ($quizzes as $quiz) {
        // Get best finished attempt for this user.
        $bestattempt = $DB->get_record_sql(
            "SELECT id, sumgrades
               FROM {quiz_attempts}
              WHERE quiz = :quizid AND userid = :userid AND state = 'finished'
              ORDER BY sumgrades DESC
              LIMIT 1",
            ['quizid' => $quiz->quizid, 'userid' => $USER->id]
        );

        if (!$bestattempt) {
            $status = 'notdone';
        } else {
            // Rescale attempt grade to quiz grade scale.
            $attemptgrade = 0;
            if ($quiz->sumgrades > 0) {
                $attemptgrade = ($bestattempt->sumgrades / $quiz->sumgrades) * $quiz->maxgrade;
            }
            $gradepass = $gradepassmap[$quiz->quizid] ?? 0;
            if ($gradepass > 0) {
                $status = ($attemptgrade >= $gradepass) ? 'passed' : 'failed';
            } else {
                // No passing grade set — any finished attempt is "passed".
                $status = 'passed';
            }
        }

        // Determine active/inactive based on timeopen/timeclose.
        $now = time();
        $isactive = true;
        if (!empty($quiz->timeopen) && $now < $quiz->timeopen) {
            $isactive = false;
        }
        if (!empty($quiz->timeclose) && $now > $quiz->timeclose) {
            $isactive = false;
        }

        $statuses[] = [
            'cmid' => (int)$quiz->cmid,
            'status' => $status,
            'availability' => $isactive ? 'active' : 'inactive',
        ];
    }

    if (!empty($statuses)) {
        $PAGE->requires->js_call_amd('local_support/internaltest_badges', 'init', [$statuses]);
    }
}

/**
 * Serve files uploaded via local_support admin settings (logo, favicon, etc.).
 *
 * @param stdClass $course   Not used.
 * @param stdClass $cm       Not used.
 * @param context  $context  Must be system context.
 * @param string   $filearea File area name (e.g. 'green_logo', 'green_favicon').
 * @param array    $args     Remaining URL path segments.
 * @param bool     $forcedownload
 * @param array    $options
 * @return bool|void
 */
function local_support_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_SYSTEM) {
        return false;
    }

    $allowed = ['green_logo', 'green_favicon', 'red_logo', 'red_favicon'];
    if (!in_array($filearea, $allowed)) {
        return false;
    }

    $itemid   = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? ('/' . implode('/', $args) . '/') : '/';

    $fs   = get_file_storage();
    $file = $fs->get_file($context->id, 'local_support', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, false, $options);
}

/**
 * Return the URL of a stored file in a local_support file area, or null if none uploaded.
 *
 * @param string $filearea  'green_logo' or 'green_favicon'
 * @return moodle_url|null
 */
function local_support_get_filearea_url(string $filearea): ?\moodle_url {
    $fs      = get_file_storage();
    $context = \context_system::instance();
    $files   = $fs->get_area_files($context->id, 'local_support', $filearea, 0, 'itemid', false);
    if (empty($files)) {
        return null;
    }
    $file = reset($files);
    return \moodle_url::make_pluginfile_url(
        $context->id,
        'local_support',
        $filearea,
        0,
        $file->get_filepath(),
        $file->get_filename()
    );
}
