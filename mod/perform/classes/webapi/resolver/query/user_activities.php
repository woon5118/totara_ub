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
use core\entities\user;
use core\orm\collection;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use mod_perform\data_providers\activity\user_activities as user_activities_data_provider;
use mod_perform\models\activity\user_activity as user_activity_model;

class user_activities implements query_resolver {

    /**
     * Get the subject instances that the logged in user is participating in.
     *
     * @param array $args
     * @param execution_context $ec
     * @return collection|mixed|user_activity_model[]
     */
    public static function resolve(array $args, execution_context $ec) {
        require_login(null, false, null, false, true);

        // Only supports the logged in user for now.
        // $args['participant_id'] ??
        $participant_id = User::logged_in()->id;

        $filters = $args['filters'] ?? [];
        $about_filter = $filters['about'] ?? [];

        return (new user_activities_data_provider($participant_id))
            ->set_about_filter($about_filter)
            ->fetch()
            ->get();
    }

}