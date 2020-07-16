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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use context_coursecat;
use core\entities\user;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use core_user\access_controller;
use mod_perform\util;

/**
 * Get the users that the current user can see and can select.
 *
 * @package mod_perform\webapi\resolver\query
 */
class selectable_users implements query_resolver, has_middleware {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $ec->set_relevant_context(context_coursecat::instance(util::get_default_category_id()));

        return user::repository()
            ->filter_by_not_deleted()
            ->filter_by_not_guest()
            ->select_user_picture_fields()
            ->select_full_name_fields_only()
            ->order_by_full_name()
            ->get()
            ->filter(static function (user $user) {
                return access_controller::for($user->get_record())
                    ->can_view_field('fullname');
            });
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
