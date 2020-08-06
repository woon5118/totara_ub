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
use Exception;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\userdata\traits\purge_trait;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

class purge_other_responses extends item {
    use purge_trait;

    /**
     * Execute user data purging for this item.
     * @param target_user $user
     * @param context $context restriction for purging e.g., system context for everything, course context for purging one course
     * @return int result self::RESULT_STATUS_SUCCESS, self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function purge(target_user $user, context $context): int {
        global $DB;

        try {
            $DB->transaction(function () use ($DB, $user, $context) {
                (subject_instance::repository())
                    ->filter_by_context($context)
                    ->filter_by_subject_user($user->id)
                    ->get()
                    ->map(function ($subject_instance) {
                        $track_user_assignment_id = $subject_instance->track_user_assignment_id;
                        $track_user_assignment = new track_user_assignment($track_user_assignment_id);
                        $track_user_assignment->deleted = 1;
                        $track_user_assignment->save();

                        // Delete cascades to include participant instances and responses etc.
                        $subject_instance->delete();
                    });
            });
        } catch (Exception $e) {
            return self::RESULT_STATUS_ERROR;
        }

        return self::RESULT_STATUS_SUCCESS;
    }

    /**
     * Count user data for this item.
     * @param target_user $user
     * @param context $context restriction for counting i.e., system context for everything and course context for course data
     * @return int amount of data or negative integer status code (self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED)
     */
    protected static function count(target_user $user, \context $context): int {
        return subject_instance::repository()
            ->filter_by_context($context)
            ->filter_by_subject_user($user->id)
            ->count();
    }
}