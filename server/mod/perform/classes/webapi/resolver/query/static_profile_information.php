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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\entities\activity\subject_static_instance as subject_static_instance_entity;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\models\activity\subject_static_instance as subject_static_instance_model;
use mod_perform\webapi\middleware\require_activity;

class static_profile_information implements query_resolver, has_middleware {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $subject_instance_id = $args['subject_instance_id'];
        $participant_instance_id = $args['participant_instance_id'];

        // Get subject instance.
        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->find($subject_instance_id);
        if (empty($subject_instance_entity)) {
            throw new \coding_exception('No subject instance found');
        }

        // Get participant instance.
        /** @var participant_instance_entity $participant_entity */
        $participant_entity = $subject_instance_entity->participant_instances()
            ->where('id', $participant_instance_id)
            ->one(true);

        // Get static subject instances.
        $static_instances = $subject_instance_entity->static_instances()->get();
        $instances = [];

        /** @var subject_static_instance_entity $static_instance */
        foreach ($static_instances as $static_instance) {
            $model = subject_static_instance_model::load_by_entity($static_instance);
            $instances[] = $model->get_job_assignment();
        }

        return [
            'subject' => [
                'subject_instance' => subject_instance_model::load_by_entity($subject_instance_entity),
                'job_assignments' => $instances,
            ],
            'participant' => participant_instance_model::load_by_entity($participant_entity),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            require_activity::by_subject_instance_id('subject_instance_id', true),
        ];
    }
}