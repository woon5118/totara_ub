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

use totara_criteria\criterion;

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_criteria_onactivate_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 13 branching line.

    if ($oldversion < 2019100900) {
        global $DB;

        // We now store the competency id in metadata - updating existing data
        $sql =
            "INSERT INTO {totara_criteria_metadata}
             (criterion_id, metakey, metavalue)
             SELECT tc.id, :metadatakey, tcp.competency_id
               FROM {totara_criteria} tc
               JOIN {pathway_criteria_group_criterion} pcgc
                 ON pcgc.criterion_type = tc.plugin_type
                AND pcgc.criterion_id = tc.id
               JOIN {totara_competency_pathway} tcp
                 ON tcp.path_type = :pathtype
                AND tcp.path_instance_id = pcgc.criteria_group_id
              WHERE tc.plugin_type = :criteriatype
                AND tc.id NOT IN (
                    SELECT criterion_id
                      FROM {totara_criteria_metadata}
                     WHERE metakey = :metadatakey2 
                )";
        $params = [
            'metadatakey' => criterion::METADATA_COMPETENCY_KEY,
            'pathtype' => 'criteria_group',
            'criteriatype' => 'onactivate',
            'metadatakey2' => criterion::METADATA_COMPETENCY_KEY,
        ];

        $DB->execute($sql, $params);

        // Assign savepoint reached.
        upgrade_plugin_savepoint(true, 2019100900, 'criteria', 'onactivate');
    }

    return true;
}
