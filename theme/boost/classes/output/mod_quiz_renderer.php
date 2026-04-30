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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz renderer override for theme_boost.
 * Adds the "Submit all and finish" button at the top of the summary page,
 * next to the "Back" button in the tertiary navigation bar.
 *
 * @package    theme_boost
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_boost_mod_quiz_renderer extends mod_quiz\output\renderer {

    /**
     * Create the summary page with the finish button duplicated at the top.
     *
     * @param quiz_attempt $attemptobj
     * @param display_options $displayoptions
     */
    public function summary_page($attemptobj, $displayoptions) {
        $output = '';
        $output .= $this->header();
        $output .= $this->summary_tertiary_nav($attemptobj);
        $output .= $this->heading(format_string($attemptobj->get_quiz_name()));
        $output .= $this->heading(get_string('summaryofattempt', 'quiz'), 3);
        $output .= $this->summary_table($attemptobj, $displayoptions);
        $output .= $this->summary_page_controls($attemptobj);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the tertiary nav for the summary page with the finish button added at the top.
     *
     * @param quiz_attempt $attemptobj
     * @return string HTML
     */
    protected function summary_tertiary_nav($attemptobj): string {
        $output = '';
        $output .= \html_writer::start_div('tertiary-navigation');
        $output .= \html_writer::start_div('row align-items-center');

        // Back button (same as standard during_attempt_tertiary_nav).
        $output .= \html_writer::start_div('navitem');
        $output .= \html_writer::link(
            $attemptobj->view_url(),
            get_string('back'),
            ['class' => 'btn btn-secondary']
        );
        $output .= \html_writer::end_div();

        // Finish button — only when attempt is still in progress.
        if ($attemptobj->get_state() == \mod_quiz\quiz_attempt::IN_PROGRESS) {
            $options = [
                'attempt'      => $attemptobj->get_attemptid(),
                'finishattempt'=> 1,
                'timeup'       => 0,
                'slots'        => '',
                'cmid'         => $attemptobj->get_cmid(),
                'sesskey'      => sesskey(),
            ];
            $actionurl = new \moodle_url($attemptobj->processattempt_url(), $options);

            $output .= \html_writer::start_div('navitem ms-2');
            $output .= \html_writer::start_tag('form', [
                'method' => 'post',
                'action' => $actionurl->out_omit_querystring(),
                'id'     => 'frm-finishattempt-top',
            ]);
            foreach ($options as $name => $value) {
                $output .= \html_writer::empty_tag('input', [
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ]);
            }
            $output .= \html_writer::tag('button',
                get_string('submitallandfinish', 'quiz'),
                ['type' => 'submit', 'class' => 'btn btn-primary']
            );
            $output .= \html_writer::end_tag('form');
            $output .= \html_writer::end_div();
        }

        $output .= \html_writer::end_div();
        $output .= \html_writer::end_div();
        return $output;
    }
}
