<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\observers;

use core\event\base;
use core\event\cohort_member_added;
use core\event\cohort_member_removed;
use mod_perform\user_groups\grouping;
use mod_perform\track_assignment_actions;
use totara_cohort\event\members_updated;

class track_assignment_user_groups {

    /**
     * When dynamic audience members changes
     *
     * @param base|cohort_member_added|cohort_member_removed|members_updated $event
     */
    public static function cohort_updated(base $event) {
        $valid_events = [
            members_updated::class,
            cohort_member_added::class,
            cohort_member_removed::class
        ];

        if (!in_array(get_class($event), $valid_events)) {
            throw new \coding_exception('Invalid event, expected members_updated, cohort_member_added or cohort_member_removed');
        }

        track_assignment_actions::create()->mark_for_expansion([grouping::cohort($event->objectid)]);
    }

}