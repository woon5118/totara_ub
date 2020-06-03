<?php
/**
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

namespace mod_perform\task\service;

use core\orm\collection;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\hook\subject_instances_created;

/**
 * This class is responsible for creating new subject instances for users who
 * are assigned to a track.
 *
 * Currently, it creates new instance for every assignment which does not have a
 * subject instance yet and meets time interval restrictions.
 * In the future it will also create repeating subject instances, if the track is
 * configured that way.
 */
class subject_instance_creation {

    public function generate_instances() {
        // Get all user assignments which do not have a subject instance at the moment
        $user_assignments  = $this->get_active_user_assignments();

        $dtos = new \core\collection();

        foreach ($user_assignments as $user_assignment) {
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->job_assignment_id = $user_assignment->job_assignment_id;
            $subject_instance->save();

            $dtos->append(subject_instance_dto::create_from_entity($subject_instance));
        }

        $hook = new subject_instances_created($dtos);
        $hook->execute();
    }

    /**
     * Get all user assignments which do not yet have a subject instance and which have matching
     * period settings.
     * Also skip tracks that need schedule synchronisation because that should happen before we create
     * subject instances.
     *
     * @return collection|track_user_assignment[]
     */
    private function get_active_user_assignments(): collection {
        // TODO Later we need to take the repeating schedule into account and the status of the subject instance.
        //      Currently, we just create new subject instances if there are no existing ones.
        return track_user_assignment::repository()
            ->filter_by_no_subject_instances()
            ->filter_by_active()
            ->filter_by_active_track_and_activity()
            ->filter_by_time_interval()
            ->filter_by_does_not_need_schedule_sync()
            ->with('track')
            ->get();
    }

}