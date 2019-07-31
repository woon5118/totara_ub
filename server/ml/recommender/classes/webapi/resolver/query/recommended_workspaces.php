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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package ml_recommender
 */

namespace ml_recommender\webapi\resolver\query;

use container_workspace\workspace;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use ml_recommender\loader\recommended_item\workspaces_loader;
use ml_recommender\query\recommended_item\item_query;
use totara_core\advanced_feature;

/**
 * Recommended workspaces Graphql
 *
 * @package ml_recommender\webapi\resolver\query
 */
final class recommended_workspaces implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();
        if (advanced_feature::is_disabled('ml_recommender')) {
            return [];
        }

        // Firstly, find our target workspace
        $target_workspace = workspace::from_id($args['workspace_id']);
        if (!$target_workspace) {
            throw new \coding_exception("Could not find the target container_workspace to recommend against");
        }

        // Build our query
        $query = new item_query($target_workspace->get_id(), $target_workspace::get_type());

        if (isset($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        // Load the interaction items
        $paginator = workspaces_loader::get_recommended_workspaces($query);
        return $paginator->get_items()->all();
    }
}