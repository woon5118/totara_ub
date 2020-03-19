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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package totara
 * @subpackage plan
 */

require_once($CFG->dirroot.'/totara/plan/db/upgradelib.php');

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_plan_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    // TL-14290 duedate in dp_plan_program_assign must not be -1, instead use 0.
    if ($oldversion < 2017050500) {
        totara_plan_upgrade_fix_invalid_program_duedates();

        upgrade_plugin_savepoint(true, 2017050500, 'totara', 'plan');
    }

    if ($oldversion < 2017051800) {
        // Rename columns types to type 'plan'.
        reportbuilder_rename_data('columns', 'dp_course', 'course_completion', 'statusandapproval', 'plan', 'statusandapproval');
        reportbuilder_rename_data('columns', 'dp_course', 'course', 'status', 'plan', 'coursestatus');

        upgrade_plugin_savepoint(true, 2017051800, 'totara', 'plan');
    }

    if ($oldversion < 2017070600) {

        // Add a timecreated field.
        $table = new xmldb_table('dp_plan_objective');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add a timemodified field.
        $table = new xmldb_table('dp_plan_objective');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2017070600, 'totara', 'plan');
    }

    if ($oldversion < 2018052300) {
        // Clean up orphaned files from any previously deleted evidence.
        totara_plan_upgrade_clean_deleted_evidence_files();

        upgrade_plugin_savepoint(true, 2018052300, 'totara', 'plan');
    }

    if ($oldversion < 2019012200) {
        // Rename columns types to type 'plan'.
        reportbuilder_rename_data('columns', 'dp_course', 'course_completion', 'status', 'plan', 'courseprogress');
        reportbuilder_rename_data('filters', 'dp_course', 'course_completion', 'status', 'plan', 'courseprogress');

        upgrade_plugin_savepoint(true, 2019012200, 'totara', 'plan');
    }

    if ($oldversion < 2020062600) {
        totara_plan_upgrade_remove_evidence_tables();

        upgrade_plugin_savepoint(true, 2020062600, 'totara', 'plan');
    }

    if ($oldversion < 2020062900) {
        // Covert any programs assigned to plans as actual program assignments using the new plan assignment type.
        totara_plan_upgrade_do_program_assignments();

        upgrade_plugin_savepoint(true, 2020062900, 'totara', 'plan');
    }

    if ($oldversion < 2020062901) {

        // Define table dp_plan_competency_value to be created.
        $table = new xmldb_table('dp_plan_competency_value');

        // Adding fields to table dp_plan_competency_value.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('competency_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scale_value_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('date_assigned', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('positionid', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('organisationid', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('assessorid', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('assessorname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('assessmenttype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timeproficient', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('manual', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table dp_plan_competency_value.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('competency_id_fk', XMLDB_KEY_FOREIGN, array('competency_id'), 'comp', array('id'));
        $table->add_key('user_id_fk', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));
        $table->add_key('scale_value_id_fk', XMLDB_KEY_FOREIGN, array('scale_value_id'), 'comp_scale_values', array('id'));
        $table->add_key('positionid_fk', XMLDB_KEY_FOREIGN, array('positionid'), 'pos', array('id'));
        $table->add_key('organisationid_fk', XMLDB_KEY_FOREIGN, array('organisationid'), 'org', array('id'));
        $table->add_key('assessorid_fk', XMLDB_KEY_FOREIGN, array('assessorid'), 'user', array('id'));

        $table->add_index('comp_user_unique', XMLDB_INDEX_UNIQUE, array('competency_id', 'user_id'));

        // Conditionally launch create table for dp_plan_competency_value.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Plan savepoint reached.
        upgrade_plugin_savepoint(true, 2020062901, 'totara', 'plan');
    }

    // TODO Remove this before we release perform

    if ($oldversion < 2020062903) {
        // Define key competency_id_fk (foreign) to be added to dp_plan_competency_value.
        $table = new xmldb_table('dp_plan_competency_value');

        $key = new xmldb_key('competency_id_fk', XMLDB_KEY_FOREIGN, array('competency_id'), 'comp', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $key = new xmldb_key('user_id_fk', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $key = new xmldb_key('scale_value_id_fk', XMLDB_KEY_FOREIGN, array('scale_value_id'), 'comp_scale_values', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $key = new xmldb_key('positionid_fk', XMLDB_KEY_FOREIGN, array('positionid'), 'pos', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $key = new xmldb_key('organisationid_fk', XMLDB_KEY_FOREIGN, array('organisationid'), 'org', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $key = new xmldb_key('assessorid_fk', XMLDB_KEY_FOREIGN, array('assessorid'), 'user', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $index = new xmldb_index('comp_user_unique', XMLDB_INDEX_UNIQUE, array('competency_id', 'user_id'));

        // Conditionally launch add index comp_user_unique.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Plan savepoint reached.
        upgrade_plugin_savepoint(true, 2020062903, 'totara', 'plan');
    }

    return true;
}
