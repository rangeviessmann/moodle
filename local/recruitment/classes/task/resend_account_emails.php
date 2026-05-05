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
 * Scheduled task: resend new-account credential emails to users who never received them.
 *
 * Targets users who:
 *  - are in local_recruitment_user (imported via CSV)
 *  - have never logged in (lastlogin = 0)
 *  - were created within the last 30 days
 *  - have no entry in local_mail_history with the new-account subject
 *
 * Resets the password and sends the email again.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_recruitment\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Resend credential emails to users who never received them.
 */
class resend_account_emails extends \core\task\scheduled_task {

    public function get_name(): string {
        return get_string('task_resend_account_emails', 'local_recruitment');
    }

    public function execute(): void {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/user/lib.php');

        $subject   = get_string('newaccountsubject', 'local_recruitment');
        $cutoff    = (int) get_config('local_recruitment', 'account_email_since');

        // Users imported via CSV, never logged in, created on or after the
        // fixed cutoff date, with no new-account email in local_mail_history.
        $sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email
                  FROM {user} u
                  JOIN {local_recruitment_user} lru ON lru.userid = u.id
                 WHERE u.lastlogin = 0
                   AND u.deleted   = 0
                   AND u.suspended = 0
                   AND u.timecreated >= :cutoff
                   AND NOT EXISTS (
                       SELECT 1
                         FROM {local_mail_history} mh
                        WHERE mh.userid  = u.id
                          AND mh.subject = :subject
                   )";

        $users = $DB->get_records_sql($sql, ['cutoff' => $cutoff, 'subject' => $subject]);

        if (empty($users)) {
            mtrace('resend_account_emails: no users pending, done.');
            return;
        }

        mtrace('resend_account_emails: ' . count($users) . ' user(s) to process.');

        foreach ($users as $userrow) {
            $user = $DB->get_record('user', ['id' => $userrow->id]);
            if (!$user || empty($user->email)) {
                mtrace('  skip user ' . $userrow->id . ': no email address.');
                continue;
            }

            // Generate a new password and update the user record.
            $newpassword = generate_password(12);
            update_internal_user_password($user, $newpassword);

            // Ensure they must change password on first login.
            set_user_preference('auth_forcepasswordchange', 1, $user->id);

            // Send the credentials email (also writes to local_mail_history on success).
            $sent = \local_recruitment\recruitment::send_new_account_email($user, $newpassword);

            if ($sent) {
                mtrace('  sent to user ' . $user->id . ' (' . $user->email . ')');
            } else {
                mtrace('  FAILED for user ' . $user->id . ' (' . $user->email . ')');
            }
        }
    }
}
