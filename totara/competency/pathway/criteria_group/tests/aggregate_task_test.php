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
 * @package pathway_criteria_group
 */

use pathway_criteria_group\task\aggregate;

class pathway_criteria_group_aggregate_task_testcase extends advanced_testcase {

    private function generate_active_expanded_user_assignments($competency, $users) {
        global $DB;

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();

        $assignment_ids = [];
        foreach ($users as $user) {
            $assignment = $assignment_generator->create_user_assignment($competency->id, $user->id);
            $assignment_ids[] = $assignment->id;
        }

        $model = new \totara_competency\models\assignment_actions();
        $model->activate($assignment_ids);

        $expand_task = new \totara_competency\expand_task($DB);
        $expand_task->expand_all();

        return $assignment_ids;
    }

    private function get_mock_criteria() {
        /** @var \totara_criteria\criterion|\PHPUnit\Framework\MockObject\MockObject $criteria */
        $criteria = $this->getMockForAbstractClass(\totara_criteria\criterion::class, [], 'mock');
        $criteria->method('get_items_type')->willReturn('mock');
        if (!class_exists('criteria_mock\mock')) {
            class_alias('mock', 'criteria_mock\mock');
        }
        \totara_competency\plugintypes::enable_plugin('mock', 'criteria', 'totara_criteria');
        $criteria->set_aggregation_method(\totara_criteria\criterion::AGGREGATE_ALL);
        $criteria->set_aggregation_params(['req_items' => 1]);
        $criteria->add_items([100]);
        $criteria->save();

        return $criteria;
    }

    public function test_execute_with_no_data() {
        $task = new aggregate();
        $task->execute();
    }

    public function test_no_users_assigned() {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $competency = $competency_generator->create_competency();
        $criteria = $this->get_mock_criteria();

        $criteria_group = $competency_generator->create_criteria_group($competency, $criteria);

        $task = new aggregate();
        $this->assertEmpty($task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(0, iterator_count($to_calculate));
        $to_calculate->close();

        $task->execute();
    }

    public function test_one_new_user_assigned() {

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $competency = $competency_generator->create_competency();
        $criteria = $this->get_mock_criteria();

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $criteria_group = $competency_generator->create_criteria_group($competency, $criteria);

        $task = new aggregate();
        $this->assertEmpty($task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(1, iterator_count($to_calculate));
        $to_calculate->close();

        // This should add the missing item record.
        $task->execute();

        $this->assertCount(1, $task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(0, iterator_count($to_calculate));
        $to_calculate->close();
    }

    public function test_one_user_archived_assignment_no_item_record() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $competency = $competency_generator->create_competency();
        $criteria = $this->get_mock_criteria();

        $user = $this->getDataGenerator()->create_user();
        $assignment_ids = $this->generate_active_expanded_user_assignments($competency, [$user]);

        $criteria_group = $competency_generator->create_criteria_group($competency, $criteria);

        (new \totara_competency\models\assignment_actions())->archive($assignment_ids);
        (new \totara_competency\expand_task($DB))->expand_all();

        $task = new aggregate();
        $this->assertEmpty($task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(0, iterator_count($to_calculate));
        $to_calculate->close();
    }

    public function test_one_user_archived_assignment_with_item_record() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $competency = $competency_generator->create_competency();
        $criteria = $this->get_mock_criteria();

        $user = $this->getDataGenerator()->create_user();
        $assignment_ids = $this->generate_active_expanded_user_assignments($competency, [$user]);

        $criteria_group = $competency_generator->create_criteria_group($competency, $criteria);

        $task = new aggregate();
        $task->execute();

        // Make sure of the state after execute. But don't execute again after this. We just want to check
        // the numbers are as expected.
        $this->assertCount(1, $task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(0, iterator_count($to_calculate));
        $to_calculate->close();

        (new \totara_competency\models\assignment_actions())->archive($assignment_ids);
        (new \totara_competency\expand_task($DB))->expand_all();

        $this->assertEmpty($task->get_users_requiring_aggregation($criteria_group->get_id()));

        $to_calculate = $task->get_users_to_add_item_records_for();
        $this->assertEquals(0, iterator_count($to_calculate));
        $to_calculate->close();
    }
}
