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

class course_item_combined extends item_combined {

    protected function update_criterion_completion(criterion $criterion) {
        global $DB;

        $criterion_id = $criterion->get_id();

        // Get the timestamp here. This is used at the end to mark changed users in the temp table
        $now = time();

        // TODO: Should we delete items if using a list?? I.e. list is required to contain all assignements??
        $this->delete_item_records($criterion_id);
        $this->create_item_records($criterion_id, $now);

        // No need to link with temp_users or list here as we've already ensured that there is a record for applicable users
        // Handle courses that are completed but item_record still indicates 'not met'
        $sql =
           "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE id IN (
                   SELECT tcir.id
                     FROM {totara_criteria_item_record} tcir
                     JOIN {totara_criteria_item} tci 
                       ON tci.id = tcir.criterion_item_id
                      AND tci.item_type = :itemtype
                     JOIN {course_completions} cc 
                       ON cc.course = tci.item_id 
                      AND cc.userid = tcir.user_id
                    WHERE tci.criterion_id = :criterionid
                      AND tcir.criterion_met = :currentmet
                      AND (cc.status = :statuscomplete OR cc.status = :statusrpl))";
        $params = [
            'now' => $now,
            'itemtype' => 'course',
            'criterionid' => $criterion_id,
            'newmet' => 1,
            'currentmet' => 0,
            'statuscomplete' => COMPLETION_STATUS_COMPLETE,
            'statusrpl' => COMPLETION_STATUS_COMPLETEVIARPL
        ];

        $DB->execute($sql, $params);

        // Now do the reverse. Todo: deal with timecompleted. That is also part of the check for whether a course is complete.
        // And if there happens to be an inconsistency between timecompleted and status, is the course considered complete or not?
        // Note the small difference with the previous sql :- LEFT JOIN on course_completion
         $sql =
           "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE id IN (
                   SELECT tcir.id
                     FROM {totara_criteria_item_record} tcir
                     JOIN {totara_criteria_item} tci 
                       ON tci.id = tcir.criterion_item_id
                      AND tci.item_type = :itemtype
                LEFT JOIN {course_completions} cc 
                       ON cc.course = tci.item_id 
                      AND cc.userid = tcir.user_id
                    WHERE tci.criterion_id = :criterionid
                      AND tcir.criterion_met = :currentmet
                      AND (cc.id IS NULL OR
                          (cc.status <> :statuscomplete AND cc.status <> :statusrpl)))";
        $params['newmet'] = 0;
        $params['currentmet'] = 1;

        $DB->execute($sql, $params);
    }
}
