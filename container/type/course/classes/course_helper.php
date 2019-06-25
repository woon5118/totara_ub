<?php
/*
 * This file is part of Totara LMS
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_course
 */
namespace container_course;

use core_container\factory;

final class course_helper {
    /**
     * Keeping track of which module has been warned or not
     * Array<string, int>
     * @var array
     */
    private static $warned;

    /**
     * Preventing any construction on this class.
     * course_helper constructor.
     */
    private function __construct() {
    }

    /**
     * Checking the capability of an actor whether he/she is able to add the module to the course or not.
     *
     * @param string    $modname
     * @param course    $course
     * @param int       $userid
     *
     * @return bool
     */
    public static function is_module_addable(string $modname, course $course, int $userid = 0): bool {
        global $USER;

        if (0 == $userid) {
            // Including null check.
            $userid = $USER->id;
        }

        if (null == static::$warned) {
            static::$warned = [];
        }

        $capabilityname = "mod/{$modname}:addinstance";
        $capability = get_capability_info($capabilityname);

        if (!$capability) {
            $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            if (!isset(static::$warned[$modname]) && MOD_ARCHETYPE_SYSTEM !== $archetype) {
                // Debug warning that the capability does not exist, but no more than once per page.

                debugging(
                    "The module {$modname} does not define the standard capability '{$capabilityname}'",
                    DEBUG_DEVELOPER
                );

                static::$warned[$modname] = 1;
            }

            // If the capability does not exist, the module can always be added.
            return true;
        }

        if ((defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            // Unit tests are running, we skip the actual capability check.
            return true;
        }

        $context = $course->get_context();
        return has_capability($capabilityname, $context, $userid);
    }

    /**
     * @param \stdClass $data
     * @param array|null $editoroptions
     *
     * @return course
     */
    public static function create_course(\stdClass $data, ?array $editoroptions = null): course {
        global $CFG, $DB;
        $record = fullclone($data);

        if (!empty($editoroptions)) {
            // summary text is updated later, we need context to store the files first
            $record->summary = '';
            $record->summaryformat = FORMAT_HTML;
        }

        /** @var course $course */
        $course = course::create($data);

        if (!empty($editoroptions)) {
            // Save the files used in the summary editor and store.
            require_once("{$CFG->dirroot}/lib/filelib.php");
            $context = $course->get_context();

            $record = file_postupdate_standard_editor(
                $record,
                'summary',
                $editoroptions,
                $context,
                'course',
                'summary',
                0
            );

            $params = ['id' => $course->id];

            $DB->set_field('course', 'summary', $record->summary, $params);
            $DB->set_field('course', 'summaryformat', $record->summaryformat, $params);

            $course->rebuild_cache(true);
        }

        return $course;
    }

    /**
     * @param int       $courseid
     * @param \stdClass $data
     * @param array|null $editoroptions
     *
     * @return course
     */
    public static function update_course(int $courseid, \stdClass $data, ?array $editoroptions = null): course {
        /** @var course $course */
        $course = factory::from_id($courseid);
        $data = fullclone($data);

        if (!empty($editoroptions)) {
            // Modifying $data with course's summary
            $data = file_postupdate_standard_editor(
                $data,
                'summary',
                $editoroptions,
                $course->get_context(),
                'course',
                'summary',
                0
            );
        }

        $course->update($data);
        return $course;
    }
}