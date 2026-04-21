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
 * Directions (courses/kierunki) listing within a recruitment.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

$rid = required_param('rid', PARAM_INT);

admin_externalpage_setup('local_recruitment');

$recruitment = $DB->get_record('local_recruitment', ['id' => $rid], '*', MUST_EXIST);

$pageurl = new moodle_url('/local/recruitment/courses.php', ['rid' => $rid]);

// Handle category visibility toggle.
// $togglecatvisibility holds the direction ID — the target_categoryid is resolved
// server-side so the URL never carries a raw category ID that could mismatch.
$togglecatvisibility = optional_param('togglecatvisibility', 0, PARAM_INT);
if ($togglecatvisibility) {
    require_sesskey();
    $direction = $DB->get_record(
        'local_recruitment_course',
        ['id' => $togglecatvisibility, 'recruitmentid' => $rid],
        'id, target_categoryid',
        MUST_EXIST
    );
    if (!empty($direction->target_categoryid)) {
        $cat = core_course_category::get($direction->target_categoryid, MUST_EXIST, true);
        if ($cat->visible) {
            $cat->hide();
        } else {
            $cat->show();
        }
    }
    redirect($pageurl);
}

$PAGE->set_url($pageurl);
$PAGE->set_title(get_string('directions', 'local_recruitment') . ': ' . format_string($recruitment->name));
$PAGE->set_heading(get_string('directions', 'local_recruitment') . ': ' . format_string($recruitment->name));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('directions', 'local_recruitment') . ': ' . format_string($recruitment->name));

// Back link and add button.
$headerdata = [
    'backurl' => (new moodle_url('/local/recruitment/index.php'))->out(false),
    'str_back' => get_string('backtorecruitments', 'local_recruitment'),
    'addurl' => (new moodle_url('/local/recruitment/course_edit.php', ['rid' => $rid]))->out(false),
    'str_add' => get_string('adddirection', 'local_recruitment'),
];
echo $OUTPUT->render_from_template('local_recruitment/courses_header', $headerdata);

// Display directions table.
$table = new \local_recruitment\output\direction_table('local-recruitment-directions', $pageurl, $rid);
$table->out(50, true);

echo $OUTPUT->footer();
