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
 * Activity report table definition.
 *
 * @package    local_activityreport
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_activityreport\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Table class for the activity report, extending table_sql.
 */
class activityreport_table extends \table_sql {

    /** @var array Event class names to include in the report. */
    private const EVENT_WHITELIST = [
        '\\core\\event\\user_loggedin',
        '\\mod_resource\\event\\course_module_viewed',
        '\\mod_page\\event\\course_module_viewed',
        '\\mod_url\\event\\course_module_viewed',
        '\\mod_folder\\event\\course_module_viewed',
        '\\mod_book\\event\\course_module_viewed',
        '\\mod_lesson\\event\\course_module_viewed',
        '\\core\\event\\course_module_completion_updated',
        '\\core\\event\\course_completed',
        '\\mod_quiz\\event\\attempt_started',
        '\\mod_quiz\\event\\attempt_submitted',
        '\\mod_quiz\\event\\attempt_reviewed',
        '\\mod_quiz\\event\\attempt_viewed',
        '\\local_activityreport\\event\\youtube_video_played',
    ];

    /**
     * Mapping from event action key to lang string identifier.
     */
    private const EVENT_DESC_MAP = [
        'user_loggedin'                    => 'eventdesc_user_loggedin',
        'course_module_viewed'             => 'eventdesc_course_module_viewed',
        'course_module_completion_updated' => 'eventdesc_course_module_completion_updated',
        'course_completed'                 => 'eventdesc_course_completed',
        'attempt_started'                  => 'eventdesc_attempt_started',
        'attempt_submitted'                => 'eventdesc_attempt_submitted',
        'attempt_reviewed'                 => 'eventdesc_attempt_reviewed',
        'attempt_viewed'                   => 'eventdesc_attempt_viewed',
        'youtube_video_played'             => 'eventdesc_youtube_played',
    ];

    /** @var array Mapping from event action key to lang string for event name. */
    private const EVENT_NAME_MAP = [
        'user_loggedin'                    => 'eventname_user_loggedin',
        'course_module_viewed'             => 'eventname_course_module_viewed',
        'course_module_completion_updated' => 'eventname_course_module_completion_updated',
        'course_completed'                 => 'eventname_course_completed',
        'attempt_started'                  => 'eventname_attempt_started',
        'attempt_submitted'                => 'eventname_attempt_submitted',
        'attempt_reviewed'                 => 'eventname_attempt_reviewed',
        'attempt_viewed'                   => 'eventname_attempt_viewed',
        'youtube_video_played'             => 'eventname_youtube_played',
    ];

    /** @var array Filter values. */
    private array $filters;

    /** @var array Per-row cache: contextinstanceid => activityname. */
    private array $activitynamecache = [];

    /**
     * Constructor.
     *
     * @param string $uniqueid Unique ID for the table.
     * @param array $filters Associative array of filter values.
     */
    public function __construct(string $uniqueid, array $filters = []) {
        parent::__construct($uniqueid);
        $this->filters = $filters;

        $columns = ['firstname', 'lastname', 'email', 'phone', 'eventname', 'activityname', 'description', 'timecreated'];
        $headers = [
            get_string('firstname', 'local_activityreport'),
            get_string('lastname', 'local_activityreport'),
            get_string('email', 'local_activityreport'),
            get_string('phone', 'local_activityreport'),
            get_string('eventname', 'local_activityreport'),
            get_string('activityname', 'local_activityreport'),
            get_string('description', 'local_activityreport'),
            get_string('timecreated', 'local_activityreport'),
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);

        // activityname is computed in PHP — disable SQL sorting for it.
        $this->no_sorting('activityname');

        $this->sortable(true, 'timecreated', SORT_DESC);
        $this->collapsible(false);
        $this->pageable(true);
        $this->set_attribute('class', 'generaltable generalbox');

        $this->setup_sql();
    }

