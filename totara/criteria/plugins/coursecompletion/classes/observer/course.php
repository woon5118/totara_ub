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

use coding_exception;
use core\event\base;
use core\event\course_completed;
use core\orm\collection;
use totara_completioneditor\event\course_completion_edited;
use totara_completionimport\event\bulk_course_completionimport;
use totara_criteria\entities\criteria_item;

class course {

    /**
     * In case someone completes a course or manually edits course completion we need to make sure it gets reaggregated
     *
     * @param base|course_completed|course_completion_edited $event
     */
    public static function course_completion_changed(base $event) {
        if (!$event instanceof course_completed && !$event instanceof course_completion_edited) {
            throw new coding_exception('Expected course_completed or course_completion_edited event');
        }

        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        $criteria_items = self::get_criteria_items_of_course_ids([$event->courseid]);

        if ($criteria_items->count()) {
            $hook = new \totara_criteria\hook\criteria_achievement_changed([$event->relateduserid => $criteria_items->pluck('criterion_id')]);
            $hook->execute();
        }
    }

    /**
     * When course completion records are imported in bulk, we also need to make sure competency achievement is reaggregated
     *
     * @param bulk_course_completionimport $event
     */
    public static function bulk_course_completions_imported(bulk_course_completionimport $event) {

        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids for each user.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        // Get courses per user
        $user_courses = [];
        foreach ($event->other[bulk_course_completionimport::PAYLOAD_KEY] as $user_completion) {
            $user_id = $user_completion['userid'];
            $course_id = $user_completion['courseid'];
            if (!isset($user_courses[$user_id])) {
                $user_courses[$user_id] = [];
            }
            $user_courses[$user_id][] = $course_id;
        }

        // Find a list of all unique course ids from the event payload
        $course_ids = array_unique(array_column($event->other[bulk_course_completionimport::PAYLOAD_KEY], 'courseid'));

        // Map course_ids to criterion_ids
        $course_criteria = array_fill_keys($course_ids, []);
        $criteria_items = self::get_criteria_items_of_course_ids($course_ids);
        foreach ($criteria_items as $criteria_item) {
            $course_criteria[$criteria_item->item_id][] = $criteria_item->criterion_id;
        }

        // Build the list of criterion_ids per user
        $user_criteria_ids = [];
        foreach ($user_courses as $user_id => $course_ids) {
            $ids = [];
            foreach ($course_ids as $course_id) {
                if (!empty($course_criteria[$course_id])) {
                    array_push($ids, ...$course_criteria[$course_id]);
                }
            }

            if (!empty($ids)) {
                $user_criteria_ids[$user_id] = array_unique($ids);
            }
        }

        if (!empty($user_criteria_ids)) {
            $hook = new \totara_criteria\hook\criteria_achievement_changed($user_criteria_ids);
            $hook->execute();
        }
    }

    /**
     * Find all criteria items for completion of this course
     *
     * @param int[] $ids
     * @return collection
     */
    private static function get_criteria_items_of_course_ids(array $ids): collection {
        return criteria_item::repository()
            ->where('item_type', 'course')
            ->where('item_id', $ids)
            ->get();
    }

}
