<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTDvs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author  Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\query_resolver;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the "mod_perform_user_grouping_users" GraphQL query.
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class user_grouping_users implements query_resolver {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        // TODO: not sure if this is correct.
        require_login();
        $ec->set_relevant_context(\context_user::instance($USER->id));

        // TODO: this should defer to a model and not have a direct dependency on
        // a database.
        return user::repository()
            ->set_filters(['visible' => true])
            ->get()
            ->map_to(
                function (user $user): \stdClass {
                    return (object) $user->to_array();
                }
            );
    }
}
