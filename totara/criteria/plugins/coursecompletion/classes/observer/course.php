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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package criteria_coursecompletion
 */

namespace criteria_coursecompletion\observer;

use core\event\base;
use core\event\course_completed;
use totara_completioneditor\event\course_completion_edited;
use totara_criteria\entities\criteria_item;
use totara_criteria\event\criteria_achievement_changed;

class course {

    /**
     * In case someone completes a course or manually edits course completion we need to make sure it gets reaggregated
     *
     * @param base|course_completed|course_completion_edited $event
     */
    public static function course_completion_changed(base $event) {
        if (!$event instanceof course_completed && !$event instanceof course_completion_edited) {
            throw new \coding_exception('Expected course_completed or course_completion_edited event');
        }

        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        $criteria_ids = self::get_criteria_ids_for_course_id($event->courseid);

        if (!empty($criteria_ids)) {
            criteria_achievement_changed::create_with_ids($event->relateduserid, $criteria_ids)->trigger();
        }
    }

    /**
     * Find all criteria items for completion of this course
     *
     * @param int $id
     * @return array
     */
    private static function get_criteria_ids_for_course_id(int $id): array {
        return criteria_item::repository()
            ->select('criterion_id')
            ->where('item_type', 'course')
            ->where('item_id', $id)
            ->group_by('criterion_id')
            ->get()
            ->pluck('criterion_id');
    }
}
