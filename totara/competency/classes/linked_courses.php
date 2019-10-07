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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_competency;

use core\task\manager as task_manager;
use criteria_linkedcourses\task\update_linked_course_items_adhoc;
use totara_competency\event\linked_courses_updated;

/**
 * Class linked_courses
 *
 * Helper methods relating to linked courses for competencies.
 */
class linked_courses {

    public const LINKTYPE_OPTIONAL = PLAN_LINKTYPE_OPTIONAL;
    public const LINKTYPE_MANDATORY = PLAN_LINKTYPE_MANDATORY;

    /**
     * Returns array of course records. Each record also includes the linktype,
     * i.e. PLAN_LINKTYPE_OPTIONAL or PLAN_LINKTYPE_MANDATORY.
     *
     * Permissions checks, e.g. ensuring the user can see each course, must be done externally.
     *
     * @param int $competency_id
     * @return \stdClass[] keyed by course id.
     */
    public static function get_linked_courses($competency_id) {
        global $DB;

        $linked_courses_records = $DB->get_records_sql(
            'SELECT course.id, course.fullname, cc.linktype
                   FROM {course} course
                   JOIN {comp_criteria} cc
                     ON cc.itemtype = ?
                    AND course.id = cc.iteminstance
                  WHERE cc.competencyid = ?
                  ORDER BY course.id',
            ['coursecompletion', $competency_id]
        );

        return $linked_courses_records;
    }

    /**
     * Returns array of course ids.
     *
     * @param int $competency_id
     * @return int[] keyed by course id.
     */
    public static function get_linked_course_ids($competency_id): array {
        global $DB;

        $linked_course_ids = $DB->get_fieldset_select('comp_criteria',
            'iteminstance',
            'competencyid = :competencyid AND itemtype = :itemtype',
            ['competencyid' => $competency_id, 'itemtype' => 'coursecompletion']);

        return $linked_course_ids;
    }

    /**
     * Allows setting of which courses are linked to a given competency.
     *
     * Overwrites previous linked courses for the given competency.
     * Permissions checks, e.g. ensuring the user can see each course, must be done externally.
     *
     * @param int $competency_id
     * @param array $courses Each array element is an array that must contain 'id' and 'linktype'.
     *   'linktype' would be either PLAN_LINKTYPE_OPTIONAL or PLAN_LINKTYPE_MANDATORY.
     */
    public static function set_linked_courses($competency_id, $courses) {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/totara/plan/lib.php');

        // Have set iteminstance as the first field so that the results are keyed by the course id.
        $linked_courses_records = $DB->get_records_sql(
            'SELECT iteminstance, linktype, id
                   FROM {comp_criteria}
                  WHERE itemtype = ?
                    AND competencyid = ?',
            ['coursecompletion', $competency_id]
        );

        $valid_linktypes = [
            static::LINKTYPE_OPTIONAL => static::LINKTYPE_OPTIONAL,
            static::LINKTYPE_MANDATORY => static::LINKTYPE_MANDATORY,
        ];

        $to_update = [];
        $to_add = [];

        foreach ($courses as $course) {
            // Validate linktype.
            if (!isset($course['linktype'])) {
                if (!isset($course['mandatory'])) {
                    throw new \coding_exception('Linktype or mandatory is required');
                }

                $course['linktype'] = $course['mandatory'] ? static::LINKTYPE_MANDATORY : static::LINKTYPE_OPTIONAL;

            } else if (!isset($valid_linktypes[$course['linktype']])) {
                throw new \coding_exception('Invalid linktype');
            }

            if (isset($to_add[$course['id']]) || isset($to_update[$course['id']])) {
                throw new \coding_exception('Duplicate courses');
            }

            if (isset($linked_courses_records[$course['id']])) {
                $linked_courses_record = $linked_courses_records[$course['id']];

                // Check if linktype is same and update if not.
                if ($linked_courses_record->linktype != $course['linktype']) {
                    $linked_courses_record->linktype = $course['linktype'];
                    $linked_courses_record->timemodified = time();
                    $linked_courses_record->usermodified = $USER->id;
                    $to_update[$course['id']] = $linked_courses_record;
                }
            } else {
                $comp_criteria = new \stdClass();
                $comp_criteria->competencyid = $competency_id;
                $comp_criteria->itemtype = 'coursecompletion';
                $comp_criteria->iteminstance = $course['id'];
                $comp_criteria->timecreated = time();
                $comp_criteria->timemodified = time();
                $comp_criteria->usermodified = $USER->id;
                $comp_criteria->linktype = $course['linktype'];
                $to_add[$course['id']] = $comp_criteria;
            }

            unset($linked_courses_records[$course['id']]);
        }

        if (count($to_add) > 0) {
            $DB->insert_records('comp_criteria', $to_add);
        }

        foreach ($to_update as $update_record) {
            $DB->update_record('comp_criteria', $update_record);
        }

        // Any remaining courses from the initial SELECT query were not unset, so are not in the list
        // of linked courses to be set. These must be deleted.
        $delete_ids = [];
        foreach ($linked_courses_records as $linked_courses_record) {
            $delete_ids[] = $linked_courses_record->id;
        }
        $DB->delete_records_list('comp_criteria', 'id', $delete_ids);

        $event = linked_courses_updated::create_from_competency($competency_id);
        $event->trigger();
    }
}
