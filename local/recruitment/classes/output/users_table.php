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
 * Users table for a direction.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_recruitment\output;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/tablelib.php');

/**
 * Table class for displaying users within a direction.
 */
class users_table extends \table_sql {

    /** @var int Direction ID. */
    protected int $directionid;

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param \moodle_url $url
     * @param int $directionid
     * @param array $filters
     */
    public function __construct(string $uniqueid, \moodle_url $url, int $directionid, array $filters = []) {
        global $DB;

        parent::__construct($uniqueid);
        $this->directionid = $directionid;
        $this->baseurl = $url;

        $columns = ['username', 'firstname', 'lastname', 'email', 'phone1', 'declaration', 'notified', 'actions'];
        $headers = [
            get_string('username'),
            get_string('firstname'),
            get_string('lastname'),
            get_string('email'),
            get_string('phone'),
            get_string('declaration', 'local_recruitment'),
            get_string('notificationstatus', 'local_recruitment'),
            get_string('actions'),
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->define_baseurl($url);
        $this->column_style('username', 'width', '12%');
        $this->column_style('firstname', 'width', '12%');
        $this->column_style('lastname', 'width', '14%');
        $this->column_style('email', 'width', '18%');
        $this->column_style('phone1', 'width', '10%');
        $this->column_style('declaration', 'width', '10%');
        $this->column_style('notified', 'width', '12%');
        $this->column_style('actions', 'width', '12%');
        $this->collapsible(false);
        $this->sortable(true, 'lastname', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('actions');

        $where  = 'ru.directionid = :directionid AND u.deleted = 0 AND u.suspended = 0';
        $params = ['directionid' => $directionid];

        if (!empty($filters['username'])) {
            $where .= ' AND ' . $DB->sql_like('u.username', ':fusername', false);
            $params['fusername'] = '%' . $DB->sql_like_escape($filters['username']) . '%';
        }
        if (!empty($filters['firstname'])) {
            $where .= ' AND ' . $DB->sql_like('u.firstname', ':ffirstname', false);
            $params['ffirstname'] = '%' . $DB->sql_like_escape($filters['firstname']) . '%';
        }
        if (!empty($filters['lastname'])) {
            $where .= ' AND ' . $DB->sql_like('u.lastname', ':flastname', false);
            $params['flastname'] = '%' . $DB->sql_like_escape($filters['lastname']) . '%';
        }
        if (!empty($filters['email'])) {
            $where .= ' AND ' . $DB->sql_like('u.email', ':femail', false);
            $params['femail'] = '%' . $DB->sql_like_escape($filters['email']) . '%';
        }
        if (!empty($filters['phone'])) {
            $where .= ' AND ' . $DB->sql_like('u.phone1', ':fphone', false);
            $params['fphone'] = '%' . $DB->sql_like_escape($filters['phone']) . '%';
        }
        if ($filters['declaration'] !== '' && $filters['declaration'] !== null) {
            $where .= ' AND ru.declaration = :fdeclaration';
            $params['fdeclaration'] = (int)$filters['declaration'];
        }
        if ($filters['notified'] !== '' && $filters['notified'] !== null) {
            $where .= ' AND ru.notified = :fnotified';
            $params['fnotified'] = (int)$filters['notified'];
        }

        $this->set_sql(
            'ru.id, ru.declaration, ru.notified, ru.timenotified, ru.userid, ru.directionid,
             u.username, u.firstname, u.lastname, u.email, u.phone1',
            '{local_recruitment_user} ru
             JOIN {user} u ON u.id = ru.userid',
            $where,
            $params
        );
    }

    /**
     * Format the declaration column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_declaration(\stdClass $row): string {
        if (!empty($row->declaration)) {
            return '<span class="badge badge-success bg-success">' .
                get_string('declarationyes', 'local_recruitment') . '</span>';
        }
        return '<span class="badge badge-secondary bg-secondary">' .
            get_string('declarationno', 'local_recruitment') . '</span>';
    }

    /**
     * Format the notified column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_notified(\stdClass $row): string {
        if (!empty($row->notified)) {
            $date = userdate($row->timenotified, get_string('strftimedatetimeshort', 'langconfig'));
            return '<span class="badge badge-success bg-success">' .
                get_string('notifiedyes', 'local_recruitment') . '</span>' .
                '<br><small class="text-muted">' . $date . '</small>';
        }
        return '<span class="badge badge-secondary bg-secondary">' .
            get_string('notifiedno', 'local_recruitment') . '</span>';
    }

    /**
     * Format the actions column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions(\stdClass $row): string {
        global $OUTPUT;

        $actions = '';

        // "Ustaw deklarację" — only when declaration is NOT set (irreversible once set).
        if (empty($row->declaration)) {
            $seturl = new \moodle_url('/local/recruitment/users.php', [
                'did' => $row->directionid,
                'setdeclaration' => $row->id,
                'sesskey' => sesskey(),
            ]);
            $icon = $OUTPUT->pix_icon('t/check', get_string('setdeclaration', 'local_recruitment'));
            $actions .= \html_writer::link($seturl, $icon . ' ' . get_string('setdeclaration', 'local_recruitment'), [
                'class' => 'btn btn-sm btn-outline-success',
            ]);
            $actions .= ' ';
        }

        // "Usuń z rekrutacji" — removes the user from this direction only.
        $removeurl = new \moodle_url('/local/recruitment/users.php', [
            'did' => $row->directionid,
            'removeuser' => $row->userid,
            'sesskey' => sesskey(),
        ]);
        $confirmmsg = get_string('confirmremoveuser', 'local_recruitment',
            $row->firstname . ' ' . $row->lastname);
        $icon = $OUTPUT->pix_icon('t/delete', get_string('removeuser', 'local_recruitment'));
        $actions .= \html_writer::link($removeurl, $icon, [
            'class' => 'btn btn-sm btn-outline-danger',
            'title' => get_string('removeuser', 'local_recruitment'),
            'onclick' => 'return confirm(' . json_encode($confirmmsg) . ');',
        ]);

        return $actions;
    }
}
