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
 * @package container_workspace
 */

namespace container_workspace\webapi\resolver\query;

use container_workspace\interactor\workspace\category_interactor;
use container_workspace\workspace;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;

/**
 * Query resolver to fetch the workspace interactor
 */
final class workspace_category_interactor implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return category_interactor
     */
    public static function resolve(array $args, execution_context $ec): category_interactor {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_coursecat::instance(workspace::get_default_category_id()));
        }

        $user_id = $USER->id;

        if (isset($args['user_id'])) {
            $user_id = $args['user_id'];
        }

        $category_id = workspace::get_default_category_id();
        if (isset($args['workspace_id'])) {
            // Use the workspace category instead if we know it
            $workspace = workspace::from_id($args['workspace_id']);
            $category_id = $workspace->category;
        }

        return category_interactor::from_category_id($category_id, $user_id);
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