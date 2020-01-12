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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package core
 */

/**
 * Transforms Moodle installation to Moodle 3.4.9 data format.
 *
 * @param int|float $oldversion
 */
function xmldb_core_premigrate() {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $version = $CFG->version;

    if ($version >= 2018040500.01) {
        $table = new xmldb_table('cohort');
        $field = new xmldb_field('theme', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'timemodified');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018040500.00);
    }

    if ($version >= 2018032200.05) {
        $table = new xmldb_table('message_working');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unreadmessageid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('processorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('unreadmessageid_idx', XMLDB_INDEX_NOTUNIQUE, array('unreadmessageid'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $version = premigrate_main_savepoint(2018032200.04);
    }

    if ($version >= 2018032200.01) {
        $table = new xmldb_table('messages');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_conversations');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_conversation_members');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_user_actions');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('notifications');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2018032200.00);
    }

    if ($version >= 2018022800.03) {
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('multiplecontexts', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'showstandard');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018022800.02);
    }

    if ($version >= 2018022800.02) {
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'contextid', 'tiuserid', 'tagid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Get rid of multiple tags.
        $sql = "SELECT MIN(id) AS id, COUNT(id) AS duplicates, component, itemtype, itemid, tiuserid, tagid
                  FROM {tag_instance}
              GROUP BY component, itemtype, itemid, tiuserid, tagid
                HAVING COUNT(id) > 1";
        $instances = $DB->get_records_sql($sql);
        foreach ($instances as $instance) {
            $select = "id <> :id AND duplicates = :duplicates AMD component = :component
                       AND itemtype = :itemtype AND itemid = :itemid AND tiuserid = :tiuserid AND tagid = :tagid";
            $DB->delete_records_select('tag_instance', $select, (array)$instance);
        }

        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'tiuserid', 'tagid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $version = premigrate_main_savepoint(2018022800.01);
    }

    if ($version >= 2017122200.01) {
        $table = new xmldb_table('search_index_requests');
        $index = new xmldb_index('indexprioritytimerequested', XMLDB_INDEX_NOTUNIQUE, array('indexpriority', 'timerequested'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('search_index_requests');
        $field = new xmldb_field('indexpriority', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'partialtime');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2017122200.00);
    }

    if ($version >= 2017121900.00) {
        $table = new xmldb_table('role_allow_view');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2017121800.00);
    }

    // This site is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111309.00) {
        $version = premigrate_main_savepoint(2017111309.00);
    }
    set_config('release', '3.4.9 (Build: 20190513)');
}
