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
 * User progress report page.
 *
 * @package    local_userprogress
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_userprogress');

$context = context_system::instance();
require_capability('local/userprogress:view', $context);

$firstname = optional_param('firstname', '', PARAM_TEXT);
$lastname = optional_param('lastname', '', PARAM_TEXT);
$email = optional_param('email', '', PARAM_TEXT);
$recruitmentid = optional_param('recruitmentid', 0, PARAM_INT);
$directionid = optional_param('directionid', 0, PARAM_INT);
$resetfilters = optional_param('resetfilters', 0, PARAM_INT);

if ($resetfilters) {
    $firstname = '';
    $lastname = '';
    $email = '';
    $recruitmentid = 0;
    $directionid = 0;
}

$baseurl = new moodle_url('/local/userprogress/index.php', [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'recruitmentid' => $recruitmentid,
    'directionid' => $directionid,
]);

$PAGE->set_url($baseurl);
$PAGE->set_title(get_string('userprogress', 'local_userprogress'));
$PAGE->set_heading(get_string('userprogress', 'local_userprogress'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('userprogress', 'local_userprogress'));

// Filter form.
$recruitments = $DB->get_records_menu('local_recruitment', null, 'name ASC', 'id, name');
$recruitmentoptions = [0 => get_string('allrecruitments', 'local_userprogress')] + $recruitments;

$selectoptions = [];
foreach ($recruitmentoptions as $key => $value) {
    $selectoptions[] = ['value' => $key, 'label' => s($value), 'selected' => ($key == $recruitmentid)];
}

// Direction options filtered by selected recruitment.
$directionconditions = $recruitmentid ? ['recruitmentid' => $recruitmentid] : [];
$directions = $DB->get_records_menu('local_recruitment_course', $directionconditions, 'name ASC', 'id, name');
$directionoptions = [0 => get_string('allcourses', 'local_userprogress')] + $directions;

$directionselect = [];
foreach ($directionoptions as $key => $value) {
    $directionselect[] = ['value' => $key, 'label' => s($value), 'selected' => ($key == $directionid)];
}

$filterdata = [
    'actionurl' => (new moodle_url('/local/userprogress/index.php'))->out(false),
    'reseturl' => (new moodle_url('/local/userprogress/index.php', ['resetfilters' => 1]))->out(false),
    'fields' => [
        ['id' => 'filter-firstname', 'name' => 'firstname', 'label' => get_string('filterfirstname', 'local_userprogress'), 'type' => 'text', 'value' => s($firstname)],
        ['id' => 'filter-lastname', 'name' => 'lastname', 'label' => get_string('filterlastname', 'local_userprogress'), 'type' => 'text', 'value' => s($lastname)],
        ['id' => 'filter-email', 'name' => 'email', 'label' => get_string('filteremail', 'local_userprogress'), 'type' => 'text', 'value' => s($email)],
    ],
    'selects' => [
        ['id' => 'filter-recruitment', 'name' => 'recruitmentid', 'label' => get_string('filterrecruitment', 'local_userprogress'), 'options' => $selectoptions],
        ['id' => 'filter-direction', 'name' => 'directionid', 'label' => get_string('filtercourse', 'local_userprogress'), 'options' => $directionselect],
    ],
    'str_filter' => get_string('filter', 'local_userprogress'),
    'str_reset' => get_string('resetfilters', 'local_userprogress'),
];
echo $OUTPUT->render_from_template('local_userprogress/filter_form', $filterdata);

// Build filters array.
$filters = [];
if (!empty($firstname)) {
    $filters['firstname'] = $firstname;
}
if (!empty($lastname)) {
    $filters['lastname'] = $lastname;
}
if (!empty($email)) {
    $filters['email'] = $email;
}
if (!empty($recruitmentid)) {
    $filters['recruitmentid'] = $recruitmentid;
}
if (!empty($directionid)) {
    $filters['directionid'] = $directionid;
}

$table = new \local_userprogress\output\userprogress_table('userprogress_report', $baseurl, $filters);
$table->pagesize(25, 0);
$table->out(25, true);

// Make table rows clickable — each row carries a data-href set by the table class.
echo html_writer::script("
(function() {
    document.querySelectorAll('#userprogress_report tbody tr').forEach(function(tr) {
        var span = tr.querySelector('.row-href');
        if (!span) return;
        tr.style.cursor = 'pointer';
        tr.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') return;
            window.location.href = span.dataset.href;
        });
    });
})();
");

echo $OUTPUT->footer();