    /**
     * Build and set the SQL query with filters.
     */
    private function setup_sql(): void {
        global $DB;

        // Build event whitelist params.
        $eventparams = [];
        $eventplaceholders = [];
        foreach (self::EVENT_WHITELIST as $i => $eventclass) {
            $paramname = 'evt' . $i;
            $eventplaceholders[] = ':' . $paramname;
            $eventparams[$paramname] = $eventclass;
        }
        $eventin = implode(', ', $eventplaceholders);

        $descriptionexpr = $DB->sql_concat('l.component', "' '", 'l.action', "' '", 'l.target');

        $fields = "l.id, l.eventname, l.component, l.action, l.target, l.objecttable, l.objectid,
                   l.crud, l.edulevel, l.anonymous,
                   l.contextid, l.contextlevel, l.contextinstanceid, l.userid, l.courseid,
                   l.relateduserid, l.other, l.timecreated, l.origin, l.ip,
                   u.firstname, u.lastname, u.email,
                   COALESCE(u.phone1, '') AS phone,
                   {$descriptionexpr} AS description";

        $from = "{logstore_standard_log} l JOIN {user} u ON u.id = l.userid";

        $where = "l.eventname IN ($eventin)";
        $params = $eventparams;

        // Apply text filters.
        $filtermap = [
            'filter_firstname' => 'u.firstname',
            'filter_lastname'  => 'u.lastname',
            'filter_email'     => 'u.email',
            'filter_phone'     => 'u.phone1',
        ];

        foreach ($filtermap as $filterkey => $dbfield) {
            if (!empty($this->filters[$filterkey])) {
                $paramname = $filterkey;
                $where .= " AND " . $DB->sql_like($dbfield, ':' . $paramname, false);
                $params[$paramname] = '%' . $DB->sql_like_escape($this->filters[$filterkey]) . '%';
            }
        }

        // Event name filter — supports direct action key (from dropdown) or translated name substring.
        if (!empty($this->filters['filter_eventname'])) {
            $filterval = $this->filters['filter_eventname'];
            $matchedclasses = [];

            if (array_key_exists($filterval, self::EVENT_NAME_MAP)) {
                // Exact action key match (dropdown selection).
                foreach (self::EVENT_WHITELIST as $eventclass) {
                    $parts = explode('\\', trim($eventclass, '\\'));
                    if (end($parts) === $filterval) {
                        $matchedclasses[] = $eventclass;
                    }
                }
            } else {
                // Fallback: match against translated event names (text input).
                $search = mb_strtolower($filterval);
                foreach (self::EVENT_NAME_MAP as $actionkey => $stringid) {
                    $translated = mb_strtolower(get_string($stringid, 'local_activityreport'));
                    if (strpos($translated, $search) !== false) {
                        foreach (self::EVENT_WHITELIST as $eventclass) {
                            $parts = explode('\\', trim($eventclass, '\\'));
                            if (end($parts) === $actionkey) {
                                $matchedclasses[] = $eventclass;
                            }
                        }
                    }
                }
            }

            if (!empty($matchedclasses)) {
                $inclause = [];
                foreach ($matchedclasses as $i => $cls) {
                    $pname = 'fev' . $i;
                    $inclause[] = ':' . $pname;
                    $params[$pname] = $cls;
                }
                $where .= ' AND l.eventname IN (' . implode(', ', $inclause) . ')';
            } else {
                // No match found — force empty result set.
                $where .= ' AND 1=0';
            }
        }

        // Activity name filter — find cmids matching the name, then filter log rows by contextinstanceid.
        if (!empty($this->filters['filter_activityname'])) {
            $cmids = $this->get_cmids_by_activityname($this->filters['filter_activityname']);
            $youtubeclass = '\\local_activityreport\\event\\youtube_video_played';
            $anlike = '%' . $DB->sql_like_escape($this->filters['filter_activityname']) . '%';
            if (!empty($cmids)) {
                [$insql, $inparams] = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED, 'acm');
                $params = array_merge($params, $inparams);
                $params['filter_an_yt'] = $youtubeclass;
                $params['filter_an_other'] = $anlike;
                $where .= " AND ((l.contextinstanceid $insql AND l.contextlevel = " . CONTEXT_MODULE
                    . ") OR (l.eventname = :filter_an_yt AND " . $DB->sql_like('l.other', ':filter_an_other', false) . "))";
            } else {
                // No module matches — only search YouTube other field.
                $params['filter_an_yt'] = $youtubeclass;
                $params['filter_an_other'] = $anlike;
                $where .= " AND (l.eventname = :filter_an_yt AND " . $DB->sql_like('l.other', ':filter_an_other', false) . ")";
            }
        }

        // Description filter — search across component, action, target.
        if (!empty($this->filters['filter_description'])) {
            $desclike = $DB->sql_like($descriptionexpr, ':filter_description', false);
            $where .= " AND " . $desclike;
            $params['filter_description'] = '%' . $DB->sql_like_escape($this->filters['filter_description']) . '%';
        }

