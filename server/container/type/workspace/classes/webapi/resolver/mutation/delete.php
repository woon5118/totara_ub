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
namespace container_workspace\webapi\resolver\mutation;

use container_workspace\local\workspace_helper;
use container_workspace\tracker\tracker;
use container_workspace\workspace;
use core\notification;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use totara_core\advanced_feature;

/**
 * Resolver for deleting a workspace via graphql.
 */
final class delete implements mutation_resolver {

    /**
     * Mutation resolver.
     *
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(array $args, execution_context $ec): bool {
        global $USER;

        require_login();
        advanced_feature::require('container_workspace');
        $workspace = workspace::from_id($args['workspace_id']);

        $workspace_id = $workspace->get_id();
        // Clear the tracker for navigating the right page.
        tracker::clear_all_for_workspace($workspace_id);

        // Delete the workspace.
        workspace_helper::delete_workspace($workspace, $USER->id);

        // Add deleted workspace to notification.
        notification::success(get_string('notification_deleted', 'container_workspace', $workspace->fullname));
        return true;
    }
}