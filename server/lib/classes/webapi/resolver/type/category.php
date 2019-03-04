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

        $category_context = context_coursecat::instance($category->id);
        if (!self::authorize($field, $format, $category_context)) {
            return null;
        }

        $datefields = ['timemodified'];
        if (in_array($field, $datefields) && empty($category->{$field})) {
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

        // Transform the format field from the constants to a core_format string.
        if ($field == 'descriptionformat') {
            switch ($category->descriptionformat) {
                case FORMAT_MOODLE:
                case FORMAT_HTML:
                    return 'HTML';
                    break;
                case FORMAT_PLAIN:
                    return 'PLAIN';
                    break;
                case FORMAT_RAW:
                    return 'RAW';
                    break;
                case FORMAT_MARKDOWN:
                    return 'MARKDOWN';
                    break;
                default:
                    // Note: There is also FORMAT_WIKI but it has been deprecated since 2005.
                    throw new \coding_exception("Unrecognised description format '{$category->descriptionformat}'" );
                    break;
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

        $formatter = new category_formatter($category, $category_context);
        return $formatter->format($field, $format);
    }

    public static function authorize(string $field, ?string $format, context_coursecat $context) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['name']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/category:manage', $context);
        }
        // Permission to see RAW formatted text fields
        if (in_array($field, ['description']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/category:manage', $context);
        }
        return true;
    }
}
