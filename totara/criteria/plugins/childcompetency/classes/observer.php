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

namespace criteria_childcompetency;

use core\orm\query\table;
use hierarchy_competency\event\competency_created;
use hierarchy_competency\event\competency_deleted;
use hierarchy_competency\event\competency_moved;
use tassign_competency\event\assignment_user_archived;
use tassign_competency\event\assignment_user_assigned;
use tassign_competency\event\assignment_user_unassigned;
use totara_competency\entities\competency;
use totara_competency\event\competency_achievement_updated;
use totara_criteria\criterion;
use totara_criteria\entities\criteria_item as item_entity;
use totara_criteria\entities\criteria_metadata as metadata_entity;
use totara_criteria\event\criteria_achievement_changed;

class observer {

    public static function competency_created(competency_created $event) {
        // Update the items for the new competency's parent
        $competency = new competency($event->objectid);
        if (!empty($competency->parentid)) {
            items_processor::update_items($competency->parentid);
        }
    }

    public static function competency_moved(competency_moved $event) {
        global $DB;

        // The event doesn't provide information on the previous parent.
        // We therefore need to find it through the existing items
        $previous_parent_id = static::get_parent_competency_of_item($event->objectid);
        if (!is_null($previous_parent_id)) {
            items_processor::update_items($previous_parent_id);
        }

        // If new parent is not top, update it's items as well
        $competency = new competency($event->objectid);
        if (!empty($competency->parentid)) {
            items_processor::update_items($competency->parentid);
        }
    }

    public static function competency_deleted(competency_deleted $event) {
        // If the deleted competency has a parent, we need to update its parent
        // competency's items
        $previous_parent_id = static::get_parent_competency_of_item($event->objectid);
        if (!is_null($previous_parent_id)) {
            items_processor::update_items($previous_parent_id);
        }
    }

    public static function competency_achievement_updated(competency_achievement_updated $event) {
        // Find all criteria items on this competency's parent (i.e. find all criteria with a 'competency' item
        // with this competency as item_id) (Not expecting there to be more than 1, but who knows what clients will do)
        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        static::trigger_parent_criteria_achievement_changed($event->relateduserid, $event->other['competency_id']);
    }

    public static function user_assigned(assignment_user_assigned $event) {
        // If the user is assigned to a child competency and the parent has 'childcompetency' criteria
        // we need to re-aggregate the user's rating on the parent
        static::trigger_parent_criteria_achievement_changed($event->relateduserid, $event->other['competency_id']);
    }

    public static function user_unassigned(assignment_user_unassigned $event) {
        // If the user is unassigned from a child competency and the parent has 'childcompetency' criteria
        // we need to re-aggregate the user's rating on the parent
        static::trigger_parent_criteria_achievement_changed($event->relateduserid, $event->other['competency_id']);
    }

    public static function user_assignment_archived(assignment_user_archived $event) {
        // If the user's assignment on a child competency is archived and the parent has 'childcompetency' criteria
        // we need to re-aggregate the user's rating on the parent
        static::trigger_parent_criteria_achievement_changed($event->relateduserid, $event->other['competency_id']);
    }

    /**
     * Retrieve the competency_id from metadata for the specific 'competency' item's parent
     * @param  int $child_competency_id Competency id to search for
     * @return int|null Id of parent competency
     */
    private static function get_parent_competency_of_item(int $child_competency_id) {
        $item_type = (new childcompetency())->get_items_type();

        $parent_competency_id = metadata_entity::repository()
            ->join((new table(item_entity::TABLE))->as('item'), 'criterion_id', 'criterion_id')
            ->where('item.item_type', $item_type)
            ->where('item.item_id', $child_competency_id)
            ->where('metakey', criterion::METADATA_COMPETENCY_KEY)
            ->get()
            ->first();

        return !is_null($parent_competency_id) ? $parent_competency_id ->metavalue : null;
    }

    /**
     * Trigger the criteria_achievement_changed event for all childcompetency criteria on the parent competency
     *
     * @param int $user_id
     * @param int $child_competency_id
     */
    private static function trigger_parent_criteria_achievement_changed(int $user_id, int $child_competency_id) {
        $criteria_ids = item_entity::repository()
            ->select('criterion_id')
            ->where('item_type', 'competency')
            ->where('item_id', $child_competency_id)
            ->group_by('criterion_id')
            ->get()
            ->pluck('criterion_id');

        if (!empty($criteria_ids)) {
            criteria_achievement_changed::create_with_ids($user_id, $criteria_ids)->trigger();
        }
    }
}
