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
 * @package assignfeedback_editpdf
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_assignfeedback_editpdf_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('assignfeedback', 'editpdf');

    if ($version > 2019052000) {
        throw new coding_exception("Invalid plugin (assignfeedback_editpdf) version ($version) for pre-migration");
    }

    if ($version >= 2019010800) {
        // Define table assignfeedback_editpdf_rot to be created.
        $table = new xmldb_table('assignfeedback_editpdf_rot');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_plugin_savepoint(2019010700, 'assignfeedback', 'editpdf');
    }

    // Moodle 3.7 pre-migration line.

    // Moodle 3.6 pre-migration line.

    // Reverse 2018051401 changes from MDL-63891.
    if ($version >= 2018051401) {
        $table = new xmldb_table('assignfeedback_editpdf_queue');
        $key = new xmldb_key('submissionid-submissionattempt', XMLDB_KEY_UNIQUE, ['submissionid', 'submissionattempt']);
        $dbman->drop_key($table, $key);

        $table = new xmldb_table('assignfeedback_editpdf_queue');
        $field = new xmldb_field('attemptedconversions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'submissionattempt');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2018051400, 'assignfeedback', 'editpdf');
    }

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111300) {
        $version = premigrate_plugin_savepoint(2017111300, 'assignfeedback', 'editpdf');
    }
}