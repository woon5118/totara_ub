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
 * @package mod_workshop
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_mod_workshop_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('mod', 'workshop');

    if ($version > 2019111800) {
        throw new coding_exception("Invalid plugin (mod_workshop) version ($version) for pre-migration");
    }

    // Moodle 3.8 pre-migration line.

    // Moodle 3.7 pre-migration line.

    if ($version >= 2018062600) {
        $table = new xmldb_table('workshop');
        $field = new xmldb_field('nattachments', XMLDB_TYPE_INTEGER, '3', null, null, null, '0');
        $dbman->change_field_default($table, $field);

        $table = new xmldb_table('workshop');
        $field = new xmldb_field('submissiontypefile', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('workshop');
        $field = new xmldb_field('submissiontypetext', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2018062500, 'mod', 'workshop');
    }

    // Moodle 3.6 pre-migration line.

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111301) {
        $version = premigrate_plugin_savepoint(2017111301, 'mod', 'workshop');
    }
}