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
 * Plugin callbacks for local_recruitment.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Inject "Send reminder notifications" checkbox into the quiz activity settings form.
 *
 * @param moodleform_mod $formwrapper
 * @param MoodleQuickForm $mform
 */
function local_recruitment_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB;

    $current = $formwrapper->get_current();
    if (empty($current->modulename) || $current->modulename !== 'quiz') {
        return;
    }

    // Load existing setting when editing an existing quiz.
    $value = 0;
    $instanceid = !empty($current->instance) ? (int) $current->instance : 0;
    if ($instanceid > 0) {
        $settings = $DB->get_record('local_recruitment_quiz_settings', ['quizid' => $instanceid]);
        if ($settings) {
            $value = (int) $settings->send_notifications;
        }
    }

    $mform->addElement(
        'checkbox',
        'local_recruitment_send_notifications',
        get_string('send_notifications', 'local_recruitment')
    );
    $mform->addHelpButton('local_recruitment_send_notifications', 'send_notifications', 'local_recruitment');
    $mform->setDefault('local_recruitment_send_notifications', $value);
}

/**
 * Save the "Send reminder notifications" setting after the quiz form is submitted.
 *
 * @param stdClass $moduleinfo Saved module data (instance is the quiz ID).
 * @param stdClass $course
 * @return stdClass $moduleinfo (unchanged)
 */
function local_recruitment_coursemodule_edit_post_actions($moduleinfo, $course) {
    global $DB;

    if ($moduleinfo->modulename !== 'quiz') {
        return $moduleinfo;
    }

    $quizid = (int) $moduleinfo->instance;
    $send   = !empty($moduleinfo->local_recruitment_send_notifications) ? 1 : 0;
    $now    = time();

    $existing = $DB->get_record('local_recruitment_quiz_settings', ['quizid' => $quizid]);
    if ($existing) {
        $existing->send_notifications = $send;
        $existing->timemodified       = $now;
        $DB->update_record('local_recruitment_quiz_settings', $existing);
    } else {
        $DB->insert_record('local_recruitment_quiz_settings', (object) [
            'quizid'             => $quizid,
            'send_notifications' => $send,
            'timecreated'        => $now,
            'timemodified'       => $now,
        ]);
    }

    return $moduleinfo;
}
