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

use context_user;
use core\webapi\query_resolver;
use core\entities\user;
use totara_core\advanced_feature;

abstract class profile_resolver implements query_resolver {

    /**
     * Authorize given user, returns user id, or throws an exception if the user is not authorized
     *
     * @param int|null $user_id
     * @return int|null
     */
    public static function authorize(int $user_id = null) {
        if (is_null($user_id)) {
            throw new \coding_exception('User id is required');
        }

        require_login();

        $authorized_user = user::logged_in();

        advanced_feature::require('competency_assignment');

        $capability = $authorized_user->id === $user_id
            ? 'totara/competency:view_own_profile'
            : 'totara/competency:view_other_profile';

        require_capability($capability, context_user::instance($user_id));

        return $user_id;
    }

}