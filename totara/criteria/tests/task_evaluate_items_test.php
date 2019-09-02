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

use totara_criteria\task\evaluate_items;
use totara_competency\plugintypes;

class totara_criteria_task_evaluate_items_testcase extends advanced_testcase {

    public function test_no_enabled_plugins() {
        $enabled = plugintypes::get_enabled_plugins('criteria', 'totara_criteria');
        foreach ($enabled as $plugin) {
            plugintypes::disable_plugin($plugin, 'criteria', 'totara_criteria');
        }

        $task = new evaluate_items();
        $task->execute();
    }

    public function test_one_enabled_plugins() {
        $task = new evaluate_items();

        // To load the totara_criteria_test_item_evaluator class.
        $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $evaluators = [totara_criteria_test_item_evaluator::class];

        // Test update, with before and after check of number of times update_item_records called.
        $this->assertEquals(0, totara_criteria_test_item_evaluator::get_times_called());
        $task->update_item_records($evaluators);
        $this->assertEquals(1, totara_criteria_test_item_evaluator::get_times_called());

        totara_criteria_test_item_evaluator::reset_times_called();
    }

    public function test_duplicate_item_evaluators() {
        $task = new evaluate_items();

        // To load the totara_criteria_test_item_evaluator class.
        $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $evaluators = [
            totara_criteria_test_item_evaluator::class,
            totara_criteria_test_item_evaluator::class
        ];

        // Test update, with before and after check of number of times update_item_records called.
        // Matching evaluators are only called once.
        $this->assertEquals(0, totara_criteria_test_item_evaluator::get_times_called());
        $task->update_item_records($evaluators);
        $this->assertEquals(1, totara_criteria_test_item_evaluator::get_times_called());

        totara_criteria_test_item_evaluator::reset_times_called();
    }

    public function test_different_item_evaluators() {
        $task = new evaluate_items();

        // To load the totara_criteria_test_item_evaluator class.
        $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        // A way of providing a different classname without adding another actual test class.
        class_alias(totara_criteria_test_item_evaluator::class, totara_criteria_test_item_evaluator::class . '_copy');

        $evaluators = [
            totara_criteria_test_item_evaluator::class,
            totara_criteria_test_item_evaluator::class,
            totara_criteria_test_item_evaluator::class . '_copy'
        ];

        // Test update, with before and after check of number of times update_item_records called.
        // Matching evaluators are only called once. But the evaluator with a different classname was still called.
        $this->assertEquals(0, totara_criteria_test_item_evaluator::get_times_called());
        $task->update_item_records($evaluators);
        $this->assertEquals(2, totara_criteria_test_item_evaluator::get_times_called());

        totara_criteria_test_item_evaluator::reset_times_called();
    }
}