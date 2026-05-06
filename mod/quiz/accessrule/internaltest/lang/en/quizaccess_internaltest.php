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
$string['notification_subject_7days'] = 'Internal tests will be available in 7 days';
$string['notification_subject_open'] = 'Internal tests are now available';
$string['notification_subject_closing'] = 'Internal tests will be available for 24 more hours';
$string['notification_body_7days'] = 'Please be informed that the internal tests for the Vocational Qualification Course will be available in 7 days in your student panel. Log in: {$a->loginurl}
The tests are mandatory for all students. You must complete them before {$a->closedate} and achieve a minimum of {$a->gradepass} to pass the course.';
$string['notification_body_open'] = 'Please be informed that the internal tests for the Vocational Qualification Course are now available in your student panel. Log in: {$a->loginurl}
The tests are mandatory for all students. You must complete them before {$a->closedate} and achieve a minimum of {$a->gradepass} to pass the course.';
$string['notification_body_closing'] = 'Please be informed that the internal tests for the Vocational Qualification Course will be available in your student panel for 24 more hours. Log in: {$a->loginurl}
The tests are mandatory for all students. You must complete them before {$a->closedate} and achieve a minimum of {$a->gradepass} to pass the course.';
$string['taskname'] = 'Send credit notifications';
$string['sms_7days'] = 'Internal tests for the Vocational Qualification Course will be available in 7 days. Log in: {$a->loginurl}';
$string['sms_open'] = 'Internal tests for the Vocational Qualification Course are now available in your student panel. Log in: {$a->loginurl} Complete tests before {$a->closedate}';
$string['sms_open_noclose'] = 'Internal tests for the Vocational Qualification Course are now available in your student panel. Log in: {$a->loginurl}';
$string['sms_closing'] = 'Internal tests for the Vocational Qualification Course will be available for 24 more hours. Log in: {$a->loginurl}';
