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

    if ($oldversion < 2020061500) {
        throw new upgrade_exception('mod_perform', '2020061500', 'Cannot upgrade from an earlier version - do a fresh install instead');
    }

    if ($oldversion < 2020061800) {

        // Define field completed_at to be added to perform_subject_instance.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_dynamic_source', XMLDB_TYPE_TEXT, null, null, null, null, null, 'schedule_dynamic_direction');

        // Conditionally launch add field completed_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020061800, 'perform');
    }

    if ($oldversion < 2020061801) {

        // Define field completed_at to be added to perform_subject_instance.
        $table = new xmldb_table('perform_track');
        $field = new xmldb_field('schedule_use_anniversary', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'schedule_dynamic_source');

        // Conditionally launch add field completed_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020061801, 'perform');
    }

    if ($oldversion < 2020061900) {

        // Define table perform_notification to be created.
        $table = new xmldb_table('perform_notification');

        // Adding fields to table perform_notification.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('class_key', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('triggers', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('last_run_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_notification.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activity_fk', XMLDB_KEY_FOREIGN, array('activity_id'), 'perform', array('id'), 'cascade');

        // Adding indexes to table perform_notification.
        $table->add_index('notification_ix', XMLDB_INDEX_UNIQUE, array('activity_id', 'class_key'));

        // Conditionally launch create table for perform_notification.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_notification_recipient to be created.
        $table = new xmldb_table('perform_notification_recipient');

        // Adding fields to table perform_notification_recipient.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('notification_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('section_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table perform_notification_recipient.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('notification_fk', XMLDB_KEY_FOREIGN, array('notification_id'), 'perform_notification', array('id'), 'cascade');
        $table->add_key('section_relationship_fk', XMLDB_KEY_FOREIGN, array('section_relationship_id'), 'perform_section_relationship', array('id'), 'cascade');

        // Adding indexes to table perform_notification_recipient.
        $table->add_index('notification_recipient_ix', XMLDB_INDEX_UNIQUE, array('notification_id', 'section_relationship_id'));

        // Conditionally launch create table for perform_notification_recipient.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table perform_notification_message to be created.
        $table = new xmldb_table('perform_notification_message');

        // Adding fields to table perform_notification_message.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sent_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('notification_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('core_relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table perform_notification_message.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('notification_fk', XMLDB_KEY_FOREIGN, array('notification_id'), 'perform_notification', array('id'), 'cascade');
        $table->add_key('core_relationship_fk', XMLDB_KEY_FOREIGN, array('core_relationship_id'), 'totara_core_relationship', array('id'), 'cascade');

        // Conditionally launch create table for perform_notification_message.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2020061900, 'perform');
    }

    return true;
}
