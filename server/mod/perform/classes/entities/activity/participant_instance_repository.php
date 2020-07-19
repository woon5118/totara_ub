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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\entity\repository;
use core\orm\query\builder;

class participant_instance_repository extends repository {

    /**
     * Should one user be able to see the other users profile details in the context of mod perform.
     *
     * Will return true if the viewing user share a subject instance with the target user,
     * or if the the target user is the subject of a subject_instance that the viewing user is participating in.
     *
     * @param int $viewing_user_id The user requesting to view the target user
     * @param int $target_user_id The user who's
     * @return bool
     */
    public function user_can_view_other_users_profile(int $viewing_user_id, int $target_user_id): bool {
        $shared_subject_instance = participant_instance::repository()
            ->as('other_pi')
            ->select('id')
            ->where_raw('main_pi.subject_instance_id = other_pi.subject_instance_id')
            ->where_raw('other_pi.id != main_pi.id')
            ->get_builder();

        $participant_in_subject_about_target = subject_instance::repository()
            ->as('si')
            ->select('id')
            ->where('si.subject_user_id', $target_user_id)
            ->where_raw('si.id = main_pi.subject_instance_id')
            ->get_builder();

        return participant_instance::repository()
            ->as('main_pi')
            ->where('main_pi.participant_id', $viewing_user_id)
            ->where(function (builder $builder) use ($shared_subject_instance, $participant_in_subject_about_target) {
                return $builder->where_exists($shared_subject_instance)
                    ->or_where_exists($participant_in_subject_about_target);
            })
            ->exists();
    }

}