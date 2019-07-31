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

use core\webapi\execution_context;
use core\webapi\query_resolver;
use container_workspace\workspace as model;
use core_container\factory;
use totara_core\advanced_feature;

/**
 * Resolver for querying the workspace
 */
final class workspace implements query_resolver {
    /**
     * Note that we do not have any permissions check or access check for now.
     *
     * @param array $args
     * @param execution_context $ec
     *
     * @return model
     */
    public static function resolve(array $args, execution_context $ec): model {
        require_login();
        advanced_feature::require('container_workspace');

        /** @var model $workspace */
        $workspace = factory::from_id($args['id']);
        $type = model::get_type();

        if (!$workspace->is_typeof($type)) {
            throw new \coding_exception(
                "Cannot using graphql for fetching workspace to fetch another type of container"
            );
        }

        return $workspace;
    }
}