<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

/**
 * Database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 */
function xmldb_totara_hierarchy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2016120100) {
        // There was a bug whereby sort orders could end up with duplicates, and gaps.
        // Although this will fix itself after the user gets the first error, we don't want them to get
        // any errors so we will fix the sortorders of all hierarchy custom fields now, during upgrade.
        // The function isn't particularly well performing, however we don't expect to encounter sites
        // with thousands of custom fields per type, as such we will raise memory as a caution as proceed.
        raise_memory_limit(MEMORY_HUGE);
        require_once($CFG->dirroot.'/totara/hierarchy/db/upgradelib.php');

        totara_hierarchy_upgrade_fix_customfield_sortorder('comp_type'); // Competencies.
        totara_hierarchy_upgrade_fix_customfield_sortorder('goal_type'); // Company goals.
        totara_hierarchy_upgrade_fix_customfield_sortorder('goal_user'); // User goals.
        totara_hierarchy_upgrade_fix_customfield_sortorder('org_type'); // Organisations.
        totara_hierarchy_upgrade_fix_customfield_sortorder('pos_type'); // Positions.

        upgrade_plugin_savepoint(true, 2016120100, 'totara', 'hierarchy');
    }

    if ($oldversion < 2017072700) {
        // The timeproficient field will be a manually set completion date/time for competencies.
        $field = new xmldb_field('timeproficient', XMLDB_TYPE_INTEGER, '18', null, null, null, null, 'proficiency');

        // Add timeproficient to comp_record table.
        $table = new xmldb_table('comp_record');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add timeproficient to comp_record_history table.
        $table = new xmldb_table('comp_record_history');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2017072700, 'totara', 'hierarchy');
    }

    return true;
}
