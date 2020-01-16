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
 * @package mod_forum
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_mod_forum_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('mod', 'forum');

    if ($version > 2019052000) {
        throw new coding_exception("Invalid plugin (mod_forum) version ($version) for pre-migration");
    }

    if ($version >= 2019040402) {
        $table = new xmldb_table('forum_discussions');
        $field = new xmldb_field('timelocked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'pinned');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2019040401, 'mod', 'forum');
    }

    if ($version >= 2019040400) {
        $table = new xmldb_table('forum');
        $field = new xmldb_field('duedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'duedate');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2019040300, 'mod', 'forum');
    }

    if ($version >= 2019031200) {
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('privatereplyto', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'mailnow');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2019031100, 'mod', 'forum');
    }

    // Moodle 3.7 pre-migration line.

    // Moodle 3.6 pre-migration line.

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111301) {
        $version = premigrate_plugin_savepoint(2017111301, 'mod', 'forum');
    }
}