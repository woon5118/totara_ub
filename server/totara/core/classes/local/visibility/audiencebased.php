<?php
/*
 * This file is part of Totara LMS
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\local\visibility;

defined('MOODLE_INTERNAL') || die();

use core\dml\sql;

/**
 * Audience based visibility resolver abstract class.
 *
 * Designed to centralise common logic when processing audience based visibility.
 * Importantly, it must conform to the resolver interface.
 *
 * @internal
 */
abstract class audiencebased extends base implements resolver {

    /**
     * Returns the cohort association with this item type.
     *
     * @return int One of COHORT_ASSN_ITEMTYPE_*
     */
    abstract protected function get_cohort_association(): int;

    /**
     * Returns an SQL snippet the resolves whether the user has an assignment on item given its ID field.
     *
     * @param int $userid
     * @param string $field_id
     * @return sql
     */
    abstract protected function sql_user_assignment(int $userid, string $field_id): sql;

    /**
     * @inheritDoc
     * @return string
     */
    public function sql_field_visible(): string {
        return 'audiencevisible';
    }

    /**
     * Returns true if the given user can see all items, regardless of visibility.
     *
     * @param int $userid
     * @return bool
     */
    final protected function can_see_all(int $userid) {
        if (parent::can_see_all($userid)) {
            return true;
        }
        // OK, it is audience based visibility.
        if (has_capability('totara/coursecatalog:manageaudiencevisibility', \context_system::instance(), $userid)) {
            return true;
        }
        return false;
    }

    /**
     * Generates an audience visibility SQL snippet
     *
     * @param int $userid
     * @param string $field_id
     * @param string $field_visible
     * @return sql
     */
    final protected function get_visibility_sql(int $userid, string $field_id, string $field_visible): sql {
        $sql_not_none = new sql("{$field_visible} <> :visible_none", ['visible_none' => COHORT_VISIBLE_NOUSERS]);
        $sql_all = new sql("{$field_visible} = :visible_all", ['visible_all' => COHORT_VISIBLE_ALL]);
        $sql_audience = sql::wrap(
            [
                new sql("{$field_visible} = :visible_audience", ['visible_audience' => COHORT_VISIBLE_AUDIENCE]),
                new sql(
                    "EXISTS (
                            SELECT 1
                              FROM {cohort_visibility} vw_cv
                              JOIN {cohort_members} vw_cm ON vw_cv.cohortid = vw_cm.cohortid
                             WHERE vw_cm.userid = :user
                               AND vw_cv.instanceid = {$field_id}
                               AND vw_cv.instancetype = :cohort_type
                        )",
                    [
                        'user' => $userid,
                        'cohort_type' => $this->get_cohort_association()
                    ]
                )
            ],
            ' AND '
        );
        $sql_enrolled = sql::wrap(
            [
                new sql(
                    "{$field_visible} IN (:audience, :enrolled)",
                    ['audience' => COHORT_VISIBLE_AUDIENCE, 'enrolled' => COHORT_VISIBLE_ENROLLED]
                ),
                $this->sql_user_assignment($userid, $field_id)
            ],
            ' AND '
        );

        return sql::wrap(
            [
                $sql_not_none,
                sql::wrap(
                    [
                        $sql_all,
                        $sql_audience,
                        $sql_enrolled
                    ],
                    ' OR '
                )
            ],
            ' AND '
        );
    }
}