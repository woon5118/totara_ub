<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_orm
 */

namespace totara_competency\webapi\resolver\query;

use context_system;
use context_user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_assignment\entities\user;
use totara_competency\models\profile\progress as progress_model;

class profile_progress implements query_resolver {

    public static function resolve(array $args, execution_context $ec) {
        return progress_model::for(static::authorize($args['user_id'] ?? null), $args['filters'] ?? []);
    }

    public static function authorize($user_id = null) {
        if (is_null($user_id)) {
            throw new \coding_exception('User id is required');
        }

        $user_id = intval($user_id);

        if (!$authorized_user = user::logged_in()) {
            require_login();
        }

        $capability = $authorized_user->id === $user_id
            ? 'totara/competency:view_own_profile'
            : 'totara/competency:view_other_profile';

        require_capability($capability, context_user::instance($user_id));

        return $user_id;
    }

}