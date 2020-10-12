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

use container_workspace\member\status;
use container_workspace\query\member\non_member_query;
use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use core\tenant_orm_helper;

/**
 * A loader classes to load users that are not a member of a workspace.
 */
final class non_member_loader {
    /**
     * non_member_loader constructor.
     * Preventing this class from being constructed.
     */
    private function __construct() {
    }

    /**
     * @param non_member_query $query
     * @return offset_cursor_paginator
     */
    public static function get_non_members(non_member_query $query): offset_cursor_paginator {
        global $CFG;

        $workspace_id = $query->get_workspace_id();

        // Create a query of all enrollments in the workspace
        $members_query = builder::table('user_enrolments', 'ue');
        $members_query->select('ue.id');
        $members_query->where_field('ue.userid', 'u.id');
        $members_query->where('status', status::get_active());
        $members_query->join(['enrol', 'e'], 'ue.enrolid', 'e.id');
        $members_query->where('e.courseid', $workspace_id);

        // Select all users excluding those who have a membership
        $builder = builder::table('user', 'u');
        $builder->select_raw('DISTINCT u.*');
        $builder->where_not_exists($members_query);

        $search_term = $query->get_search_term();
        if (null !== $search_term) {
            require_once("{$CFG->dirroot}/totara/core/searchlib.php");
            $keywords = totara_search_parse_keywords($search_term);
            [$sql_search, $parameters] = totara_search_get_keyword_where_clause(
                $keywords,
                ['u.firstname', 'u.lastname', 'u.email'],
                SQL_PARAMS_NAMED
            );

            $builder->where_raw($sql_search, $parameters);
        }

        $guest_id = guest_user()->id;
        $builder->where('u.id', '<>', $guest_id);
        $builder->where('u.deleted', 0);
        $builder->where('u.suspended', 0);

        // ================= SQL =====================
        // SELECT DISTINCT u.*
        // FROM phpunit_00user "u"
        // WHERE NOT EXISTS(
        //      SELECT ue.id
        //      FROM phpunit_00user_enrolments "ue"
        //      INNER JOIN phpunit_00enrol "e" ON ue.enrolid = e.id
        //      WHERE ue.userid = u.id
        //      AND "ue".status = $1
        //      AND e.courseid = $2
        // )
        // AND u.id <> $3
        // AND u.deleted = $4
        // AND u.suspended = $5
        // AND EXISTS(
        //      SELECT "mtru5f988f6287dd1".*
        //      FROM phpunit_00cohort_members "mtru5f988f6287dd1"
        //      INNER JOIN phpunit_00tenant "t" ON "mtru5f988f6287dd1".cohortid = "t".cohortid
        //      WHERE t.id = $6
        //      AND "mtru5f988f6287dd1".userid = u.id
        // )
        // ORDER BY u.id ASC
        // LIMIT 20 OFFSET 0
        // =============== End Of SQL ================

        // Apply tenant query.
        $context = \context_course::instance($workspace_id);
        tenant_orm_helper::restrict_users(
            $builder,
            'u.id',
            $context
        );

        $builder->order_by('u.id');
        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }
}