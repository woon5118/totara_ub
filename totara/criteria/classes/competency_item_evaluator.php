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
 * @package totara_criteria
 */

namespace totara_criteria;

use totara_competency\entities\competency_achievement;

class competency_item_evaluator extends item_evaluator {

    protected function update_criterion_completion(criterion $criterion, int $now) {
        global $DB;

        /******************************************************************************************
         * NOTE: We do NOT take assignments into consideration!!!
         *       Proficiency in child competencies is considered "outside" of an assignment,
         *       i.e. proficiency via any assignment is considered a valid fulfillment of criteria
         ******************************************************************************************/
        $criterion_id = $criterion->get_id();

        // Not linking with the users_source here as we've already ensured that there is a record for applicable users in the parent
        // Due to MySQL not allowing the table being updated to appear in a sub query, we need to do the updating in separate steps

        // First item_records shown as 'not met' but user is proficient in the child competency

        $sql =
            "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE criterion_met = :currentmet
               AND (criterion_item_id, user_id) IN ( 
                    SELECT tci.id, p.user_id
                      FROM {totara_criteria_item} tci
                      JOIN (
                           SELECT tca.comp_id, tca.user_id, MAX(tca.proficient) AS proficient
                             FROM {totara_competency_achievement} tca
                            WHERE tca.status = :achievementstatus
                            GROUP BY tca.comp_id, tca.user_id
                              HAVING MAX(tca.proficient) = :isproficient) p
                        ON tci.item_id = p.comp_id
                     WHERE tci.criterion_id = :criterionid
                       AND tci.item_type = :itemtype)";

        $params = [
            'newmet' => 1,
            'now' => $now,
            'achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
            'itemtype' => 'competency',
            'currentmet' => 0,
            'isproficient' => 1,
            'criterionid' => $criterion_id,
        ];

        $DB->execute($sql, $params);

        // Now item_records shown as 'met' but user is not proficient in the child competency
        $params['newmet'] = 0;
        $params['currentmet'] = 1;
        $params['isproficient'] = 0;

        $DB->execute($sql, $params);

        // Now item_records shown as 'met' but no existing achievement record for the child competencies

        $sql =
            "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE criterion_met = :currentmet
               AND criterion_item_id IN ( 
                    SELECT tci.id
                      FROM {totara_criteria_item} tci
                 LEFT JOIN {totara_competency_achievement} tca
                        ON tci.item_id = tca.comp_id
                       AND tca.status = :achievementstatus
                     WHERE tci.criterion_id = :criterionid
                       AND tci.item_type = :itemtype
                       AND tca.id IS NULL)";

        $params = [
            'newmet' => 0,
            'now' => $now,
            'achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
            'itemtype' => 'competency',
            'currentmet' => 1,
            'criterionid' => $criterion_id,
        ];

        $DB->execute($sql, $params);
    }

}
