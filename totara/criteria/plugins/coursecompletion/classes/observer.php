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
use totara_criteria\entities\criteria_item;
use totara_criteria\event\criteria_satisfied;

class observer {

    public static function course_completed(course_completed $event) {
        global $DB;

        // Find all criteria items for completion of this course
        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_satisfied event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        $criteria_ids = criteria_item::repository()
            ->select('criterion_id')
            ->where('item_type', 'course')
            ->where('item_id', $event->courseid)
            ->group_by('criterion_id')
            ->get()
            ->pluck('criterion_id');

        if (empty($criteria_ids)) {
            // We're not tracking the course - nothing more to do
            return;
        }

        criteria_satisfied::create_with_ids($criteria_ids, $event->relateduserid)->trigger();
    }
}
