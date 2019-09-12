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
 * @package core
 */

namespace core\webapi\resolver\type;

use core\webapi\execution_context;
use core_course\formatter\course_formatter;
use core\format;
use core\webapi\type_resolver;
use context_course;
use coursecat;

class course implements type_resolver {
    public static function resolve(string $field, $course, array $args, execution_context $ec) {
        global $USER;

        // Note: There's no good way of checking course object since it returns a db object.
        if (!$course instanceof \stdClass) {
            throw new \coding_exception('Only course objects are accepted: ' . gettype($course));
        }

        $format = $args['format'] ?? null;
        if (!$coursecontext = context_course::instance($course->id, IGNORE_MISSING)) {
            // If there is no matching context we have a bad object, ignore missing so we can do our own error.
            throw new \coding_exception('Only valid course objects are accepted');
        }
        $ec->set_relevant_context($coursecontext);

        if (!self::authorize($field, $format, $coursecontext)) {
            return null;
        }

        if (empty($course->icon)) {
            $course->icon = 'default';
        }

        $datefields = ['timecreated', 'timemodified', 'startdate', 'enddate'];
        if (in_array($field, $datefields) && empty($course->{$field})) {
            // Highly unlikely this is set to 1/1/1970, return null for notset dates.
            return null;
        }

        if ($field == 'categoryid') {
            return $course->category;
        }

        if ($field == 'category') {
            // Front page course
            if ($course->category == 0) {
                return null;
            } else {
                return coursecat::get($course->category);
            }
        }

        if ($field == 'image') {
            if ($course->image instanceof \moodle_url) {
                return $course->image = $course->image->out();
            } else {
                return $course->image;
            }
        }

        if ($field == 'sections') {
            // Note: Most of this cminfo stuff is cached for speed.
            return \course_modinfo::instance($course->id, $USER->id)->get_section_info_all();
        }

        $formatter = new course_formatter($course, $coursecontext);
        return $formatter->format($field, $format);
    }

    public static function authorize(string $field, ?string $format, context_course $ec) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['shortname', 'fullname']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/course:update', $ec);
        }
        // Permission to see RAW formatted text fields
        if (in_array($field, ['summary']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/course:update', $ec);
        }
        return true;
    }

}
