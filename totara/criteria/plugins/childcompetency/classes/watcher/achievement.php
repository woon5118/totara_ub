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
 * @package criteria_childcompetency
 */

namespace criteria_childcompetency\watcher;

use totara_competency\hook\competency_achievement_updated;
use totara_criteria\criterion;
use totara_criteria\entities\criteria_item as item_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_competency\hook\competency_achievement_updated_bulk;
use totara_criteria\hook\criteria_achievement_changed;

class achievement {

    public static function updated(competency_achievement_updated $hook) {
        global $DB;

        // Find all criteria items on this competency's parent (i.e. find all criteria with a 'competency' item
        // with this competency as item_id) (Not expecting there to be more than 1, but who knows what clients will do)
        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        $achievement = $hook->get_achievement();
        $child_competency_id = $achievement['comp_id'];
        $user_id = $achievement['user_id'];

        $criteria_ids = item_entity::repository()
            ->as('tci')
            ->join([criterion_entity::TABLE, 'tc'], 'tci.criterion_id', 'tc.id')
            ->where('tci.item_type', 'competency')
            ->where('tci.item_id', $child_competency_id)
            ->where('tc.plugin_type', 'childcompetency')
            ->get()
            ->pluck('criterion_id');

        $criteria_ids = array_unique($criteria_ids);

        if (!empty($criteria_ids)) {
            $hook = new criteria_achievement_changed([$user_id => $criteria_ids]);
            $hook->execute();
        }
    }

    public static function updated_bulk(competency_achievement_updated_bulk $hook) {
        global $DB;

        // Find all criteria items on this competency's parent (i.e. find all criteria with a 'competency' item
        // with this competency as item_id) (Not expecting there to be more than 1, but who knows what clients will do)
        // As the criterion has no knowledge whether this user's satisfaction of the criteria is to be tracked,
        // it simply generates an criteria_achievement_changed event with the relevant criterion ids and this user's id.
        // Modules that use these criteria are responsible for initiating the relevant processes to create/update
        // the item_record(s) for this user

        $child_competency_id = $hook->get_competency_id();
        $user_ids = $hook->get_user_ids();
        if (empty($user_ids)) {
            return;
        }

        $criteria_ids = item_entity::repository()
            ->as('tci')
            ->join([criterion_entity::TABLE, 'tc'], 'tci.criterion_id', 'tc.id')
            ->where('tci.item_type', 'competency')
            ->where('tci.item_id', $child_competency_id)
            ->where('tc.plugin_type', 'childcompetency')
            ->get()
            ->pluck('criterion_id');

        if (!empty($criteria_ids)) {
            $user_criteria_ids = array_fill_keys($user_ids, $criteria_ids);
            $hook = new criteria_achievement_changed($user_criteria_ids);
            $hook->execute();
        }
    }

}
