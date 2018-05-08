<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @package tool_sitepolicy
 */

/**
 * Upgrade script for tool_sitepolicy.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_sitepolicy_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Totara 11 branching line.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018050800) {
        // Add format fields for policytext and whatsnew.
        $table = new xmldb_table('tool_sitepolicy_localised_policy');
        $field = new xmldb_field('policytextformat', XMLDB_TYPE_INTEGER, '2', null, null, null, '1', 'policytext');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('whatsnewformat', XMLDB_TYPE_INTEGER, '2', null, null, null, '1', 'whatsnew');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $sql =
            "UPDATE {tool_sitepolicy_localised_policy}
                SET policytextformat = :policytextformat,
                    whatsnewformat = :whatsnewformat";
        $params = ['policytextformat' => FORMAT_PLAIN, 'whatsnewformat' => FORMAT_PLAIN];
        $DB->execute($sql, $params);

        // Connect savepoint reached.
        upgrade_plugin_savepoint(true, 2018050800, 'tool', 'sitepolicy');
    }

    return true;
}
