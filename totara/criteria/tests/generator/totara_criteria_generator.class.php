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
use totara_criteria\criterion;

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

        // Re-read the instance to ensure all default values are also set
        return coursecompletion::fetch($instance->get_id());
    }

    /**
     * Create a linkedcourses criterion
     *
     *  Data
     *  [
     *      'aggregation' => [
     *          'method' => criterion::AGGREGATE_ALL, // Optional Aggregation method - defaults to ALL
     *          'req_items' => 1, // Optional number of required items. Only used with AGGREGATE_ANY_N. Defaults to 1
     *      ]
     *      'linkedtype' => linkedcourses::LINKTYPE_MANDATORY,   // Optional linkedtype. Defaults to mandatory linked courses
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

        $instance->add_metadata([[
            'metakey' => linkedcourses::METADATA_LINKTYPE_KEY,
            'metavalue' => $data['linkedtype'] ?? linkedcourses::LINKTYPE_MANDATORY,
        ]]);

        $instance->save();

        // Re-read the instance to ensure all default values are also set
        return linkedcourses::fetch($instance->get_id());
    }

    /**
     * Create an onactivate criterion
     *
     * @return criterion
     */
    public function create_onactivate() {
        $instance = new onactivate();

        $instance->save();

        // Re-read the instance to ensure all default values are also set
        return onactivate::fetch($instance->get_id());
    }

    /**
     * Create a childcompetency criterion
     *
     *  Data
     *  [
     *      'aggregation' => [
     *          'method' => criterion::AGGREGATE_ALL, // Optional Aggregation method - defaults to ALL
     *          'req_items' => 1, // Optional number of required items. Only used with AGGREGATE_ANY_N. Defaults to 1
     *      ]
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

        $instance->save();

        // Re-read the instance to ensure all default values are also set
        return childcompetency::fetch($instance->get_id());
    }

}

class totara_criteria_test_item_evaluator {
    use \totara_criteria\item_evaluator;

    /**
     * @var int How many times this update_item_records method was called.
     */
    private static $called = 0;

    /**
     * The method you might be testing.
     *
     * We'll count how many times this is called.
     *
     * Please call the reset_times_called() method on this class at the end of the test.
     */
    public static function update_item_records() {
        self::$called++;
    }

    /**
     * The number of times that the update_item_records() method was called.
     *
     * @return int
     */
    public static function get_times_called(): int {
        return self::$called;
    }

    /**
     * Call this at the end of the test when using this class.
     */
    public static function reset_times_called() {
        self::$called = 0;
    }
}
