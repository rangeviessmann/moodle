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
 * Preparation course format — shows quizzes with training buttons when not editing.
 *
 * @package format_preparation
 * @copyright 2006 The Open University
 * @author N.D.Freear@open.ac.uk, and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Horrible backwards compatible parameter aliasing.
if ($topic = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing.

// Retrieve course format option fields and add them to the $course object.
$format = course_get_format($course);
$course = $format->get_course();
$context = context_course::instance($course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

if ($PAGE->user_is_editing()) {
    // Standard editing view.
    $renderer = $PAGE->get_renderer('format_preparation');

    if (!is_null($displaysection)) {
        $format->set_sectionnum($displaysection);
    }
    $outputclass = $format->get_output_classname('content');
    $widget = new $outputclass($format);
    echo $renderer->render($widget);
} else {
    // Custom preparation view: list all quizzes with training buttons + attempt history.
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $modinfo = get_fast_modinfo($course);
    $quizmodules = isset($modinfo->instances['quiz']) ? $modinfo->instances['quiz'] : [];

    echo '<div class="preparation-course-view container-fluid px-0">';

    if (empty($quizmodules)) {
        echo '<div class="alert alert-info">' . get_string('noquizzes', 'format_preparation') . '</div>';
    }

    foreach ($quizmodules as $cm) {
        if (!$cm->uservisible) {
            continue;
        }

        $quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
        $quizcontext = context_module::instance($cm->id);

        $canattempt = has_capability('mod/quiz:attempt', $quizcontext);
        $canpreview = has_capability('mod/quiz:preview', $quizcontext);

        if (!$canattempt && !$canpreview) {
            continue;
        }

        // Count questions available in the question bank.
        $questioncount = $DB->count_records_sql(
            "SELECT COUNT(qbe.id)
               FROM {question_bank_entries} qbe
               JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
              WHERE qc.contextid = :contextid",
            ['contextid' => $quizcontext->id]
        );

        // Check for unfinished attempt.
        $unfinishedattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id);
        $unfinished = false;
        if ($unfinishedattempt) {
            $unfinished = in_array($unfinishedattempt->state, [
                \mod_quiz\quiz_attempt::IN_PROGRESS,
                \mod_quiz\quiz_attempt::OVERDUE,
            ]);
        }

        echo '<div class="preparation-quiz-card card mb-4 shadow-sm">';
        echo '<div class="card-header py-3">';
        echo '<h3 class="mb-0 h5">' . format_string($quiz->name) . '</h3>';
        echo '</div>';
        echo '<div class="card-body">';

        if ($unfinished && $unfinishedattempt) {
            // Show continue button for unfinished attempt.
            $continueurl = new moodle_url('/mod/quiz/attempt.php', ['attempt' => $unfinishedattempt->id]);
            echo '<div class="d-flex justify-content-center mb-4">';
            echo '<a href="' . $continueurl->out(false) . '" class="btn btn-warning btn-lg">';
            echo s(get_string('continueattemptquiz', 'quiz'));
            echo '</a>';
            echo '</div>';
        } else if ($questioncount > 0) {
            // Show training start buttons.
            $actionurl = new moodle_url('/mod/quiz/startattempt.php');
            echo '<div class="d-flex gap-3 justify-content-center mb-4 flex-wrap">';
            foreach ([10, 20, 40] as $num) {
                if ($num > $questioncount) {
                    continue;
                }
                $btnlabel = get_string('starttraining', 'format_preparation', $num);
                echo '<form method="post" action="' . $actionurl->out(false) . '">';
                echo '<input type="hidden" name="cmid" value="' . (int)$cm->id . '">';
                echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
                echo '<input type="hidden" name="numquestions" value="' . (int)$num . '">';
                echo '<button type="submit" class="btn btn-primary btn-lg">';
                echo s($btnlabel);
                echo '</button>';
                echo '</form>';
            }
            echo '</div>';
        } else {
            echo '<p class="text-muted text-center">' . get_string('noquestions', 'quiz') . '</p>';
        }

        // Attempt history.
        $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'finished', true);
        if (!empty($attempts)) {
            echo '<h4 class="h6 mt-3">' . get_string('attempthistory', 'format_preparation') . '</h4>';
            echo '<div class="table-responsive">';
            echo '<table class="table table-sm table-striped generaltable">';
            echo '<thead class="thead-light"><tr>';
            echo '<th scope="col">' . get_string('attemptnumber', 'format_preparation') . '</th>';
            echo '<th scope="col">' . get_string('timecompleted', 'quiz') . '</th>';
            echo '<th scope="col">' . get_string('score', 'format_preparation') . '</th>';
            echo '<th scope="col"></th>';
            echo '</tr></thead>';
            echo '<tbody>';
            foreach (array_reverse($attempts) as $attempt) {
                $score = '-';
                if (!is_null($attempt->sumgrades)) {
                    // Count actual question slots from layout string.
                    $numslots = 0;
                    if (!empty($attempt->layout)) {
                        foreach (explode(',', $attempt->layout) as $p) {
                            if ((int)trim($p) > 0) {
                                $numslots++;
                            }
                        }
                    }
                    if ($numslots > 0) {
                        $percent = round($attempt->sumgrades / $numslots * 100, 2);
                        $score = round((float)$attempt->sumgrades, 2) . '/' . $numslots . ' (' . $percent . '%)';
                    } else {
                        $score = quiz_format_grade($quiz, $attempt->sumgrades);
                    }
                }
                $coursereturnurl = (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false);
                $reviewurl = new moodle_url('/mod/quiz/review.php', [
                    'attempt'   => $attempt->id,
                    'returnurl' => $coursereturnurl,
                ]);
                echo '<tr>';
                echo '<td>' . (int)$attempt->attempt . '</td>';
                echo '<td>' . userdate($attempt->timefinish) . '</td>';
                echo '<td>' . $score . '</td>';
                echo '<td><a href="' . $reviewurl->out(false) . '" class="btn btn-sm btn-outline-primary">'
                    . get_string('enter', 'format_preparation') . '</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<p class="text-muted small">' . get_string('noattempts', 'format_preparation') . '</p>';
        }

        echo '</div>'; // .card-body
        echo '</div>'; // .preparation-quiz-card
    }

    echo '</div>'; // .preparation-course-view
}
