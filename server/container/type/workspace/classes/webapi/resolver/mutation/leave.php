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

use container_workspace\member\member;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core_container\factory;
use container_workspace\workspace;
use totara_core\advanced_feature;

/**
 * Mutation for user to leave a workspace
 */
final class leave implements mutation_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return member
     */
    public static function resolve(array $args, execution_context $ec): member {
        global $USER;
        require_login();
        advanced_feature::require('container_workspace');

        $workspace = factory::from_id($args['workspace_id']);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception("Invalid container type");
        }

        $workspace_id = $workspace->get_id();
        require_login($workspace_id);

        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context($workspace->get_context());
        }

        $member = member::from_user($USER->id, $workspace_id);
        $member->leave($USER->id);

        return $member;
    }
}