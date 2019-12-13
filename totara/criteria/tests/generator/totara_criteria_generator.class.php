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

defined('MOODLE_INTERNAL') || die();

use criteria_childcompetency\childcompetency;
use criteria_coursecompletion\coursecompletion;
use criteria_linkedcourses\linkedcourses;
use criteria_onactivate\onactivate;
use totara_competency\plugin_types;
use totara_criteria\criterion;
use totara_criteria\criterion_factory;
use totara_criteria\entities\criterion as criterion_entity;

/**
 * Coursecompletion criterion generator.
 *
 * Usage:
 *     $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
 */
class totara_criteria_generator extends component_generator_base {

    /**
     * Create a coursecompletion criterion
     *
     *  Data
     *  [
     *      'aggregation' => [
     *          'method' => criterion::AGGREGATE_ALL, // Optional Aggregation method - defaults to ALL
     *          'req_items' => 1, // Optional number of required items. Only used with AGGREGATE_ANY_N. Defaults to 1
     *      ]
     *      'courseids' =>[], // Ids of courses to be completed.
     *  ]
     *
     * @param array $data Criterion data
     * @return criterion
     */
    public function create_coursecompletion(array $data = []) {
        $instance = new coursecompletion();

        $data['aggregation'] = $data['aggregation'] ?? [];
        $instance->set_aggregation_method($data['aggregation']['method'] ?? criterion::AGGREGATE_ALL);
        $instance->set_aggregation_params(['req_items' => $data['aggregation']['req_items'] ?? 1]);

        $instance->add_items($data['courseids']);
        $instance->save();

        return $instance;
    }

    /**
     * Create a linkedcourses criterion
     *
     *  Data
     *  [
     *      'aggregation' => [
     *          'method' => criterion::AGGREGATE_ALL, // Optional Aggregation method - defaults to ALL
     *          'req_items' => 1, // Optional number of required items. Only used with AGGREGATE_ANY_N. Defaults to 1
     *      ],
     *      'competency' => 1,   // Id of competency whose child competencies should be achieved
     *  ]
     *
     * @param array $data Criterion data
     * @return criterion
     */
    public function create_linkedcourses(array $data = []) {
        $instance = new linkedcourses();

        $data['aggregation'] = $data['aggregation'] ?? [];
        $instance->set_aggregation_method($data['aggregation']['method'] ?? criterion::AGGREGATE_ALL);
        $instance->set_aggregation_params(['req_items' => $data['aggregation']['req_items'] ?? 1]);

        if (!empty($data['competency'])) {
            $instance->set_competency_id($data['competency']);
        }

        $instance->save();

        return $instance;
    }

    /**
     * Create an onactivate criterion
     *
     *  Data
     *  [
     *      'competency' => 1,   // Id of competency whose child competencies should be achieved
     *  ]
     *
     * @param array $data
     * @return criterion
     */
    public function create_onactivate(array $data = []) {
        $instance = new onactivate();
        if (!empty($data['competency'])) {
            $instance->set_competency_id($data['competency']);
        }

        $instance->save();

        // Re-read the instance to ensure all default values are also set
        return $instance;
    }

    /**
     * Create a childcompetency criterion
     *
     *  Data
     *  [
     *      'aggregation' => [
     *          'method' => criterion::AGGREGATE_ALL, // Optional Aggregation method - defaults to ALL
     *          'req_items' => 1, // Optional number of required items. Only used with AGGREGATE_ANY_N. Defaults to 1
     *      ],
     *      'competency' => 1,   // Id of competency whose child competencies should be achieved
     *  ]
     *
     * @param array $data Criterion data
     * @return criterion
     */
    public function create_childcompetency(array $data = []) {
        $instance = new childcompetency();

        $data['aggregation'] = $data['aggregation'] ?? [];
        $instance->set_aggregation_method($data['aggregation']['method'] ?? criterion::AGGREGATE_ALL);
        $instance->set_aggregation_params(['req_items' => $data['aggregation']['req_items'] ?? 1]);

        if (!empty($data['competency'])) {
            $instance->set_competency_id($data['competency']);
        }

        $instance->save();

        // Re-read the instance to ensure all default values are also set
        return $instance;
    }

    /**
     * Create a test criterion
     *
     * @return criterion
     */
    public function create_test_criterion(string $plugin): criterion {
        plugin_types::enable_plugin($plugin, 'criteria', 'totara_criteria');
        $criterion = criterion_factory::create($plugin);
        $criterion->update_items();
        return $criterion;
    }

    /**
     * Creates a criteria item for a course
     *
     * @param stdClass $course
     * @return int the id of the item just generated
     */
    public function create_course_criterion_item(stdClass $course) {
        global $DB;

        $criterion = new criterion_entity();
        $criterion->plugin_type = 'test';
        $criterion->aggregation_method = criterion::AGGREGATE_ALL;
        $criterion->criterion_modified = time();
        $criterion->save();

        $record = new stdClass();
        $record->criterion_id = $criterion->id;
        $record->item_type = 'course';
        $record->item_id = $course->id;
        return $DB->insert_record('totara_criteria_item', $record);
    }

}

