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
use core\event\course_deleted;
use core\event\course_restored;
use core\orm\collection;
use core\orm\entity\repository;
use criteria_coursecompletion\coursecompletion;
use totara_completioneditor\event\course_completion_edited;
use totara_completionimport\event\bulk_course_completionimport;
use totara_criteria\entities\criteria_item as criteria_item_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\hook\criteria_achievement_changed;
use totara_criteria\hook\criteria_validity_changed;

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
            $hook = new criteria_achievement_changed([$event->relateduserid => $criteria_items->pluck('criterion_id')]);
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

        // Find a list of all unique course ids from the event
        $course_ids = array_unique(array_column($event->get_completions(), 'courseid'));

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
            $hook = new criteria_achievement_changed($user_criteria_ids);
            $hook->execute();
        }
    }

    /**
     * When a course is deleted, we re-evaluate the validity of all criteria.
     * A validity_changed hook is executed with the ids of all criteria that were affected
     *
     * @param course_deleted $event
     */
    public static function course_deleted(course_deleted $event) {

        $criteria = self::get_criteria_with_course($event->objectid);

        if (!$criteria->count()) {
            return;
        }

        $affected_criteria = [];
        foreach ($criteria as $criterion_entity) {
            $criterion = coursecompletion::fetch_from_entity($criterion_entity);
            // Not checking anything here - this criterion refers to a deleted course
            // If already marked as invalid - nothing to do
            if ($criterion_entity->valid) {
                $criterion->set_valid(false);
                $criterion->save_valid();

                $affected_criteria[] = $criterion_entity->id;
            }
        }

        if (!empty($affected_criteria)) {
            $hook = new criteria_validity_changed($affected_criteria);
            $hook->execute();
        }
    }

    /**
     * When a course is restored, we update all items with the original course id to restored course id.
     * Then we re-evaluate the validity of all criteria using this course.
     * A validity_changed hook is executed with the ids of all criteria that were affected
     *
     * @param course_restored $event
     */
    public static function course_restored(course_restored $event) {
        $restored_course_id = $event->courseid;
        $original_course_id = $event->other['originalcourseid'] ?? $restored_course_id;
        $criteria = self::get_criteria_with_course($original_course_id, true);

        if (!$criteria->count()) {
            return;
        }

        $affected_criteria = [];
        foreach ($criteria as $criterion_entity) {
            // If only course content is restored, the original and restored course ids are the same
            // In this case we don't need to update the items
            // However, if a deleted course is restored, the course id changes.
            // In this case we need to update all items to refer the restored id
            if ($original_course_id != $restored_course_id) {
                foreach ($criterion_entity->items as $item) {
                    if ($item->item_type == 'course' && $item->item_id == $original_course_id) {
                        $item->item_id = $restored_course_id;
                        $item->save();
                    }
                }
            }

            $criterion = coursecompletion::fetch_from_entity($criterion_entity);
            // Validate and save validity
            $criterion->validate();
            if ($criterion->is_valid() != $criterion_entity->valid) {
                $criterion->save_valid();
                $affected_criteria[] = $criterion_entity->id;
            }
        }

        if (!empty($affected_criteria)) {
            $hook = new criteria_validity_changed($affected_criteria);
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
        return criteria_item_entity::repository()
            ->where('item_type', 'course')
            ->where('item_id', $ids)
            ->get();
    }

    /**
     * Find all criteria that has contains the specified course as item
     * @param int $course_id
     * @return collection
     */
    private static function get_criteria_with_course(int $course_id, bool $with_items = false): collection {
        return criterion_entity::repository()
            ->as('c')
            ->join([criteria_item_entity::TABLE, 'ci'], 'c.id', 'ci.criterion_id')
            ->when($with_items, function (repository $repository) {
                $repository->with('items');
            })
            ->where('ci.item_type', 'course')
            ->where('ci.item_id', $course_id)
            ->get();
    }

}
