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
 * English strings for quizaccess_internaltest.
 *
 * @package    quizaccess_internaltest
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Credits';
$string['internaltest'] = 'Internal credit';
$string['internaltest_help'] = 'When enabled, quiz responses are extracted as JSON after submission. Email notifications are sent to enrolled users before the credit opens and closes.';
$string['privacy:metadata:quizaccess_inttest_results'] = 'Stores JSON data with user quiz responses for internal credits.';
$string['privacy:metadata:quizaccess_inttest_results:userid'] = 'The ID of the user who submitted the attempt.';
$string['privacy:metadata:quizaccess_inttest_results:jsondata'] = 'JSON data containing questions and user answers.';
$string['privacy:metadata:quizaccess_inttest_results:timecreated'] = 'The time the result was recorded.';
$string['messageprovider:internaltest_reminder'] = 'Credit reminders';
$string['notification_subject_7days'] = 'Credit "{$a->quizname}" opens in 7 days';
$string['notification_subject_open'] = 'Credit "{$a->quizname}" is now available';
$string['notification_subject_closing'] = 'Credit "{$a->quizname}" closes in 24 hours';
$string['notification_body_7days'] = 'The credit "{$a->quizname}" in course "{$a->coursename}" will open in 7 days ({$a->opendate}). Please prepare accordingly.';
$string['notification_body_open'] = 'The credit "{$a->quizname}" in course "{$a->coursename}" is now available. You can start it at: {$a->url}';
$string['notification_body_closing'] = 'The credit "{$a->quizname}" in course "{$a->coursename}" closes in 24 hours ({$a->closedate}). Please complete it before the deadline.';
$string['taskname'] = 'Send credit notifications';
$string['sms_7days'] = 'Credit "{$a->quizname}" ({$a->coursename}) opens on {$a->opendate}. Please prepare.';
$string['sms_open'] = 'Credit "{$a->quizname}" ({$a->coursename}) is now available. Log in to start.';
$string['sms_closing'] = 'Credit "{$a->quizname}" ({$a->coursename}) closes on {$a->closedate}. Complete it before the deadline.';
