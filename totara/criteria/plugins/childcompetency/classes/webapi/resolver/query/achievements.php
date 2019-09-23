<?php
/*
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package criteria_childcompetency
 */

namespace criteria_childcompetency\webapi\resolver\query;

use completion_completion;
use context_system;
use context_user;
use core\format;
use core\orm\collection;
use core\orm\entity\repository;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use criteria_childcompetency\childcompetency;
use criteria_coursecompletion\coursecompletion;
use tassign_competency\entities\assignment;
use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\field\text_field_formatter;

/**
 * Fetches all achievments for the coursecompletion criteria type
 */
class achievements implements query_resolver {

    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        require_login(null, false, null, false, true);

        $instance_id = $args['instance_id'];
        $user_id = $args['user_id'];
        $assignment_id = $args['assignment_id'];

        static::is_for_current_user($user_id) ?
            require_capability('totara/competency:view_own_profile', context_system::instance()) :
            require_capability('totara/competency:view_other_profile', context_user::instance($user_id));

        $completion_criteria = childcompetency::fetch($instance_id);

        // From assignment ID we need to figure out what's the competency id. From there we need to create

        $assignment = assignment::repository()
            ->where('id', $assignment_id)
            ->with('competency.children')
            ->one(true);


        if (!$assignment->competency) {
            return [
                'aggregation_type' => $completion_criteria->get_aggregation_method(),
                'required_items' => $completion_criteria->get_aggregation_params()['req_items'] ?? 1,
                'items' => new collection([]),
            ];
        }

        // Let's load achievement values if any.
        $assignment->competency->children->load(['achievement' => function(repository $repository) use ($user_id, $assignment_id) {
            $repository->where('user_id', $user_id)
                ->with('value');
        }]);

        return [
            'aggregation_type' => $completion_criteria->get_aggregation_method(),
            'required_items' => $completion_criteria->get_aggregation_params()['req_items'] ?? 1,
            'items' => $assignment->competency->children,
        ];
    }

    public static function is_for_current_user(int $user_id): bool {
        if ($user_id <= 0) {
            return false;
        }

        return $user_id === intval($GLOBALS['USER']->id ?? -1);
    }

}
