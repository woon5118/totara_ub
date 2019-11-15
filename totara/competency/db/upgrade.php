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

    if ($oldversion < 2019090600) {
        // Define field process_key to be added to totara_competency_temp_users
        $table = new xmldb_table('totara_competency_temp_users');
        $field = new xmldb_field('update_operation_name', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Conditionally launch add field related_info.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019090600, 'totara', 'competency');
    }

    if ($oldversion < 2019101500) {
        // Previous 2019082702
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
        upgrade_plugin_savepoint(true, 2019101500, 'totara', 'competency');
    }

    if ($oldversion < 2019102300) {
        // Define table totara_competency_temp_users to be renamed to totara_competency_aggregation_queue.
        $table = new xmldb_table('totara_competency_temp_users');

        // Launch rename table for totara_competency_temp_users.
        $dbman->rename_table($table, 'totara_competency_aggregation_queue');

        // Define field competency_id to be added to totara_competency_temp_users.
        $table = new xmldb_table('totara_competency_aggregation_queue');
        $field = new xmldb_field('competency_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'user_id');

        // Conditionally launch add field user_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('user_competency_id', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'competency_id']);
        // Conditionally launch add index .
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('competency_id', XMLDB_INDEX_NOTUNIQUE, ['competency_id']);
        // Conditionally launch add index .
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('process_key', XMLDB_INDEX_NOTUNIQUE, ['process_key']);
        // Conditionally launch add index .
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }


        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019102300, 'totara', 'competency');
    }

    if ($oldversion < 2019110800) {
        $table = new xmldb_table('totara_assignment_competencies');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'totara_competency_assignments');
        }

        $table = new xmldb_table('totara_assignment_competency_users');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'totara_competency_assignment_users');
        }

        $table = new xmldb_table('totara_assignment_competencies_users_log');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'totara_competency_assignment_user_logs');
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019110800, 'totara', 'competency');
    }

    if ($oldversion < 2019110802) {
        // Delete old scheduled task
        $DB->delete_records('task_scheduled', ['classname' => '\tassign_competency\task\expand_assignments_task']);
        $DB->delete_records('task_adhoc', ['classname' => '\tassign_competency\task\expand_assignments_task']);

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019110802, 'totara', 'competency');
    }

    if ($oldversion < 2019110803) {
        // Let's check whether we need to do anything at all
        global $DB, $CFG;

        // If it's already been created, no point to waste resources on running descriptions upgrade
        if (!$DB->record_exists('external_functions', ['name' => 'core_user_index'])) {
            require_once $CFG->libdir . '/db/upgradelib.php';
            // This will remove old web services added by totara_assignment
            external_update_descriptions('totara_assignment');

            // This will refresh external services from core without an explicit version bumps
            external_update_descriptions('moodle');

            // Competency savepoint reached.
            upgrade_plugin_savepoint(true, 2019110803, 'totara', 'competency');
        }
    }

    // Add more foreign keys
    if ($oldversion < 2019111400) {
        global $DB;

        $dbman = $DB->get_manager();

        $key = new xmldb_key('fk_assignment_id', XMLDB_KEY_FOREIGN, ['assignment_id'], 'totara_competency_assignments', ['id'], 'cascade');

        $table = new xmldb_table('totara_competency_achievement');
        $dbman->add_key($table, $key);

        $table = new xmldb_table('totara_competency_configuration_change');
        $dbman->add_key($table, $key);

        $table = new xmldb_table('totara_competency_configuration_history');
        $dbman->add_key($table, $key);

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019111400, 'totara', 'competency');
    }

    if ($oldversion < 2019111500) {

        // Define index ix_sortorder (not unique) to be added to totara_competency_pathway.
        $table = new xmldb_table('totara_competency_pathway');
        $index = new xmldb_index('ix_sortorder', XMLDB_INDEX_NOTUNIQUE, array('sortorder'));

        // Conditionally launch add index ix_sortorder.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_path_instance_id', XMLDB_INDEX_NOTUNIQUE, array('path_instance_id'));

        // Conditionally launch add index ix_path_instance_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_path_type_instance_id', XMLDB_INDEX_NOTUNIQUE, array('path_type', 'path_instance_id'));

        // Conditionally launch add index ix_path_type_instance_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_status', XMLDB_INDEX_NOTUNIQUE, array('status'));

        // Conditionally launch add index ix_status.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define key comupwach_pw_fk (foreign) to be dropped form totara_competency_pathway_achievement.
        $table = new xmldb_table('totara_competency_pathway_achievement');
        $key = new xmldb_key('comupwach_pw_fk', XMLDB_KEY_FOREIGN, array('pathway_id'), 'totara_competency_pathway', array('id'));

        // Launch drop key comupwach_pw_fk.
        $dbman->drop_key($table, $key);

        $key = new xmldb_key('comupwach_pw_fk', XMLDB_KEY_FOREIGN, array('pathway_id'), 'totara_competency_pathway', array('id'), 'cascade');

        // Launch add key comupwach_pw_fk.
        $dbman->add_key($table, $key);

        // Define index comconfch_ux (unique) to be dropped form totara_competency_configuration_change.
        $table = new xmldb_table('totara_competency_configuration_change');
        $index = new xmldb_index('comconfch_ux', XMLDB_INDEX_UNIQUE, array('comp_id', 'assignment_id', 'time_changed', 'change_type'));

        // Conditionally launch drop index comconfch_ux.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define key fk_comp_id (foreign) to be added to totara_competency_achievement.
        $table = new xmldb_table('totara_competency_achievement');
        $key = new xmldb_key('fk_comp_id', XMLDB_KEY_FOREIGN, array('comp_id'), 'comp', array('id'));

        // Launch add key fk_comp_id.
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fk_user_id', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));

        // Launch add key fk_user_id.
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fk_scale_value_id', XMLDB_KEY_FOREIGN, array('scale_value_id'), 'comp_scale_values', array('id'));

        // Launch add key fk_scale_value_id.
        $dbman->add_key($table, $key);

        $index = new xmldb_index('ix_comp_id_user_id', XMLDB_INDEX_NOTUNIQUE, array('comp_id', 'user_id'));

        // Conditionally launch add index ix_comp_id_user_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define key fk_comp_id (foreign) to be added to totara_competency_configuration_history.
        $table = new xmldb_table('totara_competency_configuration_history');
        $key = new xmldb_key('fk_comp_id', XMLDB_KEY_FOREIGN, array('comp_id'), 'comp', array('id'));

        // Launch add key fk_comp_id.
        $dbman->add_key($table, $key);

        // Define key fk_user_id (foreign) to be added to totara_competency_aggregation_queue.
        $table = new xmldb_table('totara_competency_aggregation_queue');
        $key = new xmldb_key('fk_user_id', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));

        // Launch add key fk_user_id.
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fk_competency_id', XMLDB_KEY_FOREIGN, array('competency_id'), 'comp', array('id'));

        // Launch add key fk_competency_id.
        $dbman->add_key($table, $key);

        $index = new xmldb_index('competency_id', XMLDB_INDEX_NOTUNIQUE, array('competency_id'));

        // Conditionally launch drop index competency_id.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index created_at (not unique) to be dropped form totara_competency_assignment_user_logs.
        $table = new xmldb_table('totara_competency_assignment_user_logs');
        $index = new xmldb_index('created_at', XMLDB_INDEX_NOTUNIQUE, array('action'));

        // Conditionally launch drop index created_at.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('created_at', XMLDB_INDEX_NOTUNIQUE, array('created_at'));

        // Conditionally launch add index created_at.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019111500, 'totara', 'competency');
    }


    return true;
}
