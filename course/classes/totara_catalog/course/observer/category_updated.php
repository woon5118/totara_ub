<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package core_course
 * @category totara_catalog
 */

namespace core_course\totara_catalog\course\observer;

defined('MOODLE_INTERNAL') || die();

use totara_catalog\observer\object_update_observer;

/**
 * update catalog data for all courses in the updated category id
 *
 */
class category_updated extends object_update_observer {

    public function get_observer_events(): array {
        return [
            '\core\event\course_category_updated'
        ];
    }

    /**
     * init all course update objects for updated category id
     */
    protected function init_change_objects(): void {
        global $DB;
        $pathconcat = $DB->sql_concat('path_categories.path', ":course_category_hierarchy_p");
        $targetconcat = $DB->sql_concat('target_category.path', ":course_category_hierarchy_t");
        $like = $DB->sql_like($targetconcat, $pathconcat);

        $sql = "SELECT course.id
                  FROM {course} course
                 WHERE course.category IN (
                        SELECT target_category.id AS categoryid
                          FROM {course_categories} target_category
                          JOIN {course_categories} path_categories
                            ON {$like} AND path_categories.id = :path_cat_id
                         GROUP BY target_category.id
                    ) ";
        $params = [
            'course_category_hierarchy_p' => '/%',
            'course_category_hierarchy_t' => '/',
            'path_cat_id'                 => $this->event->objectid,
        ];
        $changecourses = $DB->get_records_sql($sql, $params);

        foreach ($changecourses as $course) {
            $data = new \stdClass();
            $data->objectid = $course->id;
            $data->contextid = $this->event->contextid;
            $this->register_for_update($data);
        }
    }
}
