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

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\resolver\has_middleware;
use mod_perform\data_providers\activity\subject_instance_for_participant as subject_instance_data_provider;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\util;

class subject_instance implements query_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $subject_instance_id = $args['subject_instance_id'] ?? 0;
        if (!$subject_instance_id) {
            throw new \invalid_parameter_exception('invalid subject instance id');
        }

        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->find($subject_instance_id);

        if ($subject_instance_entity === null) {
            return null;
        }

        $ec->set_relevant_context(subject_instance_model::load_by_entity($subject_instance_entity)->get_context());

        /** @var user $target_participant */
        $user_id = user::logged_in()->id;

        if (util::can_manage_participation($user_id, $subject_instance_entity->subject_user_id)) {
            return new subject_instance_model($subject_instance_entity);
        }

        /** @var subject_instance_model $subject_instance */
        return (new subject_instance_data_provider($user_id))
            ->set_subject_instance_id_filter($subject_instance_id)
            ->fetch()
            ->get()
            ->first();
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