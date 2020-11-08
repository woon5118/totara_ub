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
use mod_perform\entity\activity\participant_instance;
use mod_perform\models\activity\subject_instance;
use mod_perform\userdata\traits\purge_trait;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

class purge_user_responses extends item {
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
                participant_instance::repository()
                    ->filter_by_context($context)
                    ->filter_by_participant_user($user->id)->get()->map(function ($participant_instance) {
                        $subject_instance_model = subject_instance::load_by_entity($participant_instance->subject_instance);

                        // Delete cascades to include responses etc.
                        $participant_instance->delete();

                        // Ensure progress is synced following change.
                        $subject_instance_model->update_progress_status();
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
        return participant_instance::repository()
            ->filter_by_context($context)
            ->filter_by_participant_user($user->id)
            ->count();
    }
}