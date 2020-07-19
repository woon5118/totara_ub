<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb;

class util {

    /**
     * Return SQL and params to apply to a report SQL query in order to filter to only users where the viewing
     * user can manage those user's participation.
     *
     * @param int $report_for User ID of user who is viewing
     * @param string $user_id_field String referencing database column containing user ids to filter.
     * @return array Array containing SQL string and array of params
     */
    public static function get_manage_participation_sql(int $report_for, string $user_id_field) {
        global $DB;

        // If user can manage participation across all users don't do the per-row restriction at all.
        $user_context = \context_user::instance($report_for);
        if (has_capability('mod/perform:manage_all_participation', $user_context, $report_for)) {
            return ['1=1', []];
        }

        $capability = 'mod/perform:manage_subject_user_participation';
        $permitted_users = \mod_perform\util::get_permitted_users($report_for, $capability);

        if (empty($permitted_users)) {
            // No access at all if not permitted to see any users.
            return ['1=0', []];
        }

        // Restrict to specific subject users.
        list($sourcesql, $sourceparams) = $DB->get_in_or_equal($permitted_users, SQL_PARAMS_NAMED);
        $wheresql = "$user_id_field {$sourcesql}";
        $whereparams = $sourceparams;
        return [$wheresql, $whereparams];
    }
}
