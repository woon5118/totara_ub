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

use container_workspace\exception\workspace_exception;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use container_workspace\workspace as model;
use core\webapi\resolver\has_middleware;
use core_container\factory;
use container_workspace\interactor\workspace\interactor as workspace_interactor;

/**
 * Resolver for querying the workspace
 */
final class workspace implements query_resolver, has_middleware {
    /**
     * Note that we do not have any permissions check or access check for now.
     *
     * @param array $args
     * @param execution_context $ec
     *
     * @return model
     */
    public static function resolve(array $args, execution_context $ec): model {
        global $USER;

        /** @var model $workspace */
        $workspace = factory::from_id($args['id']);
        $type = model::get_type();

        if (!$workspace->is_typeof($type)) {
            throw new \coding_exception(
                "Cannot using graphql for fetching workspace to fetch another type of container"
            );
        }

        $workspace_interactor = new workspace_interactor($workspace, $USER->id);
        if (!$workspace_interactor->can_view_workspace()) {
            throw workspace_exception::on_view();
        }

        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context($workspace->get_context());
        }

        return $workspace;
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