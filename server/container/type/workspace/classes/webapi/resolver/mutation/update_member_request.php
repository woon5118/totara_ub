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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\webapi\resolver\mutation;

use container_workspace\member\member_request;
use container_workspace\query\member\member_request_status;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;

/**
 * Mutation to update the member request.
 */
final class update_member_request implements mutation_resolver, has_middleware {
    /**
     * Note that this operation either accept the request or decline the member request.
     * For cancelling the member request, it has to go thru another process.
     *
     * @param array $args
     * @param execution_context $ec
     * @return member_request
     */
    public static function resolve(array $args, execution_context $ec): member_request {
        global $USER;

        $member_request = member_request::from_id($args['id']);
        $workspace = $member_request->get_workspace();

        if ($workspace->is_public()) {
            throw new \coding_exception("Cannot update a member request when the workspace is a public");
        }

        $new_status = member_request_status::get_value($args['new_status']);
        if (member_request_status::is_accepted($new_status)) {
            // Accept the request, then update the workspace's member by adding this user to the workspace.
            $member_request->accept($USER->id);
            return $member_request;
        } else if (member_request_status::is_declined($new_status)) {
            // Decline the request.
            $member_request->decline($USER->id);
            return $member_request;
        }

        throw new \coding_exception("Invalid new status for member request");
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('container_workspace')
        ];
    }
}