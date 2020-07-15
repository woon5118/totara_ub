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

    $dbman = $DB->get_manager();

    if ($oldversion < 2020063000) {
        throw new upgrade_exception('mod_perform', '2020063000', 'Cannot upgrade from an earlier version - do a fresh install instead');
    }

    if ($oldversion < 2020063001) {

        // Changing type of field identifier on table perform_element to char.
        $table = new xmldb_table('perform_element');
        $field = new xmldb_field('identifier', XMLDB_TYPE_CHAR, '1024', null, true, null, null, 'title');

        // Launch change of type for field identifier.
        $dbman->change_field_type($table, $field);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020063001, 'perform');
    }

    if ($oldversion < 2020070100) {
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_fixed_timezone',  XMLDB_TYPE_CHAR, '255',  null, null, null, null, 'schedule_fixed_to');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('due_date_fixed_timezone',  XMLDB_TYPE_CHAR, '255',  null, null, null, null, 'due_date_fixed');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2020070100, 'perform');
    }

    if ($oldversion < 2020070900) {
        $table = new xmldb_table('perform');
        $field = new xmldb_field('anonymous_responses',  XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'status', [0, 1]);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2020070900, 'perform');
    }

    if ($oldversion < 2020071000) {
        $DB->delete_records('perform_notification_recipient');

        // Define field core_relationship_id to be added to perform_notification_recipient.
        $table = new xmldb_table('perform_notification_recipient');

        $index = new xmldb_index('notification_recipient_ix', XMLDB_INDEX_UNIQUE, array('notification_id', 'section_relationship_id'));
        // Conditionally launch drop index notification_recipient_ix.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $key = new xmldb_key('section_relationship_fk', XMLDB_KEY_FOREIGN, array('section_relationship_id'), 'perform_section_relationship', array('id'), 'cascade');
        // Launch drop key section_relationship_fk.
        $dbman->drop_key($table, $key);

        $field = new xmldb_field('section_relationship_id');
        // Conditionally launch drop field section_relationship_id.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('core_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'notification_id');
        // Conditionally launch add field core_relationship_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2020071000, 'perform');
    }

    if ($oldversion < 2020072301) {
        require_once($CFG->dirroot . '/totara/core/db/upgradelib.php');

        totara_core_upgrade_create_relationship('mod_perform\relationship\resolvers\peer', 'perform_peer', 1, 'mod_perform');
        totara_core_upgrade_create_relationship('mod_perform\relationship\resolvers\mentor', 'perform_mentor', 1, 'mod_perform');
        totara_core_upgrade_create_relationship('mod_perform\relationship\resolvers\reviewer', 'perform_reviewer', 1, 'mod_perform');

        // Core savepoint reached.
        upgrade_mod_savepoint(true, 2020072301, 'perform');
    }

    if ($oldversion < 2020072302) {
        $table = new xmldb_table('perform_subject_instance');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'due_date');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table perform_manual_relation_selection to be created.
        $table = new xmldb_table('perform_manual_relation_selection');

        // Adding fields to table perform_manual_relation_selection.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('manual_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('selector_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_manual_relation_selection.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activity_id', XMLDB_KEY_FOREIGN, array('activity_id'), 'perform', array('id'));
        $table->add_key('manual_relationship_id', XMLDB_KEY_FOREIGN, array('manual_relationship_id'), 'totara_core_relationship', array('id'));
        $table->add_key('selector_relationship_id', XMLDB_KEY_FOREIGN, array('selector_relationship_id'), 'totara_core_relationship', array('id'));

        // Adding indexes to table perform_manual_relation_selection.
        $table->add_index('participant_role_selector', XMLDB_INDEX_UNIQUE, array('activity_id', 'manual_relationship_id', 'selector_relationship_id'));

        // Conditionally launch create table for perform_manual_relation_selection.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_manual_relation_selection_progress to be created.
        $table = new xmldb_table('perform_manual_relation_selection_progress');

        // Adding fields to table perform_manual_relation_selection_progress.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('subject_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('manual_relation_selection_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_manual_relation_selection_progress.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('subject_instance_id', XMLDB_KEY_FOREIGN, array('subject_instance_id'), 'perform_subject_instance', array('id'), 'cascade');
        $table->add_key('manual_relation_selection_id', XMLDB_KEY_FOREIGN, array('manual_relation_selection_id'), 'perform_manual_relation_selection', array('id'), 'cascade');

        // Adding indexes to table perform_manual_relation_selection_progress.
        $table->add_index('subject_participant_role_selector', XMLDB_INDEX_UNIQUE, array('subject_instance_id', 'manual_relation_selection_id'));

        // Conditionally launch create table for perform_manual_relation_selection_progress.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_manual_relation_selector to be created.
        $table = new xmldb_table('perform_manual_relation_selector');

        // Adding fields to table perform_manual_relation_selector.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('manual_relation_select_progress_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_manual_relation_selector.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('manual_relation_select_progress_id', XMLDB_KEY_FOREIGN, array('manual_relation_select_progress_id'), 'perform_manual_relation_selection_progress', array('id'), 'cascade');
        $table->add_key('user_id', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));

        // Conditionally launch create table for perform_manual_relation_selector.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_subject_instance_manual_participant to be created.
        $table = new xmldb_table('perform_subject_instance_manual_participant');

        // Adding fields to table perform_subject_instance_manual_participant.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('subject_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('core_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_by', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_subject_instance_manual_participant.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('subject_instance_id', XMLDB_KEY_FOREIGN, array('subject_instance_id'), 'perform_subject_instance', array('id'), 'cascade');
        $table->add_key('core_relationship_id', XMLDB_KEY_FOREIGN, array('core_relationship_id'), 'totara_core_relationship', array('id'), 'cascade');
        $table->add_key('user_id', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));
        $table->add_key('created_by', XMLDB_KEY_FOREIGN, array('created_by'), 'user', array('id'));

        // Conditionally launch create table for perform_subject_instance_manual_participant.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2020072302, 'perform');
    }

    return true;
}
