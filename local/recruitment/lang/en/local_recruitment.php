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
 * English strings for local_recruitment.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Recruitment Management';
$string['recruitments'] = 'Recruitments';
$string['addrecruitment'] = 'Add recruitment';
$string['editrecruitment'] = 'Edit recruitment';
$string['deleterecruitment'] = 'Delete recruitment';
$string['recruitmentname'] = 'Name';
$string['recruitmentdate'] = 'Recruitment end date';
$string['basecategory'] = 'Base category';
$string['archivecourse'] = 'Archive course';
$string['preparationcourse'] = 'Preparation course';
$string['quizescourse'] = 'Internal tests course';
$string['cohort'] = 'Cohort';
$string['choose'] = '-- Choose --';
$string['confirmdelete'] = 'Are you sure you want to delete recruitment "{$a}"? All directions and their cohorts will be deleted.';
$string['recruitmentsaved'] = 'Recruitment saved successfully.';
$string['recruitmentdeleted'] = 'Recruitment deleted successfully.';
$string['norecruitments'] = 'No recruitments found.';
$string['cohortfor'] = 'Cohort for recruitment: {$a}';
$string['cohortfor_direction'] = 'Cohort for: {$a->recruitment} - {$a->direction}';
$string['privacy:metadata'] = 'The recruitment plugin does not store personal data directly. User assignments are managed through cohort memberships.';
$string['eventrecruitmentcreated'] = 'Recruitment created';
$string['eventrecruitmentupdated'] = 'Recruitment updated';
$string['eventrecruitmentdeleted'] = 'Recruitment deleted';
$string['assignedcourses'] = 'Assigned courses';
$string['enter'] = 'Enter';
$string['directions'] = 'Courses (directions)';
$string['directionname'] = 'Course (direction) name';
$string['adddirection'] = 'Add course';
$string['editdirection'] = 'Edit course (direction)';
$string['deletedirection'] = 'Delete course (direction)';
$string['confirmdeletedirection'] = 'Are you sure you want to delete course (direction) "{$a}" and its cohort?';
$string['directionsaved'] = 'Course (direction) saved successfully.';
$string['directiondeleted'] = 'Course (direction) deleted successfully.';
$string['backtorecruitments'] = 'Back to recruitments';
$string['copystatus'] = 'Copy status';
$string['copyinprogress'] = 'Copying in progress...';
$string['copydone'] = 'Done';
$string['users'] = 'Users';
$string['importusers'] = 'Import users';
$string['downloadcsvsample'] = 'Download sample CSV';
$string['declaration'] = 'Declaration';
$string['declarationyes'] = 'Yes';
$string['declarationno'] = 'No';
$string['toggledeclaration'] = 'Toggle declaration';
$string['removeuser'] = 'Remove user from recruitment';
$string['userremoved'] = 'User has been removed from this direction.';
$string['confirmremoveuser'] = 'Are you sure you want to remove user "{$a}" from this direction?';
$string['userimported'] = 'Users imported successfully. Created: {$a->created}, Updated: {$a->updated}, Errors: {$a->errors}, Notified: {$a->notified}.';
$string['importerror'] = 'Error on line {$a->line}: {$a->message}';
$string['backtousers'] = 'Back to users';
$string['csvfilesemicolon'] = 'CSV file (semicolon-separated values)';
$string['csvfileonly'] = 'Only .csv files are allowed';
$string['csvformat'] = 'CSV format: username;firstname;lastname;email;phone;declaration (tak/nie)';
$string['exportusers'] = 'Export users';
$string['setdeclaration'] = 'Mark declaration';
$string['confirmsetdeclaration'] = 'Are you sure you want to mark the declaration for user "{$a}"? This action is irreversible and will send a notification.';
$string['declarationset'] = 'Declaration has been marked and notification sent.';
$string['filter'] = 'Filter';
$string['resetfilters'] = 'Reset';
$string['deleteuser'] = 'Delete user from platform';
$string['userdeleted'] = 'User has been permanently deleted from the platform.';
$string['confirmuserdelete'] = 'Are you sure you want to permanently delete this user from the platform? This action cannot be undone. The user will be removed from all courses and recruitments.';
$string['notificationstatus'] = 'Notification';
$string['notifiedyes'] = 'Sent';
$string['notifiedno'] = 'Not sent';
$string['examregistrationsubject'] = 'Exam registration is now available';
$string['examregistrationbody'] = 'Hello, exam registration for the course {$a->direction} (recruitment: {$a->recruitment}) is now available. Please log in to the platform: {$a->loginurl}';
$string['examregistrationsms'] = 'Exam registration for {$a->direction} ({$a->recruitment}) is now available. Log in to the platform.';
$string['messageprovider:exam_registration'] = 'Exam registration notification';
$string['newaccountsubject'] = 'Your account has been created';
$string['newaccountbody'] = 'Hello {$a->firstname} {$a->lastname},

Your account on the platform has been created.

Login (PESEL): {$a->username}
Password: {$a->password}

Log in to the platform: {$a->loginurl}

We recommend changing your password after first login.';
$string['archives_overview'] = 'Lecture Archives';
$string['preparation_overview'] = 'Exam Preparation';
$string['internaltests_overview'] = 'Internal Tests';
$string['basecourse'] = 'Base course';
$string['basecourse_label'] = 'Base course';
$string['basecourses'] = 'Base courses';
$string['directioncourses'] = 'Direction courses';
$string['progressreport'] = 'Results';
$string['progressreport_btn'] = 'Go';
$string['kategoriezowe_group'] = 'Base categories';
$string['kategorieutwortzone_group'] = 'Created directions';
$string['theme'] = 'Theme';
$string['theme_red'] = 'SPV (czerwony)';
$string['theme_green'] = 'Ogrodnik (green)';
$string['task_resend_account_emails'] = 'Resend account credential emails';
$string['task_send_quiz_notifications'] = 'Send quiz reminder notifications';
$string['send_notifications'] = 'Send reminder notifications';
$string['send_notifications_help'] = 'If checked, users will receive email notifications: 7 days before the quiz opens, when it opens, and 24 hours before it closes.';
$string['notification_subject_7days'] = 'Quiz "{$a->quizname}" opens in 7 days';
$string['notification_subject_open'] = 'Quiz "{$a->quizname}" is now available';
$string['notification_subject_closing'] = 'Quiz "{$a->quizname}" closes in 24 hours';
$string['notification_body_7days'] = 'Hello, the quiz "{$a->quizname}" in the {$a->direction} course (recruitment: {$a->recruitment}) will open on {$a->date}. Log in to the platform: {$a->loginurl}';
$string['notification_body_open'] = 'Hello, the quiz "{$a->quizname}" in the {$a->direction} course (recruitment: {$a->recruitment}) is now available. Log in to the platform: {$a->loginurl}';
$string['notification_body_closing'] = 'Hello, the quiz "{$a->quizname}" in the {$a->direction} course (recruitment: {$a->recruitment}) will close in 24 hours. Log in to the platform: {$a->loginurl}';
$string['notification_sms_7days'] = 'Quiz "{$a->quizname}" opens in 7 days. Log in to the platform.';
$string['notification_sms_open'] = 'Quiz "{$a->quizname}" is now available. Log in to the platform.';
$string['notification_sms_closing'] = 'Quiz "{$a->quizname}" closes in 24 hours. Log in to the platform.';
$string['messageprovider:quiz_notification'] = 'Quiz reminder notification';
