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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\task;

use core\task\scheduled_task;
use totara_competency\achievement_configuration;
use totara_competency\competency_achievement_aggregator;
use totara_competency\pathway;
use totara_competency\entities\competency;

/**
 * Class competency_achievement_aggregation
 *
 * Aggregates values for users competency achievements based on their pathway achievement values.
 */
class competency_achievement_aggregation extends scheduled_task {

    public function get_name() {
        return get_string('updatecompachievements', 'totara_competency');
    }

    public function execute() {
        $competencies = competency::repository()->get();
        foreach ($competencies as $competency) {
            $users = $this->get_assigned_users_with_updated_achievements($competency);

            if (count($users) === 0) {
                continue;
            }

            $configuration = new achievement_configuration($competency);
            $aggregator = new competency_achievement_aggregator($configuration);
            $aggregator->aggregate($users);
        }
    }

    /**
     * Gets the user ids that may need to be aggregated based updates of achievement values.
     *
     * This is based on pathway achievement changes and not on changes to the configuration of pathways themselves. Nor
     * does it account for changes to the aggregation method. Reaggregating following changes of settings should be
     * done separately as there are too many variables involved.
     *
     * For performance, this method avoids selecting too many users by comparing the last aggregation time of
     * the user's current record against the last aggregation time of the user's pathway achievements. But there
     * are still specific circumstances where more users are returned than necessary so that changes are not missed.
     *
     * @param competency $competency
     * @return array of user ids.
     */
    public function get_assigned_users_with_updated_achievements(competency $competency) {
        global $DB;

        // Important features of this query.
        // 1. It does not check the status of the pathway achievement. We want all as an achievement going from
        //    active to archived could mean a user no longer has a value that they used to have.
        // 2. We check if the pathway achievement aggregated time is greater than **or equal** to the comp record
        //    aggregated time. This is intentional. It may mean some doubling up of processing but allows for a
        //    an update to a pathway achievement that happens after an aggregation but before the next second in
        //    the timestamp. If we don't do this, then those edge cases mean the change will never be caught.
        $sql = "
           SELECT DISTINCT tacu.user_id
             FROM {totara_competency_assignment_users} tacu
             JOIN {totara_competency_pathway_achievement} cupa
               ON tacu.user_id = cupa.user_id
             JOIN {totara_competency_pathway} cp
               ON cupa.pathway_id = cp.id
              AND tacu.competency_id = cp.comp_id
        LEFT JOIN {totara_competency_achievement} ca
               ON ca.comp_id = cp.comp_id
            WHERE cp.comp_id = :comp_id
              AND cp.status = :active
              AND (cupa.last_aggregated >= ca.last_aggregated OR ca.id IS NULL)
        ";

        return $DB->get_fieldset_sql($sql, ['comp_id' => $competency->id, 'active' => pathway::PATHWAY_STATUS_ACTIVE]);
    }
}