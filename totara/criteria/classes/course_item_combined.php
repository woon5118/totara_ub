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
        $itemid_sql =
            "SELECT tci.id
               FROM {totara_criteria_item} tci 
              WHERE tci.criterion_id = :criterionid
                AND tci.item_type = :itemtype";

        $userid_sql =
            "SELECT cc.userid
               FROM {totara_criteria_item} tci 
               JOIN {course_completions} cc 
                 ON cc.course = tci.item_id 
              WHERE tci.criterion_id = :criterionid2
                AND tci.item_type = :itemtype2
                AND (cc.status = :statuscomplete OR cc.status = :statusrpl)";

        // Handle courses that are completed but item_record still indicates 'not met'
        $sql =
           "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE criterion_met = :currentmet
               AND criterion_item_id IN ({$itemid_sql})
               AND user_id IN ({$userid_sql})";
        $params = [
            'now' => $now,
            'itemtype' => 'course',
            'criterionid' => $criterion_id,
            'newmet' => 1,
            'currentmet' => 0,
            'itemtype2' => 'course',
            'criterionid2' => $criterion_id,
            'statuscomplete' => COMPLETION_STATUS_COMPLETE,
            'statusrpl' => COMPLETION_STATUS_COMPLETEVIARPL
        ];

        $DB->execute($sql, $params);

        // Now do the reverse. Todo: deal with timecompleted. That is also part of the check for whether a course is complete.
        // And if there happens to be an inconsistency between timecompleted and status, is the course considered complete or not?

        // We also need to make provision for users without a course_completion record
        // TODO: Test performance!!!
         $sql =
           "UPDATE {totara_criteria_item_record}
               SET criterion_met = :newmet, 
                   timeevaluated = :now
             WHERE criterion_met = :currentmet
               AND criterion_item_id IN ({$itemid_sql})
               AND user_id NOT IN ({$userid_sql})";
        $params['newmet'] = 0;
        $params['currentmet'] = 1;

        $DB->execute($sql, $params);
    }
}