        // Date filters.
        if (!empty($this->filters['filter_datefrom'])) {
            $where .= " AND l.timecreated >= :datefrom";
            $params['datefrom'] = (int)$this->filters['filter_datefrom'];
        }
        if (!empty($this->filters['filter_dateto'])) {
            $where .= " AND l.timecreated <= :dateto";
            $params['dateto'] = (int)$this->filters['filter_dateto'];
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql(
            "SELECT COUNT(1) FROM {logstore_standard_log} l JOIN {user} u ON u.id = l.userid WHERE $where",
            $params
        );
    }

    /**
     * Resolve the activity/module name for a log row.
     * Returns the module name (from quiz, resource, etc.) or the YouTube video URL
     * for youtube_video_played events.
     *
     * @param \stdClass $row
     * @return string
     */
    private function resolve_activity_name(\stdClass $row): string {
        global $DB;

        $key = $this->get_event_key($row->eventname);

        // YouTube events: return stored activity name if available, else video URL.
        if ($key === 'youtube_video_played') {
            $other = $this->decode_other($row->other);
            if (!empty($other['activityname'])) {
                return $other['activityname'];
            }
            return $other['videourl'] ?? '';
        }

        // Login events have no module context.
        if ($key === 'user_loggedin') {
            return '';
        }

        // Module-context events: look up by contextinstanceid.
        if (!empty($row->contextinstanceid) && (int)$row->contextlevel === CONTEXT_MODULE) {
            $cachekey = (int)$row->contextinstanceid;
            if (!isset($this->activitynamecache[$cachekey])) {
                $this->activitynamecache[$cachekey] = '';
                $cm = $DB->get_record('course_modules', ['id' => $cachekey], 'id, instance, module');
                if ($cm) {
                    $moduletype = $DB->get_field('modules', 'name', ['id' => $cm->module]);
                    if ($moduletype) {
                        $name = $DB->get_field($moduletype, 'name', ['id' => $cm->instance]);
                        if ($name) {
                            $this->activitynamecache[$cachekey] = $name;
                        }
                    }
                }
            }
            return $this->activitynamecache[$cachekey];
        }

        return '';
    }

