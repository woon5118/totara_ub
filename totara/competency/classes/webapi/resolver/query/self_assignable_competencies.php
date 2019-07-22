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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\webapi\resolver\query;

use context_system;
use core\orm\collection;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use tassign_competency\entities\assignment;
use tassign_competency\entities\competency as competency_entity;
use totara_assignment\entities\user;
use totara_competency\models\self_assignable_competency;

/**
 * Query to return competencies available for self assignment.
 */
class self_assignable_competencies implements query_resolver {

    /**
     * Returns a competency, given its ID.
     *
     * @param array $args
     * @param execution_context $ec
     * @return competency_entity[]|collection
     */
    public static function resolve(array $args, execution_context $ec) {
        self::authorize($args);

        $user_id = $args['user_id'];

        $is_self = $args['user_id'] == user::logged_in()->id;

        $order_by = strtolower($args['order_by'] ?? 'framework_hierarchy');
        $order_dir = strtolower($args['order_dir'] ?? 'asc');
        $filters = $args['filters'] ?? [];
        $limit = $args['limit'] ?? 0;
        $cursor = $args['cursor'] ?? null;

        // By default filter for visible only
        $filters['visible'] = true;

        $repo = competency_entity::repository()
            ->set_filters($filters);

        if ($is_self) {
            $repo->filter_by_self_assignable();
        } else {
            $repo->filter_by_other_assignable();
        }

        /** @var collection $competencies */
        $competencies = $repo
            ->set_filters($filters)
            ->order_by($order_by, $order_dir)
            ->get();

        // TODO This should definitely be extracted somewhere
        // Load all assignments for the competencies which belong to the user
        $assignments = assignment::repository()
            ->join(['totara_assignment_competency_users', 'ua'], 'id', 'assignment_id')
            ->where('ua.user_id', $user_id)
            ->where('status', assignment::STATUS_ACTIVE)
            ->where('competency_id', $competencies->pluck('id'))
            ->get();

        /** @var collection|self_assignable_competency[] $competencies */
        $competencies = $competencies ->transform(function ($item) {
            return self_assignable_competency::load_by_entity($item);
        });

        // Now combine competencies and the user assignments
        if ($assignments->count() > 0) {
            foreach ($competencies as $competency) {
                $user_assignments = $assignments->filter('competency_id', $competency->get_id());
                if ($user_assignments->count() > 0) {
                    $user_assignments->transform(function ($assignment_entity) {
                        return \tassign_competency\models\assignment::load_by_entity($assignment_entity);
                    });
                    $competency->set_user_assignments($user_assignments);
                }
            }
        }

        $result = [
            'items' => $competencies->all(),
            'total_count' => $competencies->count(),
            'page_info' => ['next_cursor' => 'dssd']
        ];

        return $result;
    }

    protected static function authorize(array $args) {
        require_login();

        require_capability('totara/hierarchy:viewcompetency', context_system::instance());

        if ($args['user_id'] == user::logged_in()->id) {
            require_capability('tassign/competency:assignself', context_system::instance());
        } else {
            $context = \context_user::instance($args['user_id']);
            require_capability('tassign/competency:assignother', $context);
        }
    }

}