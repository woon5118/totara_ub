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

use context_system;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\entities\activity\activity as activity_entity;

class activity implements query_resolver {

    /**
     * Get a specific perform activity by id.
     *
     * @param array $args
     * @param execution_context $ec
     * @return activity_model
     */
    public static function resolve(array $args, execution_context $ec) {
        require_login();

        $activity = activity_model::load_by_id($args['activity_id']);

        require_capability('mod/perform:manage_activity', $activity->get_context());

        return $activity;
    }
}