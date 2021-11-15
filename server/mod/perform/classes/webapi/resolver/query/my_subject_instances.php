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

use core\entity\user;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\data_providers\activity\subject_instance_for_participant as subject_instance_data_provider;
use mod_perform\models\activity\participant_source;
use mod_perform\util;

class my_subject_instances implements query_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $participant_id = user::logged_in()->id;

        $pagination = $args['pagination'] ?? [];
        $cursor = $pagination['cursor'] ?? '';
        $results = (new subject_instance_data_provider($participant_id, participant_source::INTERNAL))
            ->add_filters($args['filters'] ?? [])
            ->get_subject_sections_page($cursor);

        // This is a workaround for making sure the correct access control checks
        // for users are triggered. It needs a course context to determine this.
        // If we enrol users into an activity we can remove this workaround
        $first_section = $results->items->first();
        $context = $first_section
            ? $first_section->get_subject_instance()->get_context()
            : util::get_default_context();
        $ec->set_relevant_context($context);

        return $results;
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