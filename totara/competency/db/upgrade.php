<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.dro
 *
 * @author Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

/**
 * Database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 */
function xmldb_totara_competency_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 13 branching line.

    if ($oldversion < 2019070801) {
        global $DB;

        // Changes to totara_competency_configuration_history table
        $table = new xmldb_table('totara_competency_configuration_history');

        // Make assignment_id and active_to nullable
        $field = new xmldb_field('assignment_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->change_field_type($table, $field);

        $field = new xmldb_field('active_to', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->change_field_type($table, $field);

        // Changes to totara_competency_configuration_change table
        $table = new xmldb_table('totara_competency_configuration_change');

        // Change change_type to char(255) and create an index
        $table = new xmldb_table('totara_competency_configuration_change');

        // Reduce max length of change_type
        $field = new xmldb_field('change_type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);

        // Create an index
        $index = new xmldb_index('comconfch_ux', XMLDB_INDEX_UNIQUE, ['comp_id, assignment_id, time_changed, change_type']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }


        // Assign savepoint reached.
        upgrade_plugin_savepoint(true, 2019070801, 'totara', 'competency');
    }

    if ($oldversion < 2019073000) {

        // Define field related_info to be added to totara_competency_configuration_change.
        $table = new xmldb_table('totara_competency_configuration_change');
        $field = new xmldb_field('related_info', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'change_type');

        // Conditionally launch add field related_info.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019073000, 'totara', 'competency');
    }

    if ($oldversion < 2019082700) {

        // Define table totara_competency_temp_users to be created.
        $table = new xmldb_table('totara_competency_temp_users');

        // Adding fields to table auth_connect_ids.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('has_changed', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');

        // Adding keys to table auth_connect_ids.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for auth_connect_ids.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019082700, 'totara', 'competency');
    }

    if ($oldversion < 2019082701) {

        // Define field process_key to be added to totara_competency_temp_users
        $table = new xmldb_table('totara_competency_temp_users');
        $field = new xmldb_field('process_key', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Conditionally launch add field related_info.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019082701, 'totara', 'competency');
    }

    if ($oldversion < 2019082702) {
        $table = new xmldb_table('totara_competency_achievement_via');

        // Define key comachvia_comach_fk (foreign) to be added to totara_competency_achievement_via.
        $key = new xmldb_key('comachvia_comach_fk', XMLDB_KEY_FOREIGN, array('comp_achievement_id'), 'totara_competency_achievement', array('id'), 'cascade');

        // Launch add key comachvia_comach_fk.
        $dbman->add_key($table, $key);

        // Define key comachvia_pwach_fk (foreign) to be added to totara_competency_achievement_via.
        $key = new xmldb_key('comachvia_pwach_fk', XMLDB_KEY_FOREIGN, array('pathway_achievement_id'), 'totara_competency_pathway_achievement', array('id'), 'cascade');

        // Launch add key comachvia_pwach_fk.
        $dbman->add_key($table, $key);

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019082702, 'totara', 'competency');
    }

    return true;
}
