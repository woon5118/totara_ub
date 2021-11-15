<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\webapi\resolver\query;

use container_workspace\interactor\workspace\interactor;
use container_workspace\loader\file\loader;
use container_workspace\query\file\query;
use container_workspace\query\file\sort;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use core_container\factory;
use container_workspace\workspace;

/**
 * Query resolver for all the files
 */
final class files implements query_resolver, has_middleware {

    /**
     * Query resolver.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        $workspace_id = $args['workspace_id'];
        $workspace = factory::from_id($workspace_id);

        if (!$workspace->is_typeof(workspace::get_type())) {
            // Prevent the course's id being passed in.
            throw new \coding_exception("Cannot find the workspace based on '{$workspace_id}'");
        }

        if (!$ec->has_relevant_context()) {
            $context = $workspace->get_context();
            $ec->set_relevant_context($context);
        }

        $interactor = new interactor($workspace);
        if (!$interactor->can_view_discussions()) {
            throw new \moodle_exception('invalid_access', 'container_workspace');
        }

        $query = new query($workspace->get_id());

        if (isset($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        $sort_value = sort::get_value($args['sort']);
        $query->set_sort($sort_value);

        if (!empty($args['extension'])) {
            $extension = $args['extension'];
            $query->set_extension($extension);
        }

        $paginator = loader::get_files($query);
        return  $paginator->get_items()->all();
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('container_workspace'),
        ];
    }
}
