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
 * Upgrade steps for local_support.
 *
 * @package    local_support
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_support plugin.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool
 */
function xmldb_local_support_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026021300) {
        // Define table local_mail_history.
        $table = new xmldb_table('local_mail_history');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026021300, 'local', 'support');
    }

    if ($oldversion < 2026021601) {
        // Add 'training' column to quiz table for training mode.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('training', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'allowofflineattempts');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026021601, 'local', 'support');
    }

    if ($oldversion < 2026021901) {
        // Define table local_sms_history.
        $table = new xmldb_table('local_sms_history');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('phone', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, 'local_support');
        $table->add_field('success', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('response', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        $table->add_index('component_idx', XMLDB_INDEX_NOTUNIQUE, ['component']);
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026021901, 'local', 'support');
    }

    if ($oldversion < 2026022300) {
        $table = new xmldb_table('local_mail_history');
        $field = new xmldb_field('email', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2026022300, 'local', 'support');
    }

    if ($oldversion < 2026031601) {
        // Rename config keys from Polish 'ogrodnik_*' to English 'gardener_*'.
        $ogrodnikkeys = [
            'ogrodnik_smtp_heading',
            'ogrodnik_smtphosts',
            'ogrodnik_smtpport',
            'ogrodnik_smtpsecure',
            'ogrodnik_smtpuser',
            'ogrodnik_smtppass',
            'ogrodnik_smtp_fromemail',
            'ogrodnik_smtp_fromname',
        ];
        foreach ($ogrodnikkeys as $oldname) {
            $newname = str_replace('ogrodnik_', 'gardener_', $oldname);
            $oldval = get_config('local_support', $oldname);
            if ($oldval !== false) {
                set_config($newname, $oldval, 'local_support');
                unset_config($oldname, 'local_support');
            }
        }
        upgrade_plugin_savepoint(true, 2026031601, 'local', 'support');
    }

    if ($oldversion < 2026040901) {
        // Prohibit course secondary-navigation tabs for the student role:
        // Uczestnicy, Oceny, Aktywności, Kompetencje.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        if ($studentrole) {
            $context = context_system::instance();

            $capstoprevent = [
                'moodle/course:viewparticipants',         // Uczestnicy
                'moodle/grade:view',                       // Oceny (gradebook access)
                'gradereport/user:view',                   // Oceny (user grade report tab)
                'gradereport/overview:view',               // Oceny (overview report)
                'moodle/course:viewoverview',              // Aktywności
                'moodle/competency:coursecompetencyview',  // Kompetencje
            ];

            foreach ($capstoprevent as $cap) {
                // Only set if the capability actually exists in this Moodle version.
                if ($DB->record_exists('capabilities', ['name' => $cap])) {
                    assign_capability($cap, CAP_PROHIBIT, $studentrole->id, $context->id, true);
                }
            }
        }

        upgrade_plugin_savepoint(true, 2026040901, 'local', 'support');
    }

    if ($oldversion < 2026040902) {
        // Fix: gradereport/user:view was missing from the original prohibit list —
        // this is the capability that actually controls the Grades tab visibility
        // in the course secondary navigation (course_get_user_navigation_options).
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        if ($studentrole) {
            $context = context_system::instance();

            $missing = [
                'gradereport/user:view',      // Controls the Grades secondary-nav tab
                'gradereport/overview:view',  // Grades overview report
            ];

            foreach ($missing as $cap) {
                if ($DB->record_exists('capabilities', ['name' => $cap])) {
                    assign_capability($cap, CAP_PROHIBIT, $studentrole->id, $context->id, true);
                }
            }
        }

        upgrade_plugin_savepoint(true, 2026040902, 'local', 'support');
    }

    return true;
}
