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

        // Not linking with the users_source here as we've already ensured that there is a record in the parent function

        // Due to MySQL not allowing the table being updated and MSSQL not allowing '(column1, column2) IN' in a where clause,
        // first selecting items to update and then updating them

        // Find all item records where the currently indicated 'criterion_met' is wrong
        // Doing it in 2 sets as there can be a huge number of assigned users which may result in very large arrays
        // TODO: Need to do stress testing here with huge number of assigned users

        // First users marked as not having met the criteria, but have satisfied all
        $select_sql = "
            SELECT tcir.id
              FROM {totara_criteria_item} tci
              JOIN {totara_criteria_item_record} tcir
                ON tcir.criterion_item_id = tci.id
              JOIN (
                   SELECT DISTINCT tca.comp_id, tca.user_id
                     FROM {totara_competency_achievement} tca
                    WHERE tca.status = :achievementstatus 
                        AND tca.proficient = :isproficient
                ) p
                ON tci.item_id = p.comp_id
               AND tcir.user_id = p.user_id
             WHERE tci.criterion_id = :criterionid
               AND tci.item_type = :itemtype
               AND tcir.criterion_met = :currentmet
               ";

        $select_params = [
            'achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
            'isproficient' => 1,
            'criterionid' => $criterion_id,
            'itemtype' => 'competency',
            'currentmet' => 0,
        ];

        $nowmet = $DB->get_fieldset_sql($select_sql, $select_params);
        if (!empty($nowmet)) {
            [$user_sql, $user_params] = $DB->get_in_or_equal($nowmet, SQL_PARAMS_NAMED);

            // Now update item_records with the correct proficient value
            $update_sql =
                "UPDATE {totara_criteria_item_record}
                   SET criterion_met = :newmet, 
                       timeevaluated = :now
                 WHERE id {$user_sql}";

            $update_params = array_merge($user_params, ['newmet' => 1, 'now' => $now]);
            $DB->execute($update_sql, $update_params);
        }

        // Now users marked as 'criterion_met' but doesn't actually satisfy the criteria
        // (only difference in select_sql is the use of left join to handle cases where the user doesn't have an achievement record anymore)

        $select_sql = "
            SELECT tcir.id
              FROM {totara_criteria_item} tci
              JOIN {totara_criteria_item_record} tcir
                ON tcir.criterion_item_id = tci.id
         LEFT JOIN (
                   SELECT tca.comp_id, tca.user_id, MAX(tca.proficient) AS proficient
                     FROM {totara_competency_achievement} tca
                    WHERE tca.status = :achievementstatus
                    GROUP BY tca.comp_id, tca.user_id) p
                ON tci.item_id = p.comp_id
               AND tcir.user_id = p.user_id
             WHERE tci.criterion_id = :criterionid
               AND tci.item_type = :itemtype
               AND tcir.criterion_met = :currentmet
               AND (p.proficient IS NULL OR p.proficient = :isproficient)";

        $select_params = [
            'achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
            'isproficient' => 0,
            'criterionid' => $criterion_id,
            'itemtype' => 'competency',
            'currentmet' => 1,
        ];

        $notmet = $DB->get_fieldset_sql($select_sql, $select_params);
        if (!empty($notmet)) {
            [$user_sql, $user_params] = $DB->get_in_or_equal($notmet, SQL_PARAMS_NAMED);

            // Now update item_records with the correct proficient value
            $update_sql =
                "UPDATE {totara_criteria_item_record}
                   SET criterion_met = :newmet, 
                       timeevaluated = :now
                 WHERE id {$user_sql}";

            $update_params = array_merge($user_params, ['newmet' => 0, 'now' => $now]);
            $DB->execute($update_sql, $update_params);
        }
    }

}
