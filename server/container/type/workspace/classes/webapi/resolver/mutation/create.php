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

use container_workspace\local\workspace_helper;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use container_workspace\workspace;
use totara_core\advanced_feature;

/**
 * Resolver for creating a workspace via graphql.
 */
final class create implements mutation_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return workspace
     */
    public static function resolve(array $args, execution_context $ec): workspace {
        global $USER;
        require_login();
        advanced_feature::require('container_workspace');

        $workspace_name = $args['name'];
        if (empty($workspace_name)) {
            throw new \coding_exception("Cannot create a workspace with an empty name");
        }

        $summary = null;
        if (isset($args['description'])) {
            $summary = $args['description'];
        }

        $summary_format = null;
        if (isset($args['description_format'])) {
            $summary_format = $args['description_format'];
        }

        $draft_id = null;
        if (isset($args['draft_id'])) {
            $draft_id = $args['draft_id'];
        }

        $is_private = $args['private'];
        $is_hidden = $args['hidden'];

        return workspace_helper::create_workspace(
            $workspace_name,
            $USER->id,
            null,
            $summary,
            $summary_format,
            $draft_id,
            $is_private,
            $is_hidden
        );
    }
}