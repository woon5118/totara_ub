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

use mod_perform\entities\activity\element;
use mod_perform\models\activity\element_identifier;

defined('MOODLE_INTERNAL') || die();

function xmldb_perform_upgrade($oldversion) {
    global $DB, $CFG;

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

    return true;
}
