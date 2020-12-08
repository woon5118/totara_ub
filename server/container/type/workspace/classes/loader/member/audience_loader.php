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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package container_workspace
 */

namespace container_workspace\loader\member;

use container_workspace\workspace;
use core\entity\cohort_member;
use core\entity\user_enrolment;
use core\orm\query\builder;

/**
 * Loader for members within a workspace
 */
final class audience_loader {

    /**
     * Preventing this class from being constructed.
     * member_loader constructor.
     */
    private function __construct() {
    }

    /**
     * Returns number of users who are members of the given audiences but not yet enrolled in the workspace
     *
     * @param workspace $workspace
     * @param array $audience_ids
     * @return int
     */
    public static function get_bulk_members_to_add(workspace $workspace, array $audience_ids): int {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $builder = builder::table(cohort_member::TABLE)
            ->as('cm')
            ->select_raw('DISTINCT cm.userid')
            ->where_not_exists(
                builder::table(user_enrolment::TABLE)
                    ->as('ue')
                    ->join(['enrol', 'e'], 'enrolid', 'id')
                    ->where('status', ENROL_USER_ACTIVE)
                    ->where('e.courseid', $workspace->id)
                    ->where_field('userid', 'cm.userid')
            )
            ->where('cohortid', $audience_ids);

        $result = $builder->count();

        return $result;
    }

}