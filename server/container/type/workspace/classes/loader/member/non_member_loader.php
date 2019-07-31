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
        global $CFG, $DB;

        $builder = builder::table('user', 'u');
        $builder->select_raw('DISTINCT u.*');

        $workspace_id = $query->get_workspace_id();

        $builder->left_join(
            ['user_enrolments', 'ue'],
            function (builder $join): void {
                $join->where_field('ue.userid', 'u.id');
                $join->where('status', status::get_active());
            }
        );

        $builder->left_join(
            ['enrol', 'e'],
            function (builder $join) use ($workspace_id): void {
                $join->where_field('e.id', 'ue.enrolid');
                $join->where('e.courseid', $workspace_id);
            }
        );

        // We are only fetching non member of a specific workspace.
        $builder->where_null('ue.id');

        $search_term = $query->get_search_term();
        if (null !== $search_term) {
            $first_name_like = $DB->sql_like('u.firstname', ':first_name');
            $last_name_like = $DB->sql_like('u.lastname', ':last_name');
            $email_like = $DB->sql_like('u.email', ':email');

            $builder->where_raw(
                "({$first_name_like} OR {$last_name_like} OR {$email_like})",
                [
                    'first_name' => "%{$DB->sql_like_escape($search_term)}%",
                    'last_name' => "%{$DB->sql_like_escape($search_term)}%",
                    'email' => "%{$DB->sql_like_escape($search_term)}%"
                ]
            );
        }

        $guest_id = guest_user()->id;
        $builder->where('u.id', '<>', $guest_id);
        $builder->where('u.deleted', 0);
        $builder->where('u.suspended', 0);

        // ================= SQL =====================
        // SELECT u.*
        // FROM phpu_00user "u"
        //      LEFT JOIN phpu_00user_enrolments "ue" ON ue.userid = u.id AND "ue".status = $1
        //      LEFT JOIN phpu_00enrol "e" ON e.id = ue.enrolid AND e.courseid = $2
        // WHERE ue.id IS NULL
        // AND "u".id <> $3
        // AND "u".id <> $4
        // AND "u.id" <> $5
        // LIMIT 20 OFFSET 0
        // =============== End Of SQL ================

        if ($CFG->tenantsenabled) {
            $context = \context_course::instance($workspace_id);
            $tenant_id = $context->tenantid;

            if (null !== $tenant_id) {
                // This workspace exists within a tenant - hence only looking for tenants member.
                $cohort_id = $DB->get_field('tenant', 'cohortid', ['id' =>$tenant_id], MUST_EXIST);

                $builder->join(['cohort_members', 'cm'], 'u.id', 'cm.userid');
                $builder->where('cm.cohortid', $cohort_id);
            } else if ($CFG->tenantsisolated){
                // Non tenant workspace - and isolation mode is on, therefore we are only looking for
                // system users.
                $builder->where_null('u.tenantid');
            }
        }

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }
}