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
 * @package torara_criteria
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_pathway_criteria_group_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 13 branching line.

    // TODO Remove all upgrade before we release
    if ($oldversion < 2019071101) {
        // Define field aggregation_method to be dropped from pathway_criteria_group.
        $table = new xmldb_table('pathway_criteria_group');

        $field = new xmldb_field('aggregation_method');

        // Conditionally launch drop field aggregation_method.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('aggregation_params');

        // Conditionally launch drop field aggregation_params.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2019071101, 'pathway', 'criteria_group');
    }

    return true;
}