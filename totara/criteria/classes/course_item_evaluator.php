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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_criteria;

class course_item_evaluator extends item_evaluator {

    /**
     * Evaluate criteria completion / satisfaction for users in the source
     * @param criterion $criterion
     * @param int $now
     */
    protected function update_criterion_completion(criterion $criterion, int $now) {
        global $DB;

        $criterion_id = $criterion->get_id();

        // Not linking with the users_source here as we've already ensured that there is a record for applicable users in the parent

        // MySQL doesn't allow using the same table in the update and the where - therefore had to settle for multiple queries
        // First selecting the records to change, then performing the update

        $meet_select_sql =
            "SELECT tcir.id
               FROM {totara_criteria_item_record} tcir
               JOIN {totara_criteria_item} tci
                 ON tci.id = tcir.criterion_item_id
          LEFT JOIN {course_completions} cc
                 ON cc.course = tci.item_id
                AND cc.userid = tcir.user_id
                AND cc.status IN (:statuscomplete, :statusrpl)
              WHERE tci.criterion_id = :criterionid
                AND tci.item_type = :itemtype
                AND tcir.criterion_met = :currentmet";

        $user_completed_wh = " AND cc.id IS NOT NULL";
        $user_not_completed_wh = " AND cc.id IS NULL";

        $select_params = [
            'criterionid' => $criterion_id,
            'itemtype' => 'course',
            'currentmet' => 0,
            'statuscomplete' => COMPLETION_STATUS_COMPLETE,
            'statusrpl' => COMPLETION_STATUS_COMPLETEVIARPL
        ];

        $update_sql =
           "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE id ";

        // Users that must meet
        $must_meet_record_ids = $DB->get_fieldset_sql($meet_select_sql . $user_completed_wh, $select_params);
        if (!empty($must_meet_record_ids)) {
            [$id_sql, $update_params] = $DB->get_in_or_equal($must_meet_record_ids, SQL_PARAMS_NAMED);
            $update_params['newmet'] = 1;
            $update_params['now'] = $now;

            $DB->execute($update_sql . $id_sql, $update_params);
        }

        // Then users that must not meet - we need to handle users that doesn't yet have a completion record -
        // thus the need for the left join on course_completions
        $select_params['currentmet'] = 1;
        $must_not_meet_record_ids = $DB->get_fieldset_sql($meet_select_sql . $user_not_completed_wh, $select_params);
        if (!empty($must_not_meet_record_ids)) {
            [$id_sql, $update_params] = $DB->get_in_or_equal($must_not_meet_record_ids, SQL_PARAMS_NAMED);
            $update_params['newmet'] = 0;
            $update_params['now'] = $now;

            $DB->execute($update_sql . $id_sql, $update_params);
        }

        // Todo: deal with timecompleted?? Is it part of the check for whether a course is complete.
        //       And if there happens to be an inconsistency between timecompleted and status, is the course considered complete or not?
    }

}
