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
 * @package mod_lti
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_mod_lti_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('mod', 'lti');

    if ($version > 2019111800) {
        throw new coding_exception("Invalid plugin (mod_lti) version ($version) for pre-migration");
    }

    // Moodle 3.8 pre-migration line.

    if ($version >= 2019031302) {
        $table = new xmldb_table('lti_tool_settings');
        $field = new xmldb_field('typeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'toolproxyid');
        if ($dbman->field_exists($table, $field)) {
            $key = new xmldb_key('typeid', XMLDB_KEY_FOREIGN, ['typeid'], 'lti_types', ['id']);
            $dbman->drop_key($table, $key);
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2019031301, 'mod', 'lti');
    }

    if ($version >= 2019031301) {
        $table = new xmldb_table('lti_access_tokens');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_plugin_savepoint(2019031300, 'mod', 'lti');
    }

    if ($version >= 2019031300) {
        $table = new xmldb_table('lti_types');
        $field = new xmldb_field('ltiversion', XMLDB_TYPE_CHAR, 10, null, XMLDB_NOTNULL, null, null, 'coursevisible');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('lti_types');
        $field = new xmldb_field('clientid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'ltiversion');
        if ($dbman->field_exists($table, $field)) {
            $index = new xmldb_index('clientid', XMLDB_INDEX_UNIQUE, array('clientid'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2019031200, 'mod', 'lti');
    }

    // Moodle 3.7 pre-migration line.

    // Moodle 3.6 pre-migration line.

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111301) {
        $version = premigrate_plugin_savepoint(2017111301, 'mod', 'lti');
    }
}