<?php
/*
 *
 * This file is part of Totara LMS
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\models\activity\activity as activity_model;
use moodle_exception;
use totara_core\relationship\relationship;
use totara_core\relationship\relationship_provider;

/**
 * Query to return relationships that can be used in for performance activities.
 */
class relationships implements query_resolver, has_middleware {

    /**
     * Returns the available relationships for for performance activities.
     *
     * @param array $args
     * @param execution_context $ec
     * @return relationship[]
     */
    public static function resolve(array $args, execution_context $ec) {
        $activity = activity_model::load_by_id($args['activity_id']);

        $context = $activity->get_context();
        if (!$activity->can_manage()) {
            throw new moodle_exception('invalid_activity', 'mod_perform');
        }

        $ec->set_relevant_context($context);

        return relationship_provider::fetch_compatible_relationships(['user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            new require_login(),
        ];
    }

}
