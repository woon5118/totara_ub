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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package totara_mobile
 */

namespace totara_mobile\webapi\resolver\query;

use context_course;
use core\webapi\execution_context;
use core\webapi\middleware\require_login_course;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use core\orm\query\builder;

final class course implements query_resolver, has_middleware {

    public static function resolve(array $args, execution_context $ec) {
        global $CFG, $OUTPUT;
        require_once($CFG->dirroot . '/course/lib.php');

        // Course visibility and access is already covered in the course_require_login middleware.
        $course = get_course($args['courseid']);
        $course->image = course_get_image($course);

        // Set execution context context.
        $ec->set_relevant_context(context_course::instance($course->id));

        // Get mobile compatibility.
        $mobile_coursecompat = false;
        if (!empty($course->id)) {
            $mobile_coursecompat = (bool) builder::table('totara_mobile_compatible_courses')
                ->where('courseid', $course->id)
                ->count();
        }

        // Mobile image is blank if the course has default image. We have to kind of reverse-engineer this.
        if ($course->image instanceof \moodle_url) {
            $course->image = $course->image->out();
        }
        if ($course->image == $OUTPUT->image_url('course_defaultimage', 'moodle')) {
            $mobile_image = "";
        } else {
            $url = false;
            if (get_config('course', 'defaultimage')) {
                $syscontext = \context_system::instance();
                $fs = get_file_storage();
                $files = $fs->get_area_files($syscontext->id, 'course', 'defaultimage', 0, "timemodified DESC", false);
                if ($files) {
                    $file = reset($files);
                    $themerev = theme_get_revision();
                    $url = \moodle_url::make_pluginfile_url(
                        $syscontext->id,
                        'course',
                        'defaultimage',
                        $themerev,
                        '/',
                        $file->get_filename(),
                        false
                    );
                }
            }
            if ($url && $course->image == $url) {
                $mobile_image = "";
            } else {
                $mobile_image = $course->image;
            }
        }

        return ['course' => $course, 'mobile_coursecompat' => $mobile_coursecompat, 'mobile_image' => $mobile_image];
    }

    public static function get_middleware(): array {
        return [
            new require_login_course('courseid')
        ];
    }

}