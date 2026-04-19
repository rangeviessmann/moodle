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
 * Event fired when a user plays a YouTube video.
 *
 * @package    local_activityreport
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_activityreport\event;

defined('MOODLE_INTERNAL') || die();

/**
 * YouTube video played event.
 */
class youtube_video_played extends \core\event\base {

    /**
     * Init method.
     */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventname_youtube_played', 'local_activityreport');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        $a = new \stdClass();
        $a->userid   = $this->userid;
        $a->videourl = $this->other['videourl'] ?? '';
        $a->pagetitle = $this->other['pagetitle'] ?? '';
        return get_string('eventdesc_youtube_played', 'local_activityreport', $a);
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url(): \moodle_url {
        return new \moodle_url('/course/view.php', ['id' => $this->courseid]);
    }
}
