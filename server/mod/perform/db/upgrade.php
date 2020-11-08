<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

/**
 * Database upgrade script
 *
 * @param integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return bool
 *
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_perform_upgrade($oldversion) {
    global $DB, $CFG;
    require_once(__DIR__ . '/upgradelib.php');

    $dbman = $DB->get_manager();

    if ($oldversion < 2020090103) {

        // Define field notified_at to be added to perform_manual_relation_selector.
        $table = new xmldb_table('perform_manual_relation_selector');
        $field = new xmldb_field('notified_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'user_id');

        // Conditionally launch add field notified_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020090103, 'perform');
    }

    if ($oldversion < 2020090104) {
        $sql = "UPDATE {report_builder}
            SET source = 'perform_manage_participation_participant_instance',
                shortname = 'perform_manage_participation_participant_instance'
            WHERE shortname = 'participant_instance_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_manage_participation_participant_section',
                shortname = 'perform_manage_participation_participant_section'
            WHERE shortname = 'participant_section_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_manage_participation_subject_instance',
                shortname = 'perform_manage_participation_subject_instance'
            WHERE shortname = 'subject_instance_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_response_element',
                shortname = 'perform_response_element_by_activity'
            WHERE shortname = 'element_performance_reporting_by_activity'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_response_element',
                shortname = 'perform_response_element_by_reporting_id'
            WHERE shortname = 'element_performance_reporting_by_reporting_id'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_participation_subject_instance',
                shortname = 'perform_participation_subject_instance'
            WHERE shortname = 'perform_subject_instance'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_participation_participant_instance',
                shortname = 'perform_participation_participant_instance'
            WHERE shortname = 'perform_participant_instance'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_response',
                shortname = 'perform_response_export'
            WHERE shortname = 'response_export_performance_reporting'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_response_subject_instance',
                shortname = 'perform_response_subject_instance'
            WHERE shortname = 'subject_instance_performance_reporting'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
            SET source = 'perform_response_user',
                shortname = 'perform_response_user'
            WHERE shortname = 'user_performance_reporting'";
        $DB->execute($sql);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020090104, 'perform');
    }

    // Totara 13.0 release line.

    if ($oldversion < 2020100101) {
        $sql = "UPDATE {report_builder}
                   SET source = 'perform_manage_participation_participant_instance'
                 WHERE source = 'participant_instance_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_manage_participation_participant_section'
                 WHERE source = 'participant_section_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_manage_participation_subject_instance'
                 WHERE source = 'subject_instance_manage_participation'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_participation_participant_instance'
                 WHERE source = 'perform_participant_instance'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_participation_participant_section'
                 WHERE source = 'perform_participant_section'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_participation_subject_instance'
                 WHERE source = 'perform_subject_instance'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_response_element'
                 WHERE source = 'element_performance_reporting'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_response_subject_instance'
                 WHERE source = 'subject_instance_performance_reporting'";
        $DB->execute($sql);

        $sql = "UPDATE {report_builder}
                   SET source = 'perform_response_user'
                 WHERE source = 'user_performance_reporting'";
        $DB->execute($sql);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020100101, 'perform');
    }

    if ($oldversion < 2020100102) {
        // Create records for existing activities that do not already have records for the following notifications:
        mod_perform_upgrade_create_missing_notification_records([
            'completion' => [],
            'due_date' => [],
            'due_date_reminder' => [86400], // Trigger: 1 day (in seconds)
            'instance_created' => [],
            'instance_created_reminder' => [86400], // Trigger: 1 day (in seconds)
            'overdue_reminder' => [86400], // Trigger: 1 day (in seconds)
            'participant_selection' => [],
            'reopened' => [],
        ]);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020100102, 'perform');
    }

    if ($oldversion < 2020100103) {
        // Define field task_id to be added to perform_subject_instance.
        $table = new xmldb_table('perform_subject_instance');
        $field = new xmldb_field('task_id', XMLDB_TYPE_CHAR, '32', null, null, null, null, 'updated_at');

        // Conditionally launch add field task_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('task_id', XMLDB_INDEX_NOTUNIQUE, array('task_id'));

        // Conditionally launch add index task_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field task_id to be added to perform_participant_instance.
        $table = new xmldb_table('perform_participant_instance');
        $field = new xmldb_field('task_id', XMLDB_TYPE_CHAR, '32', null, null, null, null, 'updated_at');

        // Conditionally launch add field task_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('task_id', XMLDB_INDEX_NOTUNIQUE, array('task_id'));

        // Conditionally launch add index task_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020100103, 'perform');
    }

    if ($oldversion < 2020100105) {
        mod_perform_upgrade_unwrap_response_data();

        upgrade_mod_savepoint(true, 2020100105, 'perform');
    }

    return true;
}
