<?php
/*
 * This file is part of Totara Learn
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_criteria\webapi\resolver\query;

use completion_completion;
use context_course;
use context_system;
use context_user;
use core\format;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use course_in_list;
use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\field\text_field_formatter;
use totara_criteria\criterion;
use totara_criteria\criterion_not_found_exception;

/**
 * Fetches all achievements for the course related criteria types
 */
abstract class course_achievements implements query_resolver {

    abstract public static function get_criterion(): criterion;

    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/completion/completion_completion.php');

        require_login(null, false, null, false, true);

        $instance_id = $args['instance_id'];
        $user_id = $args['user_id'];

        // Currently we only use this for competencies, if the criteria is used
        // later in other areas like goals this need to be refactored
        static::is_for_current_user($user_id) ?
            require_capability('totara/competency:view_own_profile', context_system::instance()) :
            require_capability('totara/competency:view_other_profile', context_user::instance($user_id));

        try {
            $criterion = static::get_criterion();
            $completion_criteria = $criterion::fetch($instance_id);
        } catch (\Exception $exception) {
            throw new criterion_not_found_exception();
        }

        $items = [];
        foreach ($completion_criteria->get_item_ids() as $course_id) {
            // TODO Replace following with a proper course type in TL-22396

            $item = [
                'course' => null,
                'progress' => 0
            ];

            try {
                $course_record = get_course($course_id);
            } catch (\Exception $exception) {
                // Course not found for some reason, maybe it got deleted?
                // We don't want to return anything in this case.
                continue;
            }

            if (totara_course_is_viewable($course_record)) {
                $course_context = context_course::instance($course_id);
                $course_in_list = new course_in_list($course_record);

                $completion = new completion_completion(['userid' => $user_id, 'course' => $course_id]);
                $item['progress'] = (int)$completion->get_percentagecomplete();

                $string_formatter = new string_field_formatter(format::FORMAT_HTML, $course_context);

                $text_formatter = new text_field_formatter(format::FORMAT_HTML, $course_context);
                $text_formatter->set_pluginfile_url_options($course_context, 'course', 'summary')
                    ->set_text_format($course_in_list->summaryformat);

                $item['course'] = [
                    'name' => $string_formatter->format(get_course_display_name_for_list($course_in_list)),
                    'summary' => $text_formatter->format($course_in_list->summary),
                    'url' => (string) course_get_url($course_record)
                ];
            }

            $items[] = $item;
        }

        return [
            'aggregation_method' => $completion_criteria->get_aggregation_method(),
            'required_items' => $completion_criteria->get_aggregation_params()['req_items'] ?? 1,
            'items' => $items
        ];
    }

    public static function is_for_current_user(int $user_id): bool {
        global $USER;
        return $user_id > 0 ? $user_id == $USER->id : false;
    }

}
