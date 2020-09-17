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
namespace container_workspace\loader\member;

use container_workspace\entity\workspace;
use container_workspace\query\member\query;
use container_workspace\query\member\sort;
use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use core\orm\query\order;
use core\orm\query\raw_field;
use container_workspace\member\member;
use container_workspace\member\status;

/**
 * Loader for members within a workspace
 */
final class loader {
    /**
     * Preventing this class from being constructed.
     * member_loader constructor.
     */
    private function __construct() {
    }

    /**
     * @param query $query
     * @return offset_cursor_paginator
     */
    public static function get_members(query $query): offset_cursor_paginator {
        global $CFG, $DB;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $workspace_id = $query->get_workspace_id();
        $builder = builder::table('user_enrolments', 'ue');

        $builder->join(['enrol', 'e'], 'ue.enrolid', 'e.id');
        $builder->join(
            ['user', 'u'],
            function(builder $join): void {
                $join->where_field('ue.userid', 'u.id');
                $join->where('u.deleted', 0);
                $join->where('u.confirmed', 1);
            }
        );

        $builder->left_join(
            [workspace::TABLE, 'w'],
            function (builder $join): void {
                $join->where_field('u.id', 'w.user_id');
                $join->where_field('w.course_id', 'e.courseid');
            }
        );

        $builder->where('e.courseid', $workspace_id);
        $builder->map_to([member::class, 'from_record']);

        $builder->select([
            "ue.*",
            new raw_field("e.courseid as workspace_id")
        ]);

        $status = $query->get_member_status();
        if (null !== $status) {
            if (status::is_active($status)) {
                $builder->where('ue.status', ENROL_USER_ACTIVE);
            } else if (status::is_suspended($status)) {
                $builder->where('ue.status', ENROL_USER_SUSPENDED);
            }
        }

        $search_term = $query->get_search_term();
        if (null !== $search_term && '' !== $search_term) {
            require_once("{$CFG->dirroot}/totara/core/searchlib.php");
            $user_name_fields = get_all_user_name_fields(false, null, 'u.');
            $keywords = totara_search_parse_keywords($search_term);

            [$like_sql, $like_parameters] = totara_search_get_keyword_where_clause(
                $keywords,
                array_values($user_name_fields),
                SQL_PARAMS_NAMED
            );

            $builder->where_raw($like_sql, $like_parameters);
        }

        // By default we always include the user owner on top.
        if ($DB->get_dbfamily() == 'postgres') {
            $builder->order_by('w.user_id', order::DIRECTION_ASC);
        } else {
            // We treat mssql differently here, and it is a really UGLY hack because
            // we are at high level API and we still have to do this check.
            // However, based on https://www.sqlservertutorial.net/sql-server-basics/sql-server-order-by
            // NULL value is treated as the lowest value within MSSQL database, hence we will have to order by
            // descending so that we can have owner as top.
            // Same stuff with mysql family.
            $builder->order_by('w.user_id', order::DIRECTION_DESC);
        }

        $sort = $query->get_sort();
        if (sort::is_name($sort)) {
            $builder->order_by('u.firstname');
            $builder->order_by('u.lastname');
        } else if (sort::is_recent_join($sort)) {
            $builder->order_by('ue.timemodified', order::DIRECTION_DESC);
        }

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * If null is returned, meaning that the user had not yet been a member of a workspace.
     *
     * @param int $user_id
     * @param int $workspace_id
     *
     * @return member|null
     */
    public static function get_for_user(int $user_id, int $workspace_id): ?member {
        try {
            return member::from_user($user_id, $workspace_id);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Count the number of actively enrolled members in this workspace
     *
     * @param int $workspace_id
     * @return int
     */
    public static function count_members(int $workspace_id): int {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $builder = builder::table('user_enrolments', 'ue');

        $builder->join(['enrol', 'e'], 'ue.enrolid', 'e.id');
        $builder->join(['user', 'u'], 'ue.userid', 'u.id');

        $builder->where('e.courseid', $workspace_id);
        $builder->where('ue.status', ENROL_USER_ACTIVE);

        $builder->select([
            "ue.enrolid",
        ]);

        return $builder->count();
    }
}