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

use container_workspace\interactor\workspace\interactor;
use container_workspace\workspace;
use context_user;
use core\entity\user;
use core\entity\user_repository;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use core_container\factory;
use moodle_exception;

/**
 * Class search_users
 * @package container_workspace\webapi\resolver\query
 */
final class search_users implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return \stdClass[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;
        $workspace_id = $args['workspace_id'];

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new moodle_exception('invalid_access', 'container_workspace');
        }

        $actor_interactor = new interactor($workspace, $USER->id);
        if (!$actor_interactor->can_manage()) {
            throw new moodle_exception('invalid_access', 'container_workspace');
        }

        $context = $workspace->get_context();
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context($context);
        }

        $pattern = $args['pattern'] ?? '';

        $users = user_repository::search($context, $pattern, 20);

        $result_records = [];
        $current_owner_id = $workspace->get_user_id();

        foreach ($users as $user) {
            if ($user->id == $current_owner_id) {
                // We will skip the current owner for now.
                continue;
            }

            $result_records[$user->id] = $user;
        }

        if (empty($pattern) && $current_owner_id != $USER->id && !isset($result_records[$user->id])) {
            // Actor is not an owner and we are not looking for specific users.
            // Hence add the current user as an option. This is happening because
            // we want to make this actor's record available for the list of options.
            $actor = user::repository()->find_or_fail($USER->id);
            array_unshift($result_records, $actor);
        }

        return array_values($result_records);
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