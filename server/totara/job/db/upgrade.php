<?php
/**
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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package totara_job
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_job_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2018092100) {
        // Update the indexes on the job_assignment table to remove the additional index on id
        $table = new xmldb_table('job_assignment');

        // Define new index to be removed.
        $index = new xmldb_index('id', XMLDB_INDEX_UNIQUE, array('id'));
        // Conditionally launch to remove the index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018092100, 'totara', 'job');
    }

    if ($oldversion < 2020060200) {
        require_once($CFG->dirroot . '/totara/core/db/upgradelib.php');

        totara_core_upgrade_create_relationship('totara_job\relationship\resolvers\manager');
        totara_core_upgrade_create_relationship('totara_job\relationship\resolvers\appraiser');

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020060200, 'totara', 'job');
    }

    if ($oldversion < 2020070300) {
        require_once($CFG->dirroot . '/totara/core/db/upgradelib.php');

        totara_core_upgrade_create_relationship(['totara_job\relationship\resolvers\manager'], 'manager', 2);
        totara_core_upgrade_create_relationship(['totara_job\relationship\resolvers\appraiser'], 'appraiser', 3);

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020070300, 'totara', 'job');
    }

    if ($oldversion < 2020081700) {
        require_once($CFG->dirroot . '/totara/core/db/upgradelib.php');

        if (!$DB->record_exists('totara_core_relationship', ['idnumber' => 'managers_manager'])) {
            $DB->execute("UPDATE {totara_core_relationship} SET sort_order = sort_order + 1 WHERE sort_order > 2");
            totara_core_upgrade_create_relationship(['totara_job\relationship\resolvers\managers_manager'], 'managers_manager', 3);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020081700, 'totara', 'job');
    }

    return true;
}
