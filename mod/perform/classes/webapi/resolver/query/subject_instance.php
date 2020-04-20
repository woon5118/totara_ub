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

namespace mod_perform\webapi\resolver\query;

use totara_core\advanced_feature;
use core\entities\user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use mod_perform\data_providers\activity\subject_instance as subject_instance_data_provider;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;

class subject_instance implements query_resolver {

    /**
     * Get the subject instances that the logged in user is participating in.
     *
     * @param array $args
     * @param execution_context $ec
     * @return mixed|subject_instance_model|null
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $subject_instance_id = $args['subject_instance_id'];

        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->find($subject_instance_id);

        if ($subject_instance_entity === null) {
            return null;
        }

        $ec->set_relevant_context(subject_instance_model::load_by_entity($subject_instance_entity)->get_context());

        /** @var user $target_participant */
        $participant_id = User::logged_in()->id;

        /** @var subject_instance_model $subject_instance */
        return (new subject_instance_data_provider($participant_id))
            ->set_subject_instance_id_filter($subject_instance_id)
            ->fetch()
            ->get()
            ->first();
    }

}