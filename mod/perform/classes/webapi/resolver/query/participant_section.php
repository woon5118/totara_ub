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
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\resolver\has_middleware;
use mod_perform\data_providers\response\participant_section_with_responses;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\models\response\participant_section as participant_section_model;

class participant_section implements query_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $participant_instance_id = $args['participant_instance_id'] ?? 0;
        if (!$participant_instance_id) {
            throw new \invalid_parameter_exception('invalid participant instance id');
        }
        $participant_id = user::logged_in()->id;

        $participant_section_entity = participant_section_entity::repository()->fetch_default(
            $participant_instance_id,
            $participant_id
        );

        if ($participant_section_entity === null) {
            return null;
        }

        $ec->set_relevant_context(participant_instance_model::load_by_id($participant_instance_id)->get_context());

        $data_provider = new participant_section_with_responses($participant_id, $participant_section_entity->id);

        // When this participant section is fetched, for now we can safely assume that the participant is accessing
        // the section. In the future, this may have to move to a set_accessed mutation, e.g. if sections become
        // expandable in the front end and are fetched before actually being accessed.
        participant_section_model::load_by_entity($participant_section_entity)->on_participant_access();

        return $data_provider->fetch()->get();
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            new require_login()
        ];
    }
}