<?php
/*
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
 * along with this program.  If not, see <http://www.gnu.org/licenses);.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package torara_criteria
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_pathway_manual_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 13 branching line.

    if ($oldversion < 2019050802) {
        global $DB;

        // Changes to pathway_manual table
        $table = new xmldb_table('pathway_manual');

        // Replace aggregation_type with aggregation_method and parameters to be consistent with other tables
        $field = new xmldb_field('aggregation_type', XMLDB_TYPE_CHAR, 100, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('aggregation_method', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('aggregation_params', XMLDB_TYPE_CHAR, 100, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add status field
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->execute(
            'UPDATE {pathway_manual}
            SET aggregation_method = 2, status = 0'
        );

        // Assign savepoint reached.
        upgrade_plugin_savepoint(true, 2019050802, 'pathway', 'manual');
    }

    if ($oldversion < 2019062700) {
        global $DB;

        // Changes to pathway_manual table
        $table = new xmldb_table('pathway_manual');

        // Add timemodified field
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_plugin_savepoint(true, 2019062700, 'pathway', 'manual');
    }

    if ($oldversion < 2019070301) {

        // Define table pathway_manual_rating to be dropped.
        $table = new xmldb_table('pathway_manual_value');

        // Conditionally launch drop table for pathway_manual_rating.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Criteria_group savepoint reached.
        upgrade_plugin_savepoint(true, 2019070301, 'pathway', 'manual');
    }

    if ($oldversion < 2019070302) {

        // Define table pathway_manual_rating to be created.
        $table = new xmldb_table('pathway_manual_rating');

        // Adding fields to table pathway_manual_rating.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comp_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scale_value_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('date_assigned', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assigned_by', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assigned_by_role', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comment', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table pathway_manual_rating.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('patmanrat_fk_compid', XMLDB_KEY_FOREIGN, array('comp_id'), 'comp', array('id'));
        $table->add_key('patmanrat_fk_userid', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));
        $table->add_key('patmanrat_fk_scavalid', XMLDB_KEY_FOREIGN, array('scale_value_id'), 'comp_scale_values', array('id'));
        $table->add_key('patmanrat_fk_assiby', XMLDB_KEY_FOREIGN, array('assigned_by'), 'user', array('id'));

        // Conditionally launch create table for pathway_manual_rating.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Criteria_group savepoint reached.
        upgrade_plugin_savepoint(true, 2019070302, 'pathway', 'manual');
    }

    if ($oldversion < 2019070303) {
        // Changes to pathway_manual_rating table
        $table = new xmldb_table('pathway_manual_rating');

        // Temporarily remove key
        $key = new xmldb_key('patmanrat_fk_assiby');
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }

        // Temporarily remove index
        $index = new xmldb_index('assigned_by', XMLDB_INDEX_NOTUNIQUE, array('assigned_by'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Allow 'assigned_by' field to be nullable
        $field = new xmldb_field('assigned_by');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->change_field_notnull($table, $field);

        // Add key back
        $table->add_key('patmanrat_fk_assiby', XMLDB_KEY_FOREIGN, array('assigned_by'), 'user', array('id'));

        // Criteria_group savepoint reached.
        upgrade_plugin_savepoint(true, 2019070303, 'pathway', 'manual');
    }

    return true;
}