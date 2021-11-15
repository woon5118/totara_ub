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

use container_workspace\member\member_request;
use container_workspace\webapi\middleware\workspace_availability_check;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core_container\factory;
use container_workspace\workspace;

/**
 * Mutation to create a request to join workspace.
 */
final class create_member_request implements mutation_resolver, has_middleware {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return member_request
     */
    public static function resolve(array $args, execution_context $ec): member_request {
        global $USER;
        $workspace_id = $args['workspace_id'];

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);

        if (!$ec->has_relevant_context()) {
            $context = $workspace->get_context();
            $ec->set_relevant_context($context);
        }

        return member_request::create($workspace->get_id(), $USER->id);
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('container_workspace'),
            new workspace_availability_check('workspace_id')
        ];
    }
}