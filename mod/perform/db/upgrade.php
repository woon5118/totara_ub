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

    return true;
}
