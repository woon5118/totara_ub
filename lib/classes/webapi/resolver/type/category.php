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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @author David Curry <david.curry@totaralearning.com>
 * @package core
 */

namespace core\webapi\resolver\type;

use core\formatter\category_formatter;
use core\format;
use coursecat;
use context_coursecat;
use core\webapi\execution_context;
use core\webapi\type_resolver;

class category implements type_resolver {
    public static function resolve(string $field, $category, array $args, execution_context $ec) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $format = $args['format'] ?? null;
        $categorycontext = context_coursecat::instance($category->id);
        $ec->set_relevant_context($categorycontext);

        if (!self::authorize($field, $format, $ec)) {
            return null;
        }

        $datefields = ['timemodified'];
        if (in_array($field, $datefields) && empty($course->{$field})) {
            // Highly unlikely this is set to 1/1/1970, return null for notset dates.
            return null;
        }


        if ($field == 'parent') {
            // Top-level category.
            if (empty($category->parent)) {
                return null;
            } else {
                return coursecat::get($category->parent);
            }
        }

        if ($field == 'children') {
            $cat = coursecat::get($category->id);
            return $cat->get_children();
        }

        if ($field == 'courses') {
            $cat = coursecat::get($category->id);
            $courseids = $cat->get_courses(['idonly' => true]);
            if (empty($courseids)) {
                return [];
            } else {
                list($insql, $inparams) = $DB->get_in_or_equal($courseids);
                $courses = $DB->get_records_select('course', "id {$insql}", $inparams);
                foreach ($courses as $course) {
                    $course->image = course_get_image($course);
                }
                return $courses;
            }
        }

        $formatter = new category_formatter($category, $categorycontext);
        return $formatter->format($field, $format);
    }

    public static function authorize(string $field, ?string $format, execution_context $ec) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['name']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/category:manage', $ec->get_relevant_context());
        }
        // Permission to see RAW formatted text fields
        if (in_array($field, ['description']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/category:manage', $ec->get_relevant_context());
        }
        return true;
    }
}