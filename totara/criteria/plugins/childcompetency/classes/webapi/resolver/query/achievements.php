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

use context_system;
use context_user;
use core\orm\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use criteria_childcompetency\childcompetency;
use totara_competency\entities\assignment;
use totara_competency\models\assignment_user;
use totara_competency\entities\competency;
use totara_core\advanced_feature;
use totara_criteria\criterion_not_found_exception;

/**
 * Fetches all achievments for the childcompetency criteria type
 */
class achievements implements query_resolver {

    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('competency_assignment');

        $instance_id = $args['instance_id'];
        $user_id = $args['user_id'];
        $assignment_id = $args['assignment_id'];

        $can_assign = static::authorize($user_id);

        $ass_user = new assignment_user($user_id);

        try {
            $completion_criteria = childcompetency::fetch($instance_id);
        } catch (\Exception $exception) {
            throw new criterion_not_found_exception();
        }

        // From assignment ID we need to figure out what's the competency id. From there we need to create
        try {
            $assignment = assignment::repository()
                ->where('id', $assignment_id)
                ->with('competency.children')
                ->one(true);
        } catch (\Exception $exception) {
            throw new criterion_not_found_exception();
        }

        if (!$assignment->competency) {
            return [
                'aggregation_method' => $completion_criteria->get_aggregation_method(),
                'required_items' => $completion_criteria->get_aggregation_params()['req_items'] ?? 1,
                'items' => new collection(),
            ];
        }

        // Let's load achievement values if any, as well as assignment availability
        $assignment->competency->children->load(
            [
                'achievement' => function (repository $repository) use ($user_id, $assignment_id) {
                    $repository->where('user_id', $user_id)
                        ->where('proficient', 1)
                        ->with('value');
                },
                'availability'
            ]
        );

        return [
            'aggregation_method' => $completion_criteria->get_aggregation_method(),
            'required_items' => $completion_criteria->get_aggregation_params()['req_items'] ?? 1,
            'current_user' => static::is_for_current_user($user_id),
            'items' => $assignment->competency->children->map(function (competency $competency) use ($can_assign, $ass_user) {
                // We need to figure out whether these competencies are assigned to the current user or not.
                // The cheapest way to do so is to check achievement if a competency has an achievement,
                // well it must be assigned to the user...
                if ($competency->achievement->value ?? false) {
                    $assigned = true;
                } else {
                    $assigned = $ass_user->has_active_assignments($competency->id) ||
                        $ass_user->has_archived_assignments($competency->id);
                }

                return [
                    'competency' => $competency,
                    'value' => $competency->achievement->value ?? null,
                    'assigned' => $assigned,
                    'self_assignable' => static::can_assign($competency, $ass_user->get_id(), $can_assign),
                    'id' => $competency->id,
                ];
            })->key_by('id'),
        ];
    }

    /**
     * Check whether a user is able to assign a competency.
     *
     * @param competency $competency Competency id
     * @param int $user_id User id
     * @param bool $can_assign Permission
     * @return bool
     */
    public static function can_assign(competency $competency, $user_id, $can_assign) {
        if (!$can_assign) {
            return false;
        }

        return in_array(static::is_for_current_user($user_id) ? 1 : 2, $competency->availability->pluck('availability'));
    }

    /**
     * Check whether this is for current user
     *
     * @param int $user_id
     * @return bool
     */
    public static function is_for_current_user(int $user_id): bool {
        if ($user_id <= 0) {
            return false;
        }

        return $user_id === intval($GLOBALS['USER']->id ?? -1);
    }

    /**
     * Check whether a user can "self-assign" competencies
     *
     * @param int $user_id
     * @return bool
     */
    public static function can_assign_competencies(int $user_id) {
        return static::is_for_current_user($user_id) ?
            has_capability('totara/competency:assign_self', context_user::instance($user_id)) :
            has_capability('totara/competency:assign_other', context_user::instance($user_id));
    }

    /**
     * Authorize a user to make sure he can list and optionally self-assign competencies
     *
     * @param int $user_id
     * @return bool
     */
    public static function authorize(int $user_id): bool {
        require_login(null, false, null, false, true);

        $capability = static::is_for_current_user($user_id)
            ? 'totara/competency:view_own_profile'
            : 'totara/competency:view_other_profile';
        require_capability($capability, context_user::instance($user_id));

        return static::can_assign_competencies($user_id);
    }

}
