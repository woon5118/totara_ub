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
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_perform
*/

namespace mod_perform\userdata\traits;

use totara_userdata\userdata\target_user;

trait user_responses {

    /**
     * Can user data of this item be somehow counted?
     * How much data is there?
     * @return bool
     */
    public static function is_countable(): bool {
        return true;
    }

    /**
     * Is the given context level compatible with this item?
     * @return array
     */
    public static function get_compatible_context_levels(): array {
        return [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
            CONTEXT_MODULE
        ];
    }

    /**
     * Count user data for this item.
     * @param target_user $user
     * @param context $context restriction for counting i.e., system context for everything and course context for course data
     * @return int amount of data or negative integer status code (self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED)
     */
    protected static function count(target_user $user, \context $context): int {
        global $DB;

        $join = self::get_activities_join($context, 'perform', 'ps.activity_id', 'p');

        $sql = "SELECT COUNT(per.id) 
                  FROM {perform_element_response} per
                  JOIN {perform_participant_instance} ppi ON ppi.id = per.participant_instance_id
                  JOIN {perform_section_element} pse ON pse.id = per.section_element_id
                  JOIN {perform_subject_instance} psi ON psi.id = ppi.subject_instance_id
                  JOIN {perform_element} pe ON pe.id = pse.element_id
                  JOIN {perform_section} ps ON ps.id = pse.section_id
                 $join
                 WHERE ppi.participant_id = :puserid";
        $params = ['puserid' => $user->id];
        return $DB->count_records_sql($sql, $params);
    }
}