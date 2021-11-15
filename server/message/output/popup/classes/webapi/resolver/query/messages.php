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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package message_popup
 */

namespace message_popup\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_authenticated_user;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use message_popup\api;

final class messages implements query_resolver, has_middleware {

    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        $messages = api::get_popup_notifications($USER->id, 'DESC', 30, 0);

        return $messages;
    }

    public static function get_middleware(): array {
        return [
            require_authenticated_user::class
        ];
    }

}
