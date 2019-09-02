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

class course_item_evaluator {
    use item_evaluator;

    public static function update_item_records($user_ids = null, $item_ids = null) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/completion/completion_completion.php');

        // Todo: if item_ids and user_ids are null, allow all. If they are arrays. We restrict by those ids.

        // Todo: deal with timecompleted.
        $item_to_course_sql = "
            SELECT tcir.*
              FROM {totara_criteria_item_record} tcir
              JOIN {totara_criteria_item} tci ON tcir.criterion_item_id = tci.id
              JOIN {course_completions} cc ON tci.item_id = cc.course AND tcir.user_id = cc.userid
             WHERE tci.item_type = 'course'
        ";

        $records = $DB->get_records_sql(
            $item_to_course_sql . "
                   AND tcir.criterion_met = 0
                   AND (cc.status = :complete OR cc.status = :rpl)
                ",
            ['complete' => COMPLETION_STATUS_COMPLETE, 'rpl' => COMPLETION_STATUS_COMPLETEVIARPL]
        );

        static::update_criterion_met(1, array_keys($records));


        // Now do the reverse. Todo: deal with timecompleted. That is also part of the check for whether a course is complete.
        // And if there happens to be an inconsistency between timecompleted and status, is the course considered complete or not?

        $records = $DB->get_records_sql(
            $item_to_course_sql . "
                   AND tcir.criterion_met = 1
                   AND (cc.status <> :complete AND cc.status <> :rpl)
                ",
            ['complete' => COMPLETION_STATUS_COMPLETE, 'rpl' => COMPLETION_STATUS_COMPLETEVIARPL]
        );

        static::update_criterion_met(0, array_keys($records));
    }
}