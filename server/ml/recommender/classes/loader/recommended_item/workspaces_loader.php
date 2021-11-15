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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package ml_recommender
 */

namespace ml_recommender\loader\recommended_item;

use container_workspace\workspace;
use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use core\orm\query\table;
use ml_recommender\entity\recommended_item;
use ml_recommender\entity\recommended_user_item;
use ml_recommender\query\recommended_item\item_query;
use ml_recommender\query\recommended_item\user_query;

/**
 * Loader class for a recommended item
 */
final class workspaces_loader extends loader {
    /**
     * Select all related workspaces based on the provided component id
     *
     * @param item_query $query
     * @param int $actor_id
     * @return offset_cursor_paginator
     */
    public static function get_recommended(item_query $query, int $actor_id = 0): offset_cursor_paginator {
        $builder = static::get_base_workspace_query(recommended_item::TABLE, $actor_id);

        $builder->where('r.target_item_id', $query->get_target_item_id());
        $builder->where('r.target_component', $query->get_target_component());
        $builder->where('r.target_area', $query->get_target_area());

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * Select all recommended workspaces for the user
     *
     * @param user_query $query
     * @param int $actor_id
     * @return offset_cursor_paginator
     */
    public static function get_recommended_for_user(user_query $query, int $actor_id = 0): offset_cursor_paginator {
        $builder = static::get_base_workspace_query(recommended_user_item::TABLE, $actor_id);

        $builder->where('r.user_id', $query->get_target_user_id());
        $builder->where('r.component', $query->get_target_component());
        $builder->where('r.area', $query->get_target_area());

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * @param string $table
     * @param int $actor_id
     * @return builder
     */
    private static function get_base_workspace_query(string $table, int $actor_id): builder {
        global $USER, $CFG;
        if (!$actor_id) {
            $actor_id = $USER->id;
        }

        $builder = builder::table($table, 'r');

        // Join against workspaces
        $builder->join(['course', 'c'], 'r.item_id', 'c.id');
        $builder->join(['workspace', 'w'],
            function(builder $join): void {
                $join->where_field('c.id', 'w.course_id');

                // We are filtering out those deleted items.
                $join->where('w.to_be_deleted', 0);
            }
        );

        $builder->select_raw('c.*, w.user_id, w.id AS w_id');
        $builder->where('r.component', 'container_workspace');

        // Exclude private workspaces
        $builder->where('w.private', 0);

        // Exclude enrolled courses
        $sub_query = builder::table('course', 'c2');
        $sub_query->select('c2.id');
        $sub_query->join(['enrol', 'e'], 'c2.id', 'e.courseid');
        $sub_query->join(['user_enrolments', 'ue'], 'e.id', 'ue.enrolid');
        $sub_query->where('ue.userid', $actor_id);

        $table = new table($sub_query);
        $table->as('jc');

        $builder->left_join($table, 'c.id', 'jc.id');
        $builder->where_null('jc.id');

        // Handle hidden/inaccessible workspaces
        require_once("{$CFG->dirroot}/totara/coursecatalog/lib.php");
        [$sql_where, $sql_params] = totara_visibility_where(
            $actor_id,
            'c.id',
            'c.visible',
            'c.audiencevisible',
            'c',
            'course'
        );

        $builder->left_join(
            ['context', 'ctx'],
            function (builder $builder): void {
                $builder->where_field('c.id', 'ctx.instanceid');
                $builder->where('ctx.contextlevel', CONTEXT_COURSE);
            }
        );

        $builder->where_raw($sql_where, $sql_params);

        $builder->order_by_raw('r.score DESC');
        $builder->map_to(
            function(\stdClass $record) {
                return workspace::from_record($record);
            }
        );

        return $builder;
    }
}