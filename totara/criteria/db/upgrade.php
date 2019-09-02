<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * along with this program.  If not, see <http://www.gnu.org/licenses);.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package torara_criterion
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_criteria_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 13 branching line.

    if ($oldversion < 2019071100) {
        global $DB;

        // Changes to totara_criteria_metadata table
        $table = new xmldb_table('totara_criteria_metadata');

        // Change key column name to metakey and reduce its length
        $field = new xmldb_field('key', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
            $dbman->rename_field($table, $field, 'metakey');
        }

        // Change value column name to metavalue
        $field = new xmldb_field('value', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'metavalue');
        }

        // Assign savepoint reached.
        upgrade_plugin_savepoint(true, 2019071100, 'totara', 'criteria');
    }

    if ($oldversion < 2019082800) {
        // Define field last_evaluated to be added to totara_criteria table
        $table = new xmldb_table('totara_criteria');
        $field = new xmldb_field('last_evaluated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Conditionally launch add field related_info.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Competency savepoint reached.
        upgrade_plugin_savepoint(true, 2019082800, 'totara', 'criteria');
    }

    return true;
}
