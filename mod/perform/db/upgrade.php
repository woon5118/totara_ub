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
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020022501) {
        // Define table perform_section to be created.
        $table = new xmldb_table('perform_section');

        // Adding fields to table perform_section.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_section.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activity_id', XMLDB_KEY_FOREIGN, array('activity_id'), 'perform', array('id'), 'cascade');

        // Conditionally launch create table for perform_section.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022501, 'perform');
    }

    if ($oldversion < 2020022502) {
        // Define table perform_relationship to be created.
        $table = new xmldb_table('perform_relationship');

        // Adding fields to table perform_relationship.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_relationship.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activity_id', XMLDB_KEY_FOREIGN, array('activity_id'), 'perform', array('id'), 'cascade');

        // Adding indexes to table perform_relationship.
        $table->add_index('activity_id_classname', XMLDB_INDEX_UNIQUE, array('activity_id', 'classname'));

        // Conditionally launch create table for perform_relationship.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022502, 'perform');
    }

    if ($oldversion < 2020022503) {
        // Define table perform_section_relationship to be created.
        $table = new xmldb_table('perform_section_relationship');

        // Adding fields to table perform_section_relationship.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('perform_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('can_view', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('can_answer', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_section_relationship.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'cascade');
        $table->add_key('perform_relationship_id', XMLDB_KEY_FOREIGN, array('perform_relationship_id'), 'perform_relationship', array('id'), 'cascade');

        // Adding indexes to table perform_section_relationship.
        $table->add_index('can_view', XMLDB_INDEX_NOTUNIQUE, array('can_view'));
        $table->add_index('can_answer', XMLDB_INDEX_NOTUNIQUE, array('can_answer'));
        $table->add_index('section_perform_relationship', XMLDB_INDEX_UNIQUE, array('section_id', 'perform_relationship_id'));

        // Conditionally launch create table for perform_section_relationship.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022503, 'perform');
    }

    if ($oldversion < 2020022504) {
        // Define table perform_element to be created.
        $table = new xmldb_table('perform_element');

        // Adding fields to table perform_element.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('pluginname', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('identifier', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('element_data', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table perform_element.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for perform_element.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022504, 'perform');
    }

    if ($oldversion < 2020022505) {
        // Define table perform_section_element to be created.
        $table = new xmldb_table('perform_section_element');

        // Adding fields to table perform_section_element.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('element_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sort_order', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_section_element.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'cascade');
        $table->add_key('element_id', XMLDB_KEY_FOREIGN, array('element_id'), 'perform_element', array('id'), 'cascade');

        // Adding indexes to table perform_section_element.
        $table->add_index('sort_order', XMLDB_INDEX_NOTUNIQUE, array('sort_order'));
        $table->add_index('section_sort_order', XMLDB_INDEX_UNIQUE, array('section_id', 'sort_order'));

        // Conditionally launch create table for perform_section_element.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022505, 'perform');
    }

    if ($oldversion < 2020022506) {
        // Define table perform_subject_instance to be created.
        $table = new xmldb_table('perform_subject_instance');

        // Adding fields to table perform_subject_instance.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('subject_user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_subject_instance.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('subject_user_id', XMLDB_KEY_FOREIGN, array('subject_user_id'), 'user', array('id'));

        // Conditionally launch create table for perform_subject_instance.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022506, 'perform');
    }

    if ($oldversion < 2020022507) {
        // Define table perform_participant_instance to be created.
        $table = new xmldb_table('perform_participant_instance');

        // Adding fields to table perform_participant_instance.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('perform_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('participant_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table perform_participant_instance.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('perform_relationship_id', XMLDB_KEY_FOREIGN, array('perform_relationship_id'), 'perform_relationship', array('id'));
        $table->add_key('subject_instance_id', XMLDB_KEY_FOREIGN, array('subject_instance_id'), 'perform_subject_instance', array('id'), 'cascade');

        // Conditionally launch create table for perform_participant_instance.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022507, 'perform');
    }

    if ($oldversion < 2020022508) {
        // Define table perform_element_response to be created.
        $table = new xmldb_table('perform_element_response');

        // Adding fields to table perform_element_response.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('section_element_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('participant_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('response_data', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table perform_element_response.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('section_element_id', XMLDB_KEY_FOREIGN, array('section_element_id'), 'perform_section_element', array('id'));
        $table->add_key('participant_instance_id', XMLDB_KEY_FOREIGN, array('participant_instance_id'), 'perform_participant_instance', array('id'));

        // Adding indexes to table perform_element_response.
        $table->add_index('element_participant_instance', XMLDB_INDEX_UNIQUE, array('section_element_id', 'participant_instance_id'));

        // Conditionally launch create table for perform_element_response.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022508, 'perform');
    }

    if ($oldversion < 2020022600) {
        // Define table perform_track to be created.
        $table = new xmldb_table('perform_track');

        // Adding fields to table perform_track.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('archived_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_track.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activity_id', XMLDB_KEY_FOREIGN, array('activity_id'), 'perform', array('id'), 'cascade');

        // Adding indexes to table perform_track.
        $table->add_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));

        // Conditionally launch create table for perform_track.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_track_assignment to be created.
        $table = new xmldb_table('perform_track_assignment');

        // Adding fields to table perform_track_assignment.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('track_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_group_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_group_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('created_by', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('archived_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('expand', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table perform_track_assignment.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('track_id', XMLDB_KEY_FOREIGN, array('track_id'), 'perform_track', array('id'), 'cascade');

        // Adding indexes to table perform_track_assignment.
        $table->add_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));
        $table->add_index('user_group_type', XMLDB_INDEX_NOTUNIQUE, array('user_group_type'));
        $table->add_index('user_group_id', XMLDB_INDEX_NOTUNIQUE, array('user_group_id'));

        // Conditionally launch create table for perform_track_assignment.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_track_user_assignment to be created.
        $table = new xmldb_table('perform_track_user_assignment');

        // Adding fields to table perform_track_user_assignment.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('track_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject_user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_track_user_assignment.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('track_id', XMLDB_KEY_FOREIGN, array('track_id'), 'perform_track', array('id'), 'cascade');
        $table->add_key('subject_user_id', XMLDB_KEY_FOREIGN, array('subject_user_id'), 'user', array('id'));

        // Conditionally launch create table for perform_track_user_assignment.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020022600, 'perform');
    }

    if ($oldversion < 2020022601) {
        // Changes to perform table
        $table = new xmldb_table('perform');

        // add description field
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, false, false, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2020022601, 'perform');
    }

    if ($oldversion < 2020030300) {

        // Rename field updated_at on table perform to updated_at.
        $table = new xmldb_table('perform');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'description');

        // Launch rename field updated_at.
        $dbman->rename_field($table, $field, 'updated_at');

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030300, 'perform');
    }

    if ($oldversion < 2020030301) {

        // Define field archived_at to be dropped from perform_track.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('archived_at');

        // Conditionally launch drop field updated_at.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030301, 'perform');
    }

    if ($oldversion < 2020030302) {

        // Define index user_group_type_user_group_id (not unique) to be added to perform_track_assignment.
        $table = new xmldb_table('perform_track_assignment');
        $index = new xmldb_index('user_group_type_user_group_id', XMLDB_INDEX_NOTUNIQUE, array('user_group_type', 'user_group_id'));

        // Conditionally launch add index user_group_type_user_group_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030302, 'perform');
    }

    if ($oldversion < 2020030303) {

        // Define index track_id_type_group_type_group_id (unique) to be added to perform_track_assignment.
        $table = new xmldb_table('perform_track_assignment');
        $index = new xmldb_index('track_id_type_group_type_group_id', XMLDB_INDEX_UNIQUE, array('track_id', 'type', 'user_group_type', 'user_group_id'));

        // Conditionally launch add index track_id_type_group_type_group_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030303, 'perform');
    }

    if ($oldversion < 2020030304) {

        // Define field archived_at to be dropped from perform_track_assignment.
        $table = new xmldb_table('perform_track_assignment');
        $field = new xmldb_field('archived_at');

        // Conditionally launch drop field updated_at.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030304, 'perform');
    }

    if ($oldversion < 2020030305) {

        // Define index status (not unique) to be dropped form perform_track_assignment.
        $table = new xmldb_table('perform_track_assignment');
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));

        // Conditionally launch drop index status.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030305, 'perform');
    }


    if ($oldversion < 2020030306) {

        // Define field status to be dropped from perform_track_assignment.
        $table = new xmldb_table('perform_track_assignment');
        $field = new xmldb_field('status');

        // Conditionally launch drop field status.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030306, 'perform');
    }

    if ($oldversion < 2020030500) {
        // Define table perform_track_user_assignment_via to be created.
        $table = new xmldb_table('perform_track_user_assignment_via');

        // Adding fields to table perform_track_user_assignment_via.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('track_assignment_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('track_user_assignment_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_track_user_assignment_via.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('track_assignment_id', XMLDB_KEY_FOREIGN, array('track_assignment_id'), 'perform_track_assignment', array('id'), 'cascade');
        $table->add_key('track_user_assignment_id', XMLDB_KEY_FOREIGN, array('track_user_assignment_id'), 'perform_track_user_assignment', array('id'), 'cascade');

        // Adding indexes to table perform_track_user_assignment_via.
        $table->add_index('unique_ids', XMLDB_INDEX_UNIQUE, array('track_assignment_id', 'track_user_assignment_id'));

        // Conditionally launch create table for perform_track_user_assignment_via.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030500, 'perform');
    }

    if ($oldversion < 2020030501) {
        // Define field track_user_assignment_id to be added to perform_subject_instance.
        $table = new xmldb_table('perform_subject_instance');
        $field = new xmldb_field('track_user_assignment_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field track_user_assignment_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'subject_user_id');

        // Conditionally launch add field created_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'created_at');

        // Conditionally launch add field updated_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key('track_user_assignment_id', XMLDB_KEY_FOREIGN, array('track_user_assignment_id'), 'perform_track_user_assignment', array('id'), 'cascade');

        // Launch add key track_user_assignment_id.
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030501, 'perform');
    }

    if ($oldversion < 2020030600) {
        // Add field perform.created_at
        $table = new xmldb_table('perform');
        $field = new xmldb_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Change of nullability for field perform.updated_at.
        $field = new xmldb_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $dbman->change_field_notnull($table, $field);

        // Add field perform_section.created_at
        $field = new xmldb_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Add field perform_section.updated_at
        $field = new xmldb_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Drop classname index.
        $index = new xmldb_index('activity_id_classname', XMLDB_INDEX_UNIQUE, ['activity_id', 'classname']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Rename perform_relationship.classname to class_name
        $table = new xmldb_table('perform_relationship');
        $field = new xmldb_field('classname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'class_name');
        }

        // Re-add index with proper names
        $index = new xmldb_index('activity_id_class_name', XMLDB_INDEX_UNIQUE, ['activity_id', 'class_name']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add field perform_section_relationship.created_at
        $table = new xmldb_table('perform_section_relationship');
        $field = new xmldb_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030600, 'perform');
    }

    if ($oldversion < 2020030900) {

        // Define key perform_relationship_id (foreign) to be dropped form perform_participant_instance.
        $table = new xmldb_table('perform_participant_instance');
        $key = new xmldb_key('perform_relationship_id', XMLDB_KEY_FOREIGN, array('perform_relationship_id'), 'perform_relationship', array('id'));

        // Launch drop key perform_relationship_id.
        $dbman->drop_key($table, $key);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030900, 'perform');
    }

    if ($oldversion < 2020030901) {

        // Rename field perform_relationship_id on table perform_participant_instance to activity_relationship_id.
        $table = new xmldb_table('perform_participant_instance');
        $field = new xmldb_field('perform_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field perform_relationship_id.
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'activity_relationship_id');
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030901, 'perform');
    }

    if ($oldversion < 2020030902) {

        // Define key activity_relationship_id (foreign) to be added to perform_participant_instance.
        $table = new xmldb_table('perform_participant_instance');
        $key = new xmldb_key('activity_relationship_id', XMLDB_KEY_FOREIGN, array('activity_relationship_id'), 'perform_relationship', array('id'));

        // Launch add key activity_relationship_id.
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030902, 'perform');
    }

    if ($oldversion < 2020030903) {

        // Define field participant_source to be added to perform_participant_instance.
        $table = new xmldb_table('perform_participant_instance');

        $key = new xmldb_key('participant_id', XMLDB_KEY_FOREIGN, array('participant_id'), 'user', array('id'), 'cascade');

        // Launch add key participant_id.
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Add created_at
        $field = new xmldb_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'availability');

        // Conditionally launch add field created_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add updated_at
        $field = new xmldb_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'created_at');

        // Conditionally launch add field updated_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table perform_participant_section to be created.
        $table = new xmldb_table('perform_participant_section');

        // Adding fields to table perform_participant_section.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('participant_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_participant_section.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'cascade');
        $table->add_key('participant_instance_id', XMLDB_KEY_FOREIGN, array('participant_instance_id'), 'perform_participant_instance', array('id'), 'cascade');

        // Adding indexes to table perform_participant_section.
        $table->add_index('section_participant_instance', XMLDB_INDEX_UNIQUE, array('section_id', 'participant_instance_id'));

        // Conditionally launch create table for perform_participant_section.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030903, 'perform');
    }

    if ($oldversion < 2020030904) {
        // Define field context_id to be added to perform_element.
        $table = new xmldb_table('perform_element');
        $field = new xmldb_field('context_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field context_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index section_sort_order (unique) to be dropped form perform_section_element.
        $table = new xmldb_table('perform_section_element');
        $index = new xmldb_index('section_sort_order', XMLDB_INDEX_UNIQUE, array('section_id', 'sort_order'));

        // Conditionally launch drop index section_sort_order.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020030904, 'perform');
    }

    if ($oldversion < 2020031300) {
        // Change perform_track.status to integer
        $table = new xmldb_table('perform_track');
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0);
        $dbman->change_field_type($table, $field);

        $dbman->add_index($table, $index);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020031300, 'perform');
    }

    if ($oldversion < 2020031301) {
        // Change perform_track_assignment.type to integer
        $table = new xmldb_table('perform_track_assignment');
        $index = new xmldb_index(
            'track_id_type_group_type_group_id',
            XMLDB_INDEX_UNIQUE,
            ['track_id', 'type', 'user_group_type', 'user_group_id']
        );
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field('type', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0);
        $dbman->change_field_type($table, $field);

        $dbman->add_index($table, $index);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020031301, 'perform');
    }

    if ($oldversion < 2020031302) {
        // Change perform_track_assignment.user_group_type to integer
        $index_track_grp_type_id = new xmldb_index(
            'track_id_type_group_type_group_id',
            XMLDB_INDEX_UNIQUE,
            ['track_id', 'type', 'user_group_type', 'user_group_id']
        );
        if ($dbman->index_exists($table, $index_track_grp_type_id)) {
            $dbman->drop_index($table, $index_track_grp_type_id);
        }

        $index_grp_type = new xmldb_index('user_group_type', XMLDB_INDEX_NOTUNIQUE, ['user_group_type']);
        if ($dbman->index_exists($table, $index_grp_type)) {
            $dbman->drop_index($table, $index_grp_type);
        }

        $index_grp_type_id = new xmldb_index(
            'user_group_type_user_group_id',
            XMLDB_INDEX_NOTUNIQUE,
            ['user_group_type', 'user_group_id']
        );
        if ($dbman->index_exists($table, $index_grp_type_id)) {
            $dbman->drop_index($table, $index_grp_type_id);
        }

        $field = new xmldb_field('user_group_type', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0);
        $dbman->change_field_type($table, $field);

        $dbman->add_index($table, $index_grp_type_id);
        $dbman->add_index($table, $index_grp_type);
        $dbman->add_index($table, $index_track_grp_type_id);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020031302, 'perform');
    }

    if ($oldversion < 2020031700) {
        $DB->delete_records('perform_relationship');

        // Define index activity_id_class_name (unique) to be dropped form perform_relationship.
        $table = new xmldb_table('perform_relationship');
        $index = new xmldb_index('activity_id_class_name', XMLDB_INDEX_UNIQUE, array('activity_id', 'class_name'));

        // Conditionally launch drop index activity_id_class_name.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Rename field class_name on table perform_relationship to name.
        $table = new xmldb_table('perform_relationship');
        $field = new xmldb_field('class_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'activity_id');

        // Launch rename field class_name.
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'relationship_id');

            // Changing type of field relationship_id on table perform_relationship to int.
            $table = new xmldb_table('perform_relationship');
            $field = new xmldb_field('relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'activity_id');

            // Launch change of type for field relationship_id.
            $dbman->change_field_type($table, $field);

            // Changing precision of field relationship_id on table perform_relationship to (10).
            $table = new xmldb_table('perform_relationship');
            $field = new xmldb_field('relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'activity_id');

            // Launch change of precision for field relationship_id.
            $dbman->change_field_precision($table, $field);

            // Define key relationship_id (foreign) to be added to perform_relationship.
            $table = new xmldb_table('perform_relationship');
            $key = new xmldb_key('relationship_id', XMLDB_KEY_FOREIGN, array('relationship_id'), 'totara_core_relationship', array('id'), 'restrict');

            // Launch add key relationship_id.
            $dbman->add_key($table, $key);
        }

        // Define index activity_id_relationship_id (unique) to be added to perform_relationship.
        $table = new xmldb_table('perform_relationship');

        $field = new xmldb_field('relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'activity_id');
        $index = new xmldb_index('activity_id_relationship_id', XMLDB_INDEX_UNIQUE, array('activity_id', 'relationship_id'));

        // Conditionally launch add index activity_id_relationship_id.
        if ($dbman->field_exists($table, $field) && !$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020031700, 'perform');
    }

    if ($oldversion < 2020040701) {
        // Rename field relationship_id on table perform_relationship to core_relationship_id.
        $table = new xmldb_table('perform_relationship');
        $field = new xmldb_field('relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'activity_id');

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'core_relationship_id');
        }

        // Replace index.
        $old_index = new xmldb_index('activity_id_relationship_id', XMLDB_INDEX_UNIQUE, array('activity_id', 'relationship_id'));
        if ($dbman->index_exists($table, $old_index)) {
            $dbman->drop_index($table, $old_index);
        }
        $new_index = new xmldb_index('activity_id_core_relationship_id', XMLDB_INDEX_UNIQUE, array('activity_id', 'core_relationship_id'));
        if (!$dbman->index_exists($table, $new_index)) {
            $dbman->add_index($table, $new_index);
        }

        //Replace key
        $old_key = new xmldb_key('relationship_id', XMLDB_KEY_FOREIGN, array('relationship_id'), 'totara_core_relationship', array('id'), 'restrict');
        if ($dbman->key_exists($table, $old_key)) {
            $dbman->drop_key($table, $old_key);
        }
        $new_key = new xmldb_key('core_relationship_id', XMLDB_KEY_FOREIGN, array('core_relationship_id'), 'totara_core_relationship', array('id'), 'restrict');
        if (!$dbman->key_exists($table, $new_key)) {
            $dbman->add_key($table, $new_key);
        }
        upgrade_mod_savepoint(true, 2020040701, 'perform');
    }

    if ($oldversion < 2020040800) {
        // Rename field status on table perform_participant_section to progress.
        $table = new xmldb_table('perform_participant_section');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'participant_instance_id');

        // Launch rename field status.
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'progress');
        }

        // Rename field status on table perform_participant_instance to progress.
        $table = new xmldb_table('perform_participant_instance');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'subject_instance_id');

        // Launch rename field status.
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'progress');
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020040800, 'perform');
    }

    if ($oldversion < 2020042000) {
        // Create activity type table.
        $table = new xmldb_table('perform_type');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('is_system', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Populate canned activity types.
        mod_perform\util::create_activity_types();

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020042000, 'perform');
    }

    if ($oldversion < 2020042001) {
        // Create new activity_type column for activity table.
        $types = $DB->get_records('perform_type', null, 'id', 'id', 0, 1);
        $default_type = reset($types)->id;

        $table = new xmldb_table('perform');
        $field = new xmldb_field('type_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, $default_type, 'id');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            $type_key = new xmldb_key('type_id', XMLDB_KEY_FOREIGN, ['type_id'], 'perform_type', ['id'], 'restrict');
            $dbman->add_key($table, $type_key);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020042001, 'perform');
    }

    if ($oldversion < 2020042200) {
        // Define field progress to be added to perform_subject_instance.
        $table = new xmldb_table('perform_subject_instance');
        $field = new xmldb_field('progress', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'subject_user_id');

        // Conditionally launch add field progress.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020042200, 'perform');
    }

    if ($oldversion < 2020042900) {
        // Drop name index on perform_type if it exists.
        $table = new xmldb_table('perform_type');
        $index = new xmldb_index('perform_type_name', XMLDB_INDEX_UNIQUE, ['name']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020042900, 'perform');
    }

    if ($oldversion < 2020042901) {
        // Define field availability to be added.
        $field = new xmldb_field('availability', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $tables = [
            'perform_subject_instance',
            'perform_participant_instance',
            'perform_participant_section',
        ];

        foreach ($tables as $table) {
            $table_to_update = new xmldb_table($table);
            if (!$dbman->field_exists($table_to_update, $field)) {
                $dbman->add_field($table_to_update, $field);
            }
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020042901, 'perform');
    }

    if ($oldversion < 2020043000) {
        // Define field schedule_type to be added to perform_track.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_type', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'status');

        // Conditionally launch add field schedule_type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020043000, 'perform');
    }

    if ($oldversion < 2020050500) {

        // Define field close_on_completion to be added to perform.
        $table = new xmldb_table('perform');
        $field = new xmldb_field('close_on_completion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'status', [0, 1]);

        // Conditionally launch add field close_on_completion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020050500, 'perform');
    }

    if ($oldversion < 2020051100) {
        $table = new xmldb_table('perform_track');

        // Define field schedule_fixed_from to be added to perform_track.
        $field = new xmldb_field('schedule_fixed_from', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'schedule_type');

        // Conditionally launch add field schedule_fixed_from.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field schedule_fixed_to to be added to perform_track.
        $field = new xmldb_field('schedule_fixed_to', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'schedule_fixed_from');

        // Conditionally launch add field schedule_fixed_to.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020051100, 'perform');
    }

    if ($oldversion < 2020051300) {

        // Changing precision of field name on table perform to (1024).
        $table = new xmldb_table('perform');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null, 'course');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020051300, 'perform');
    }

    if ($oldversion < 2020051400) {

        // Define field period_start_date to be added to perform_track_user_assignment.
        $table = new xmldb_table('perform_track_user_assignment');
        $field = new xmldb_field('period_start_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'subject_user_id');

        // Conditionally launch add field period_start_date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field period_end_date to be added to perform_track_user_assignment.
        $table = new xmldb_table('perform_track_user_assignment');
        $field = new xmldb_field('period_end_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'period_start_date');

        // Conditionally launch add field period_end_date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020051400, 'perform');
    }

    if ($oldversion < 2020051800) {
        $table = new xmldb_table('perform_track');

        // Define field schedule_is_open to be added to perform_track.
        $field = new xmldb_field('schedule_is_open', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, 'status');

        // Conditionally launch add field schedule_is_open.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field schedule_is_fixed to be added to perform_track.
        $field = new xmldb_field('schedule_is_fixed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, 'schedule_is_open');

        // Conditionally launch add field schedule_is_fixed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field schedule_type to be dropped from perform_track.
        $field = new xmldb_field('schedule_type');

        // Conditionally launch drop field schedule_type.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020051800, 'perform');
    }
    if ($oldversion < 2020052000) {
        // Define field schedule_dynamic_count_from to be added to perform_track.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_dynamic_count_from', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'schedule_fixed_to');

        // Conditionally launch add field schedule_dynamic_count_from.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('schedule_dynamic_count_to', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'schedule_dynamic_count_from');

        // Conditionally launch add field schedule_dynamic_count_to.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('schedule_dynamic_unit', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'schedule_dynamic_count_to');

        // Conditionally launch add field schedule_dynamic_unit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('schedule_dynamic_direction', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'schedule_dynamic_unit');

        // Conditionally launch add field schedule_dynamic_direction.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2020052000, 'perform');
    }
    if ($oldversion < 2020052001) {
        $table = new xmldb_table('perform_track');

        // Define field due_date_is_enabled to be added to perform_track.
        $field = new xmldb_field('due_date_is_enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'schedule_dynamic_direction');

        // Conditionally launch add field due_date_is_enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052001, 'perform');

    }
    if ($oldversion < 2020052200) {
        // Define field is_required to be added to perform_element.
        $table = new xmldb_table('perform_element');

        $field = new xmldb_field('is_required', XMLDB_TYPE_INTEGER, '1', null, null, null, null);

        // Conditionally launch add field is_required.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2020052200, 'perform');
    }


    if ($oldversion < 2020052500) {

        // Define field schedule_needs_sync to be added to perform_track.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_needs_sync', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'schedule_dynamic_direction');

        // Conditionally launch add field schedule_needs_sync.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2020052500, 'perform');
    }

    if ($oldversion < 2020052600) {

        $table = new xmldb_table('perform_section_relationship');
        // Replave section_id key on delete.
        $old_key = new xmldb_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'cascade');
        if ($dbman->key_exists($table, $old_key)) {
            $dbman->drop_key($table, $old_key);
        }
        $new_key = new xmldb_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'restrict');
        if (!$dbman->key_exists($table, $new_key)) {
            $dbman->add_key($table, $new_key);
        }

        $table = new xmldb_table('perform_participant_section');
        // Replave section_id key on delete.
        $old_key = new xmldb_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'cascade');
        if ($dbman->key_exists($table, $old_key)) {
            $dbman->drop_key($table, $old_key);
        }
        $new_key = new xmldb_key('section_id', XMLDB_KEY_FOREIGN, array('section_id'), 'perform_section', array('id'), 'restrict');
        if (!$dbman->key_exists($table, $new_key)) {
            $dbman->add_key($table, $new_key);
        }

        // Replace participant_instance_id key on delete.
        $old_key = new xmldb_key('participant_instance_id', XMLDB_KEY_FOREIGN, array('participant_instance_id'), 'perform_participant_instance', array('id'), 'cascade');
        if ($dbman->key_exists($table, $old_key)) {
            $dbman->drop_key($table, $old_key);
        }
        $new_key = new xmldb_key('participant_instance_id', XMLDB_KEY_FOREIGN, array('participant_instance_id'), 'perform_participant_instance', array('id'), 'restrict');
        if (!$dbman->key_exists($table, $new_key)) {
            $dbman->add_key($table, $new_key);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052600, 'perform');
    }
    if ($oldversion < 2020052700) {
        // Define field repeating_is_enabled to be added to perform_track.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('repeating_is_enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'due_date_is_enabled');

        // Conditionally launch add field repeating_is_enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052700, 'perform');
    }

    if ($oldversion < 2020052800) {
        $table = new xmldb_table('perform_track');

        // Define field due_date_is_fixed to be added to perform_track.
        $field = new xmldb_field('due_date_is_fixed', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'due_date_is_enabled');

        // Conditionally launch add field due_date_is_fixed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field due_date_fixed to be added to perform_track.
        $field = new xmldb_field('due_date_fixed', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'due_date_is_fixed');

        // Conditionally launch add field due_date_fixed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field due_date_relative_count to be added to perform_track.
        $field = new xmldb_field('due_date_relative_count', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'due_date_fixed');

        // Conditionally launch add field due_date_relative_count.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field due_date_relative_unit to be added to perform_track.
        $field = new xmldb_field('due_date_relative_unit', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'due_date_relative_count');

        // Conditionally launch add field due_date_relative_unit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052800, 'perform');
    }

    if ($oldversion < 2020052900) {
        // Define new perform_setting table.
        $table = new xmldb_table('perform_setting');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name',XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value',XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_track.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('activity_id', XMLDB_KEY_FOREIGN, ['activity_id'], 'perform', ['id'], 'cascade');

        // Conditionally launch create table.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $index = new xmldb_index('activity_setting_name', XMLDB_INDEX_UNIQUE, array('activity_id', 'name'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052900, 'perform');
    }

    if ($oldversion < 2020052901) {
        // Remove field close_on_completion on activity table.
        $table = new xmldb_table('perform');
        $field = new xmldb_field('close_on_completion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'status', [0, 1]);

        if ($dbman->field_exists($table, $field)) {
            // Transfer the close on completion setting to the new setting table first.
            foreach ($DB->get_records('perform') as $activity) {
                $setting_record = [
                    'activity_id' => $activity->id,
                    'name' => 'close_on_completion',
                    'value' => $activity->close_on_completion,
                    'created_at' => time()
                ];

                $DB->insert_record('perform_setting', $setting_record);
            }

            $dbman->drop_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020052901, 'perform');
    }

    return true;
}
