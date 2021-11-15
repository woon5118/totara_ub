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
namespace container_workspace\webapi\resolver\query;

use container_workspace\webapi\middleware\require_workspace_members_access;
use core\orm\pagination\offset_cursor_paginator;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use container_workspace\query\member\non_member_query;
use container_workspace\loader\member\non_member_loader;
use core_container\factory;

/**
 * Query resolver to fetch the cursor of non member users.
 */
final class non_member_users_cursor implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return offset_cursor_paginator
     */
    public static function resolve(array $args, execution_context $ec): offset_cursor_paginator {
        $workspace_id = $args['workspace_id'];

        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(factory::from_id($workspace_id)->get_context());
        }

        $query = new non_member_query($workspace_id);
        if (isset($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        if (isset($args['search_term'])) {
            $query->set_search_term($args['search_term']);
        }

        return non_member_loader::get_non_members($query);
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('container_workspace'),
            new require_workspace_members_access('workspace_id'),
        ];
    }
}
