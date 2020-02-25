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
use mod_perform\models\activity\activity as activity_model;

class update_activity_general_info implements mutation_resolver {

    /**
     * Updates the general info (name and description) of an existing a perform activity.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array response envelop holding an activity model
     * @see activity
     */
    public static function resolve(array $args, execution_context $ec) {
        require_login(null, false, null, false, true);

        $activity = activity_model::load_by_id($args['activity_id']);

        require_capability('mod/perform:manage_activity', $activity->get_context());

        $activity->update_general_info($args['name'], $args['description'] ?? null);

        return ['activity' => $activity];
    }

}