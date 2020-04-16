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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use mod_perform\models\activity\subject_instance;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use totara_core\advanced_feature;

class participant_section implements query_resolver {

    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->find($args['subject_instance_id']);

        if ($subject_instance_entity === null) {
            return null;
        }

        $subject_instance = subject_instance::load_by_entity($subject_instance_entity);

        $participant_id = user::logged_in()->id;

        $participant_section = $subject_instance->get_participant_section_for_participant($participant_id);

        $ec->set_relevant_context($participant_section->get_section()->get_activity()->get_context());

        return $participant_section;
    }
}