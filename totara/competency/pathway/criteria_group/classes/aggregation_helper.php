<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_criteria_group
 */

namespace pathway_criteria_group;

use totara_competency\aggregation_users_table;
use totara_competency\pathway;

class aggregation_helper {

    /**
     * Add entries to totara_competency_aggregation_queue for each competency with a pathway with
     * any of the provided criteria
     *
     * @param array $criteria_ids
     * @param int|null $user_id
     */
    public static function mark_for_reaggregate_from_criteria(array $criteria_ids, ?int $user_id = null) {
        global $DB;

        if (empty($criteria_ids)) {
            return;
        }

        $aggregation_table = new aggregation_users_table();

        // TODO: For now inserting one by one. Find better way if user_id is null

        [$criteria_id_sql, $params] = $DB->get_in_or_equal($criteria_ids, SQL_PARAMS_NAMED);

        $sql = "SELECT DISTINCT tacu.id,
                       tcp.comp_id, 
                       tacu.user_id
                  FROM {totara_competency_assignment_users} tacu
                  JOIN {totara_competency_pathway} tcp
                    ON tcp.comp_id = tacu.competency_id
                  JOIN {pathway_criteria_group} pcg
                    ON pcg.id = tcp.path_instance_id
                  JOIN {pathway_criteria_group_criterion} pcgc
                    ON pcgc.criteria_group_id = pcg.id
                 WHERE tcp.path_type = :pathtype
                   AND tcp.status = :activestatus
                   AND pcgc.criterion_id {$criteria_id_sql}";
        if (!empty($user_id)) {
            $sql .= ' AND tacu.user_id = :userid';
            $params['userid'] = $user_id;
        }

        $params['pathtype'] = 'criteria_group';
        $params['activestatus'] = pathway::PATHWAY_STATUS_ACTIVE;
        $params['userid'] = $user_id;

        $rows = $DB->get_records_sql($sql, $params);

        foreach ($rows as $row) {
            $aggregation_table->queue_for_aggregation($row->user_id, $row->comp_id);
        }
    }

}
