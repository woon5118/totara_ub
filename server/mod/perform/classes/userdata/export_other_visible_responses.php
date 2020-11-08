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
use mod_perform\entity\activity\element_response;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\respondable_element_plugin;
use mod_perform\userdata\traits\export_trait;
use totara_userdata\userdata\export;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

class export_other_visible_responses extends item {
    use export_trait;

    /**
     * Count user data for this item.
     * @param target_user $user
     * @param context $context restriction for counting i.e., system context for everything and course context for course data
     * @return int amount of data or negative integer status code (self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED)
     */
    protected static function count(target_user $user, context $context): int {
        return (element_response::repository())
            ->filter_for_export()
            ->filter_by_context($context)
            ->filter_by_subject_for_export($user->id)
            ->filter_by_subject_can_view($user->id)
            ->count();
    }

    /**
     * Export user data from this item.
     * @param target_user $user
     * @param context $context restriction for exporting i.e., system context for everything and course context for course export
     * @return export|int result object or integer error code self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function export(target_user $user, context $context) {
        $responses = (element_response::repository())
            ->filter_for_export()
            ->filter_by_context($context)
            ->filter_by_subject_for_export($user->id)
            ->filter_by_subject_can_view($user->id)
            ->get(true)
            ->map(function ($response) use ($user) {
                return self::process_response_record($response, $user->id);
            })
            ->to_array();

        $export = new export();
        $export->data = $responses;
        return $export;
    }
}