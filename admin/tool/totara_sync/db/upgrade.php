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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package totara
 * @subpackage cohort
 */

require_once($CFG->dirroot . '/admin/tool/totara_sync/db/upgradelib.php');

/**
 * DB upgrades for Totara Sync
 */

function xmldb_tool_totara_sync_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    // TL-12312 Rename the setting which controls whether an import has previously linked on job assignment id number and
    // make sure that linkjobassignmentidnumber is enabled if it has previously linked on job assignment id number.
    if ($oldversion < 2016122300) {
        tool_totara_sync_upgrade_link_job_assignment_mismatch();

        upgrade_plugin_savepoint(true, 2016122300, 'tool', 'totara_sync');
    }

    // Set default for new 'sourceallrecords' setting for Organisations and Positions.
    if ($oldversion < 2017060800) {
        // Set source all records to 1 to preserve current behaviour in upgrades.
        set_config('sourceallrecords', '1', 'totara_sync_element_pos');
        set_config('sourceallrecords', '1', 'totara_sync_element_org');

        upgrade_plugin_savepoint(true, 2017060800, 'tool', 'totara_sync');
    }

    if ($oldversion < 2017081600) {
        $previouslylinkedonjobassignmentidnumber = get_config('totara_sync_element_user', 'previouslylinkedonjobassignmentidnumber');
        set_config('previouslylinkedonjobassignmentidnumber', $previouslylinkedonjobassignmentidnumber, 'totara_sync_element_jobassignment');
        $linkjobassignmentidnumber = get_config('totara_sync_element_user', 'linkjobassignmentidnumber');
        set_config('updateidnumbers', !$linkjobassignmentidnumber, 'totara_sync_element_jobassignment');
        unset_config('previouslylinkedonjobassignmentidnumber', 'totara_sync_element_user');
        unset_config('linkjobassignmentidnumber', 'totara_sync_element_user');

        upgrade_plugin_savepoint(true, 2017081600, 'tool', 'totara_sync');
    }

    return true;
}
