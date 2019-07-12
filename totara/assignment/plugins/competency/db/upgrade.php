<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

use tassign_competency\entities\assignment;
use totara_assignment\user_groups;

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_tassign_competency_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019020710) {
        // add new type field
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '25', null, true, null, null, 'id');

        $table = new xmldb_table('totara_assignment_competencies');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set all existing user records to admin
        $DB->execute("
            UPDATE {totara_assignment_competencies} 
            SET type = :type 
            WHERE type = '' and user_group_type = :user_group_type
        ", ['type' => 'admin', 'user_group_type' => 'user']);

        // the rest to auto
        $DB->execute("
            UPDATE {totara_assignment_competencies} 
            SET type = :type 
            WHERE type = '' and user_group_type != :user_group_type
        ", ['type' => 'auto', 'user_group_type' => 'user']);

        $index = new xmldb_index('type-user_group_type-user_group_id_ix', XMLDB_INDEX_NOTUNIQUE, ['type', 'user_group_type', 'user_group_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('competency_id_ix', XMLDB_INDEX_NOTUNIQUE, ['competency_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('user_group_id_ix', XMLDB_INDEX_NOTUNIQUE, ['user_group_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('user_group_type_ix', XMLDB_INDEX_NOTUNIQUE, ['user_group_type']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('type_ix', XMLDB_INDEX_NOTUNIQUE, ['type']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2019020710, 'tassign', 'competency');
    }

    if ($oldversion < 2019021800) {
        // Change auto to admin
        $DB->execute("
            UPDATE {totara_assignment_competencies} 
            SET type = :type 
            WHERE type = :type2
        ", ['type' => 'admin', 'type2' => 'auto']);

        upgrade_plugin_savepoint(true, 2019021800, 'tassign', 'competency');
    }

    if ($oldversion < 2019021801) {
        // Define table totara_assignment_competencies_users_log to be created.
        $table = new xmldb_table('totara_assignment_competencies_users_log');

        // Adding fields to table totara_assignment_competencies_users_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assignment_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table totara_assignment_competencies_users_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_assignment_id', XMLDB_KEY_FOREIGN, array('assignment_id'), 'totara_assignment_competencies', array('id'));
        $table->add_key('fk_user_id', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));

        // Adding indexes to table totara_assignment_competencies_users_log.
        $table->add_index('action', XMLDB_INDEX_NOTUNIQUE, array('action'));
        $table->add_index('created_at', XMLDB_INDEX_NOTUNIQUE, array('action'));

        // Conditionally launch create table for totara_assignment_competencies_users_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019021801, 'tassign', 'competency');
    }

    if ($oldversion < 2019071200) {
        $table = new xmldb_table('totara_assignment_competencies');
        $key = new xmldb_key('fk_competency_id', XMLDB_KEY_FOREIGN, array('competency_id'), 'comp', array('id'), 'cascade');
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }
        $dbman->add_key($table, $key);

        $table = new xmldb_table('totara_assignment_competency_users');
        $key = new xmldb_key('fk_assignment_id', XMLDB_KEY_FOREIGN, array('assignment_id'), 'totara_assignment_competencies', array('id'), 'cascade');
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fk_competency_id', XMLDB_KEY_FOREIGN, array('competency_id'), 'comp', array('id'), 'cascade');
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }
        $dbman->add_key($table, $key);

        $table = new xmldb_table('totara_assignment_competencies_users_log');
        $key = new xmldb_key('fk_assignment_id', XMLDB_KEY_FOREIGN, array('assignment_id'), 'totara_assignment_competencies', array('id'), 'cascade');
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }
        $dbman->add_key($table, $key);

        upgrade_plugin_savepoint(true, 2019071200, 'tassign', 'competency');
    }


    return true;
}
