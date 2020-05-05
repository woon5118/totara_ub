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

namespace mod_perform\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\activity as activity_entity;
use moodle_exception;
use totara_core\advanced_feature;
use container_perform\perform as perform_container;

class delete_activity implements mutation_resolver {

    /**
     * This hard deletes the activity, instances, assignments, responses and elements (if not used by other activities).
     *
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $args = $args['input'];

        /** @var activity_entity $activity_entity */
        $activity_entity = activity_entity::repository()->find($args['activity_id']);

        if ($activity_entity === null) {
            throw new moodle_exception('invalid_activity', 'mod_perform');
        }

        $activity = activity::load_by_entity($activity_entity);

        if (!$activity->can_delete()) {
            throw new moodle_exception('invalid_activity', 'mod_perform');
        }

        perform_container::from_activity($activity)->delete();

        return true;
    }
}
