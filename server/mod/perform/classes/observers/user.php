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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\observers;

use core\event\user_deleted;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\participant_source;
use mod_perform\models\activity\subject_instance;
use mod_perform\state\participant_instance\closed as participant_instance_closed;
use mod_perform\state\subject_instance\closed as subject_instance_closed;

class user {

    /**
     * @param user_deleted $event
     * @return void
     */
    public static function user_deleted(user_deleted $event) {
        // Set all track user assignments to deleted to make sure
        // no new subject instance get created
        track_user_assignment::repository()
            ->where('subject_user_id', $event->objectid)
            ->update([
                'deleted' => 1,
                'updated_at' => time()
            ]);

        // Now close all subject instances
        /** @var subject_instance[] $subject_instances */
        $subject_instances = subject_instance_entity::repository()
            ->where('subject_user_id', $event->objectid)
            ->where('availability', '<>', subject_instance_closed::get_code())
            ->get()
            ->map_to(subject_instance::class);

        foreach ($subject_instances as $subject_instance) {
            $subject_instance->manually_close();
        }

        // Now close all participant instance not closed yet
        /** @var participant_instance[] $participant_instances */
        $participant_instances = participant_instance_entity::repository()
            ->where('participant_id', $event->objectid)
            ->where('participant_source', participant_source::INTERNAL)
            ->where('availability', '<>', participant_instance_closed::get_code())
            ->get()
            ->map_to(participant_instance::class);

        foreach ($participant_instances as $participant_instance) {
            $participant_instance->manually_close();
        }
    }

}
