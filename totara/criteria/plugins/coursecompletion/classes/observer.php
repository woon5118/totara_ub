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
 * @package criteria_coursecompletion
 */

namespace criteria_coursecompletion;

use core\event\course_completed;
use totara_criteria\course_item_evaluator;
use totara_criteria\event\item_updated;

class observer {

    public static function course_completed(course_completed $event) {
        global $DB;

        // TODO: This is no longer valid - There may be more than one item for a specific course
        //       To be fixed in TL-22569
        return;
//        $criterion_item_id = $DB->get_field(
//            'totara_criteria_item',
//            'id',
//            [
//                'item_type' => 'course',
//                'item_id' => $event->courseid
//            ]
//        );
//
//        if (!$criterion_item_id) {
//            // We're not tracking the course.
//            return;
//        }
//
//        $now = time();
//
//        $item_record = $DB->get_record(
//            'totara_criteria_item_record',
//            [
//                'criterion_item_id' => $criterion_item_id,
//                'user_id' => $event->relateduserid,
//            ]
//        );
//
//        if (!$item_record) {
//            // We're not tracking this user for this item.
//            return;
//        }
//
//        // Update the criterion_met for this user
//        $item_record->criterion_met = 1;
//        $item_record->timeevaluated = time();
//        $DB->update_record('totara_criteria_item_record', $item_record);
//
//        item_updated::create_with_item_record($criterion_item_id, $item_record)->trigger();
    }
}
