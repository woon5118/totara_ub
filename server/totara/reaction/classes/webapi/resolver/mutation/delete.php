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
 * @package totara_reaction
 */
namespace totara_reaction\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use totara_reaction\loader\reaction_loader;
use totara_reaction\reaction_helper;

/**
 * Class delete
 * @package totara_reaction\webapi\resolver\mutation
 */
final class delete implements mutation_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return bool
     */
    public static function resolve(array $args, execution_context $ec): bool {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $component = $args['component'];
        $area = $args['area'];
        $instance_id = $args['instanceid'];

        $reaction = reaction_loader::find_by_parameters(
            $component,
            $area,
            $instance_id,
            $USER->id
        );

        if (null === $reaction) {
            debugging(
                "Cannot find the reaction for component '{$component}' and '{$area}' " .
                "with instanceid '{$instance_id}'",
                DEBUG_DEVELOPER
            );

            return false;
        }

        return reaction_helper::purge_reaction($reaction, $USER->id);
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
        ];
    }

}
