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
 * Add section output class for format_archive.
 *
 * Overrides the core addsection to bypass the deprecated get_num_sections_data()
 * path. Even though format_archive defines numsections as a format option (required
 * for admin settings compatibility), section addition uses the modern component-based
 * approach identical to formats that do not define numsections.
 *
 * @package   format_archive
 * @copyright 2026 Custom Development
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_archive\output\courseformat\content;

use core_courseformat\output\local\content\addsection as addsection_base;
use renderer_base;
use stdClass;

/**
 * Renders the "add section" area for archive-format courses.
 *
 * Uses the modern get_add_section_data() path directly, skipping the
 * legacy get_num_sections_data() check that is triggered when numsections
 * exists as a format option.
 */
class addsection extends addsection_base {

    /**
     * Export data for the mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        if (!$this->format->show_editor(['moodle/course:update'])) {
            return new stdClass();
        }

        $format = $this->format;
        $course = $format->get_course();
        $lastsection = $format->get_last_section_number();

        $data = new stdClass();

        if (course_get_format($course)->uses_sections() && $format->supports_components()) {
            $data = $this->get_add_section_data($output, $lastsection);
        }

        if (count((array) $data)) {
            $data->showaddsection = true;
        }

        return $data;
    }
}
