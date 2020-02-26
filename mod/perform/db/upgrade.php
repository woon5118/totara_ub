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

    return true;
}

