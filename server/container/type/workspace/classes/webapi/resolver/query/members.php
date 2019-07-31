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

use container_workspace\member\member;
use container_workspace\loader\member\loader;
use container_workspace\member\status;
use container_workspace\query\member\query;
use container_workspace\query\member\sort;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use core_container\factory;
use container_workspace\workspace;
use totara_core\advanced_feature;

/**
 * Members query resolver
 */
final class members implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return member[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();
        advanced_feature::require('container_workspace');

        $workspace_id = $args['workspace_id'];

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception("Cannot find workspace from id '{$workspace_id}'");
        }

        if (!$ec->has_relevant_context()) {
            $context = $workspace->get_context();
            $ec->set_relevant_context($context);
        }

        $query = new query($workspace->get_id());

        if (isset($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        if (isset($args['status'])) {
            $status_value = status::get_value($args['status']);
            $query->set_member_status($status_value);
        }

        if (isset($args['search_term'])) {
            $query->set_search_term($args['search_term']);
        }

        $sort = sort::get_value($args['sort']);
        $query->set_sort($sort);

        $paginator = loader::get_members($query);
        return $paginator->get_items()->all();
    }
}