    /**
     * Decode the 'other' field (serialized or JSON).
     *
     * @param mixed $other
     * @return array
     */
    private function decode_other($other): array {
        if (empty($other) || $other === 'N;') {
            return [];
        }
        if (preg_match('~^[aOibs][:;]~', $other)) {
            $decoded = @unserialize($other, ['allowed_classes' => [\stdClass::class]]);
            return is_array($decoded) ? $decoded : (array)$decoded;
        }
        $decoded = json_decode($other, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Restore an event object from a log row.
     *
     * @param \stdClass $row The log row.
     * @return \core\event\base|null
     */
    private function restore_event(\stdClass $row): ?\core\event\base {
        try {
            $extra = [
                'origin'     => $row->origin ?? '',
                'ip'         => $row->ip ?? '',
                'realuserid' => 0,
            ];

            $otherdata = $this->decode_other($row->other ?? null);

            $data = [
                'eventname'         => $row->eventname,
                'component'         => $row->component ?? '',
                'action'            => $row->action ?? '',
                'target'            => $row->target ?? '',
                'objecttable'       => $row->objecttable ?? '',
                'objectid'          => $row->objectid ?? null,
                'crud'              => $row->crud ?? 'r',
                'edulevel'          => $row->edulevel ?? 0,
                'contextid'         => $row->contextid ?? 0,
                'contextlevel'      => $row->contextlevel ?? 0,
                'contextinstanceid' => $row->contextinstanceid ?? 0,
                'userid'            => $row->userid ?? 0,
                'courseid'          => $row->courseid ?? 0,
                'relateduserid'     => $row->relateduserid ?? null,
                'anonymous'         => $row->anonymous ?? 0,
                'other'             => $otherdata ?: null,
                'timecreated'       => $row->timecreated ?? 0,
            ];

            return \core\event\base::restore($data, $extra);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Extract the event action key from the full eventname class.
     *
     * @param string $eventname
     * @return string
     */
    private function get_event_key(string $eventname): string {
        $parts = explode('\\', trim($eventname, '\\'));
        return end($parts);
    }

    /**
     * Get user fullname, course name, and module name for description placeholders.
     *
     * @param \stdClass $row
     * @return object {user, course, module}
     */
    private function get_description_params(\stdClass $row): object {
        global $DB;

        $a = new \stdClass();
        $a->user   = trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? ''));
        $a->course = '';
        $a->module = '';

        if (!empty($row->courseid)) {
            $course = $DB->get_field('course', 'fullname', ['id' => $row->courseid]);
            if ($course) {
                $a->course = $course;
            }
        }

        if (!empty($row->contextinstanceid) && !empty($row->contextlevel) && $row->contextlevel == CONTEXT_MODULE) {
            $cm = $DB->get_record('course_modules', ['id' => $row->contextinstanceid], 'id, instance, module');
            if ($cm) {
                $moduletype = $DB->get_field('modules', 'name', ['id' => $cm->module]);
                if ($moduletype) {
                    $name = $DB->get_field($moduletype, 'name', ['id' => $cm->instance]);
                    if ($name) {
                        $a->module = $name;
                    }
                }
            }
        }

        return $a;
    }

    /**
     * Return all course_module IDs where the module name matches the given search string.
     * Iterates all installed module types dynamically; skips tables without a 'name' column.
     *
     * @param string $search Partial name to search (case-insensitive LIKE).
     * @return int[]
     */
    private function get_cmids_by_activityname(string $search): array {
        global $DB;
        $likearg = '%' . $DB->sql_like_escape($search) . '%';
        $modules = $DB->get_records('modules', null, '', 'id, name');
        $cmids = [];
        foreach ($modules as $mod) {
            try {
                $sql = "SELECT cm.id FROM {course_modules} cm"
                    . " JOIN {" . $mod->name . "} t ON t.id = cm.instance"
                    . " WHERE cm.module = :modid AND " . $DB->sql_like('t.name', ':search', false);
                $records = $DB->get_records_sql($sql, ['modid' => $mod->id, 'search' => $likearg]);
                foreach ($records as $rec) {
                    $cmids[] = (int)$rec->id;
                }
            } catch (\Throwable $e) {
                // Module table has no 'name' column or doesn't exist — skip.
            }
        }
        return array_unique($cmids);
    }

    // ── Column renderers ────────────────────────────────────────────────────

    /**
     * Render the phone column.
     */
    public function col_phone(\stdClass $row): string {
        return s($row->phone ?? '');
    }

    /**
     * Render the activity name column.
     */
    public function col_activityname(\stdClass $row): string {
        $name = $this->resolve_activity_name($row);
        return s($name);
    }

    /**
     * Render the event name column.
     */
    public function col_eventname(\stdClass $row): string {
        $key = $this->get_event_key($row->eventname);
        if (isset(self::EVENT_NAME_MAP[$key])) {
            return get_string(self::EVENT_NAME_MAP[$key], 'local_activityreport');
        }
        $event = $this->restore_event($row);
        if ($event) {
            try {
                return $event->get_name();
            } catch (\Throwable $e) {
                // Fall through.
            }
        }
        return str_replace('\\', ' \\ ', ltrim($row->eventname, '\\'));
    }

    /**
     * Render the description column.
     */
    public function col_description(\stdClass $row): string {
        $key = $this->get_event_key($row->eventname);

        // YouTube: build description using decoded video URL.
        if ($key === 'youtube_video_played') {
            $other = $this->decode_other($row->other);
            $a = new \stdClass();
            $a->user      = trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? ''));
            $a->videourl  = $other['videourl'] ?? '';
            $a->pagetitle = $other['pagetitle'] ?? '';
            return get_string('eventdesc_youtube_played', 'local_activityreport', $a);
        }

        if (isset(self::EVENT_DESC_MAP[$key])) {
            $a = $this->get_description_params($row);
            $desc = get_string(self::EVENT_DESC_MAP[$key], 'local_activityreport', $a);
            if (\core_text::strlen($desc) > 200) {
                $desc = \core_text::substr($desc, 0, 200) . '...';
            }
            return $desc;
        }

        $event = $this->restore_event($row);
        if ($event) {
            try {
                $desc = strip_tags($event->get_description());
                if (\core_text::strlen($desc) > 200) {
                    $desc = \core_text::substr($desc, 0, 200) . '...';
                }
                return $desc;
            } catch (\Throwable $e) {
                // Fall through.
            }
        }
        return '';
    }

    /**
     * Render the time column.
     */
    public function col_timecreated(\stdClass $row): string {
        return userdate($row->timecreated);
    }
}
