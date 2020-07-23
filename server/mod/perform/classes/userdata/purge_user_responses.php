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

namespace mod_perform\userdata;

use context;
use mod_perform\userdata\traits\user_responses;
use totara_userdata\userdata\export;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

class purge_user_responses extends item {

    use user_responses;

    /**
     * Can user data of this item data be purged from system?
     * @param int $userstatus target_user::STATUS_ACTIVE, target_user::STATUS_DELETED or target_user::STATUS_SUSPENDED
     * @return bool
     */
    public static function is_purgeable(int $userstatus): bool {
        return true;
    }

    /**
     * Can user data of this item be exported from the system?
     * @return bool
     */
    public static function is_exportable(): bool {
        return false;
    }

    /**
     * Execute user data purging for this item.
     * @param target_user $user
     * @param context $context restriction for purging e.g., system context for everything, course context for purging one course
     * @return int result self::RESULT_STATUS_SUCCESS, self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function purge(target_user $user, context $context): int {
        global $DB;

        $join = self::get_activities_join($context, 'perform', 'ps.activity_id', 'p');

        $sql = "SELECT per.* 
                  FROM {perform_element_response} per
                  JOIN {perform_participant_instance} ppi ON ppi.id = per.participant_instance_id
                  JOIN {perform_section_element} pse ON pse.id = per.section_element_id
                  JOIN {perform_subject_instance} psi ON psi.id = ppi.subject_instance_id
                  JOIN {perform_element} pe ON pe.id = pse.element_id
                  JOIN {perform_section} ps ON ps.id = pse.section_id
                 $join
                WHERE ppi.participant_id = :puserid";
        $params = ['puserid' => $user->id];
        $records = $DB->get_recordset_sql($sql, $params);
        if ($records->valid()) {
            foreach ($records as $record) {
                $record->response_data = '{}';
                $DB->update_record('perform_element_response', $record);
            }
            $records->close();
        } else {
            return self::RESULT_STATUS_ERROR;
        }
        return self::RESULT_STATUS_SUCCESS;
    }
}