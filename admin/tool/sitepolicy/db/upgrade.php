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

require_once($CFG->dirroot . '/admin/tool/sitepolicy/db/upgradelib.php');

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

    if ($oldversion < 2018050200) {
        // Add format fields for policytext and whatsnew.
        $table = new xmldb_table('tool_sitepolicy_localised_policy');

        $field = new xmldb_field('policytextformat', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'policytext');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('whatsnewformat', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'whatsnew');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        tool_sitepolicy_upgrade_convert_policytext_to_html();

        // Connect savepoint reached.
        upgrade_plugin_savepoint(true, 2018050200, 'tool', 'sitepolicy');
    }

    return true;
}
