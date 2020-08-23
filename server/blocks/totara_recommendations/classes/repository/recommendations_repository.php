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
 * @package block_totara_recommendations
 */

namespace block_totara_recommendations\repository;

use core\entity\enrol;
use core\orm\query\builder;
use core\orm\query\table;
use totara_engage\access\access;
use totara_engage\timeview\time_view;

/**
 * Repo to provide interaction events
 *
 * @package block_totara_recently_viewed\repository
 */
final class recommendations_repository {
    /**
     * Fetch the micro-learning items for the provided user.
     *
     * @param int $max_count
     * @param int|null $user_id
     * @return array|\stdClass[]
     */
    public static function get_recommended_micro_learning(int $max_count, int $user_id = null): array {
        global $USER;
        if (!$user_id) {
            $user_id = $USER->id;
        }

        $builder = static::get_base_builder();

        $builder->join(['engage_resource', 'er'], 'er.id', 'ru.item_id');
        $builder->join(['engage_article', 'ea'], 'ea.id', 'er.instanceid');

        $builder->where('ea.timeview', time_view::LESS_THAN_FIVE);
        $builder->where('er.access', access::PUBLIC);
        $builder->where('ru.user_id', $user_id);

        $builder->where('ru.component', 'engage_article');

        $builder->order_by_raw('ru.score DESC, ru.time_created DESC');
        $builder->limit($max_count);

        return $builder->fetch();
    }

    /**
     * @param int $max_count
     * @param int|null $user_id
     * @return array
     */
    public static function get_recommended_courses(int $max_count, int $user_id = null): array {
        global $USER;

        if (!$user_id) {
            $user_id = $USER->id;
        }

        return static::get_recommended_container($max_count, $user_id, 'container_course');
    }

    /**
     * @param int $max_count
     * @param int|null $user_id
     * @return array
     */
    public static function get_recommended_workspaces(int $max_count, int $user_id = null): array {
        global $USER;

        if (!$user_id) {
            $user_id = $USER->id;
        }

        return static::get_recommended_container($max_count, $user_id, 'container_workspace');
    }

    /**
     * @param int $max_count
     * @param int $user_id
     * @param string $container_type
     * @return array
     */
    private static function get_recommended_container(int $max_count, int $user_id, string $container_type): array {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $builder = static::get_base_builder();
        $builder->join(['course', 'c'], function (builder $joining) use ($container_type) {
            $joining->where_raw('c.id = ru.item_id')
                ->where('ru.component', $container_type)
                ->where_raw('(c.containertype = ru.component OR c.containertype IS NULL)');
        });

        $builder->where('ru.user_id', $user_id);

        // Exclude courses that don't have self-enrollment enabled
        $builder->join([enrol::TABLE, 'e'], function (builder $joining) {
            $joining->where_raw('c.id = e.courseid')
                ->where('e.enrol', 'self')
                ->where('e.status', ENROL_INSTANCE_ENABLED);
        });

        // We have to exclude courses already enrolled in
        $sub_query = builder::table('course', 'c2');
        $sub_query->select('c2.id');
        $sub_query->join(['enrol', 'e'], 'c2.id', 'e.courseid');
        $sub_query->join(['user_enrolments', 'ue'], 'e.id', 'ue.enrolid');
        $sub_query->where('ue.userid', $user_id);

        $table = new table($sub_query);
        $table->as('jc');

        $builder->left_join($table, 'c.id', 'jc.id');
        $builder->where_null('jc.id');

        $builder->order_by_raw('ru.time_created DESC');
        $builder->limit($max_count);

        return $builder->fetch();
    }

    /**
     * @return builder
     */
    private static function get_base_builder(): builder {
        $builder = builder::table('ml_recommender_users', 'ru');
        $builder->select([
            'ru.unique_id',
            'ru.item_id',
            'ru.component',
            'ru.area',
        ]);

        return $builder;
    }
}