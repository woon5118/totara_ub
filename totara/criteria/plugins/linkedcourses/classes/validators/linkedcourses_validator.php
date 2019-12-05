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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace criteria_coursecompletion\validators;

global $CFG;
require_once($CFG->dirroot . '/lib/completionlib.php');

use completion_info;
use totara_criteria\criterion;
use totara_competency\entities\course as course_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\entities\criteria_item as criteria_item_entity;
use totara_criteria\validators\criterion_validator_interface;


/**
 * Validator for linkedcourses criteria
 */
class linkedcourses_validator implements criterion_validator_interface {

    /**
     * Validate all items of courses linked to the competency
     * @param criterion $criterion
     * @return bool
     */
    public static function validate_and_set_status(criterion $criterion): int {
        // Assuming here that the courses linked to the competency
        $item_courses = criteria_item_entity::repository()
            ->as('ci')
            ->left_join([course_entity::TABLE, 'c'], 'ci.item_id', 'c.id')
            ->select('ci.item_id')
            ->add_select('c.id as course_id')
            ->where('ci.criterion_id', $criterion->get_id())
            ->where('ci.item_type', 'course')
            ->where('c.enablecompletion', 1)
            ->get()
            ->to_array();

        $num_courses = count($item_courses);
        $valid_courses = array_filter($item_courses, function ($item_course) {
            return !is_null($item_course['course_id']);
        });

        $is_valid = completion_info::is_enabled_for_site()
            && $num_courses == count($valid_courses)
            && $num_courses >= $criterion->get_aggregation_num_required();

        $new_status = $is_valid ? criterion::STATUS_VALID : criterion::STATUS_INVALID;
        if ($is_valid != $criterion->is_valid()) {
            $criterion->set_status($new_status);
        }

        return $new_status;
    }

}
