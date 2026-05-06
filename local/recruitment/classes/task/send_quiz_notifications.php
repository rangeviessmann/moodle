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
 * Scheduled task: send quiz reminder notifications.
 *
 * Sends 3 types of notifications for each quiz that has send_notifications=1:
 *  - '7days'   : quiz opens in ≤ 7 days (sent once, as soon as the window is reached)
 *  - 'open'    : quiz has just opened
 *  - 'closing' : quiz closes in ≤ 24 hours
 *
 * Each type is sent at most once per (quiz, user) pair, tracked in
 * local_recruitment_quiz_notified.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_recruitment\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Send quiz reminder notifications to enrolled users.
 */
class send_quiz_notifications extends \core\task\scheduled_task {

    public function get_name(): string {
        return get_string('task_send_quiz_notifications', 'local_recruitment');
    }

    public function execute(): void {
        global $DB;

        $now = time();

        // All quizzes with notifications enabled and at least one time restriction.
        $sql = "SELECT q.id, q.name, q.timeopen, q.timeclose, q.course
                  FROM {quiz} q
                  JOIN {local_recruitment_quiz_settings} rqs ON rqs.quizid = q.id
                 WHERE rqs.send_notifications = 1
                   AND (q.timeopen > 0 OR q.timeclose > 0)";

        $quizzes = $DB->get_records_sql($sql);

        if (empty($quizzes)) {
            mtrace('send_quiz_notifications: no quizzes with notifications enabled.');
            return;
        }

        foreach ($quizzes as $quiz) {
            $types = $this->get_pending_types($quiz, $now);
            if (empty($types)) {
                continue;
            }

            // Find the recruitment direction that owns this quiz course.
            $direction = $DB->get_record('local_recruitment_course', ['quizes_course' => $quiz->course]);
            if (!$direction || empty($direction->cohortid)) {
                continue;
            }

            $recruitment = $DB->get_record('local_recruitment', ['id' => $direction->recruitmentid]);
            if (!$recruitment) {
                continue;
            }

            // Users enrolled via the direction cohort.
            $users = $DB->get_records_sql(
                "SELECT u.*
                   FROM {user} u
                   JOIN {cohort_members} cm ON cm.userid = u.id
                  WHERE cm.cohortid = :cohortid
                    AND u.deleted   = 0
                    AND u.suspended = 0",
                ['cohortid' => $direction->cohortid]
            );

            if (empty($users)) {
                continue;
            }

            foreach ($types as $type) {
                $sent = 0;
                foreach ($users as $user) {
                    if ($DB->record_exists('local_recruitment_quiz_notified', [
                        'quizid' => $quiz->id,
                        'userid' => $user->id,
                        'type'   => $type,
                    ])) {
                        continue;
                    }

                    // Record FIRST to prevent duplicates on task retry.
                    $DB->insert_record('local_recruitment_quiz_notified', (object) [
                        'quizid'   => $quiz->id,
                        'userid'   => $user->id,
                        'type'     => $type,
                        'timesent' => $now,
                    ]);

                    $this->notify_user($user, $quiz, $direction, $recruitment, $type);
                    $sent++;
                }
                if ($sent > 0) {
                    mtrace("  quiz {$quiz->id} ({$quiz->name}): sent '{$type}' to {$sent} user(s).");
                }
            }
        }
    }

    /**
     * Determine which notification types are due for this quiz right now.
     *
     * @param \stdClass $quiz
     * @param int $now
     * @return string[]
     */
    private function get_pending_types(\stdClass $quiz, int $now): array {
        $types = [];

        if ($quiz->timeopen > 0) {
            // 7-day notice: quiz opens in ≤ 7 days but has not opened yet.
            if ($quiz->timeopen > $now && ($quiz->timeopen - $now) <= 7 * DAYSECS) {
                $types[] = '7days';
            }
            // Open notice: quiz is now open.
            if ($quiz->timeopen <= $now) {
                $types[] = 'open';
            }
        }

        if ($quiz->timeclose > 0) {
            // Closing notice: quiz closes in ≤ 24 hours but has not closed yet.
            if ($quiz->timeclose > $now && ($quiz->timeclose - $now) <= DAYSECS) {
                $types[] = 'closing';
            }
        }

        return $types;
    }

    /**
     * Send email (and optional SMS) notification to a single user.
     *
     * @param \stdClass $user
     * @param \stdClass $quiz
     * @param \stdClass $direction
     * @param \stdClass $recruitment
     * @param string    $type  '7days' | 'open' | 'closing'
     */
    private function notify_user(
        \stdClass $user,
        \stdClass $quiz,
        \stdClass $direction,
        \stdClass $recruitment,
        string $type
    ): void {
        global $DB;

        $loginurl = (new \moodle_url('/login/index.php'))->out(false);

        // Passing grade from gradebook.
        $gradeitem = $DB->get_record('grade_items', [
            'itemtype'     => 'mod',
            'itemmodule'   => 'quiz',
            'iteminstance' => $quiz->id,
        ], 'gradepass');
        $gradepass = '';
        if ($gradeitem && $gradeitem->gradepass > 0 && $quiz->grade > 0) {
            $gradepass = round(($gradeitem->gradepass / $quiz->grade) * 100) . '%';
        }

        $dateformat = get_string('strftimerecentfull', 'langconfig');
        $closedate  = $quiz->timeclose > 0 ? userdate($quiz->timeclose, $dateformat) : '';

        $a = (object) [
            'loginurl'  => $loginurl,
            'closedate' => $closedate,
            'gradepass' => $gradepass,
        ];

        $subject  = get_string('notification_subject_' . $type, 'local_recruitment');
        $bodytext = get_string('notification_body_' . $type, 'local_recruitment', $a);

        $ahtml = clone $a;
        $ahtml->loginurl = '<a href="' . s($loginurl) . '">' . s($loginurl) . '</a>';
        $bodyhtml = nl2br(get_string('notification_body_' . $type, 'local_recruitment', $ahtml));

        $noreply = \core_user::get_noreply_user();

        $message                    = new \core\message\message();
        $message->component         = 'local_recruitment';
        $message->name              = 'quiz_notification';
        $message->userfrom          = $noreply;
        $message->userto            = $user;
        $message->subject           = $subject;
        $message->fullmessage       = $bodytext;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml   = $bodyhtml;
        $message->smallmessage      = $subject;
        $message->notification      = 1;

        try {
            message_send($message);
        } catch (\Exception $e) {
            mtrace('  FAILED email user ' . $user->id . ': ' . $e->getMessage());
        }

        // SMS (optional — only if local_support is available).
        try {
            if (class_exists('\local_support\sms_service') && !empty($user->phone1)) {
                // For 'open' without a close date, use the variant without the deadline line.
                $smskey = 'sms_' . $type;
                if ($type === 'open' && $quiz->timeclose <= 0) {
                    $smskey = 'sms_open_noclose';
                }
                $smstext = get_string($smskey, 'local_recruitment', $a);
                \local_support\sms_service::send(
                    $user, $smstext, 'local_recruitment', 'quiz_notification_sms', (int) $quiz->id
                );
            }
        } catch (\Exception $e) {
            mtrace('  FAILED SMS user ' . $user->id . ': ' . $e->getMessage());
        }
    }
}
