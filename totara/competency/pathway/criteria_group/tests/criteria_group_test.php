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
 * @package totara_competency
 */

use criteria_coursecompletion\coursecompletion;
use pathway_criteria_group\criteria_group;
use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\scale_value;
use totara_competency\pathway;
use totara_criteria\criterion;
use totara_competency\entities\scale;

class pathway_criteria_group_testcase extends \advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            public $comp;
            public $courses;
            public $cc = [];
            public $scale;
            public $scalevalues = [];
        };

        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $data->scale = $hierarchygenerator->create_scale(
            'comp',
            ['name' => 'Test scale', 'description' => 'Test scale'],
            [
                1 => ['name' => 'No clue', 'proficient' => 0, 'sortorder' => 1, 'default' => 1],
                2 => ['name' => 'Learning', 'proficient' => 0, 'sortorder' => 2, 'default' => 0],
                3 => ['name' => 'Getting there', 'proficient' => 0, 'sortorder' => 3, 'default' => 0],
                4 => ['name' => 'Almost there', 'proficient' => 1, 'sortorder' => 4, 'default' => 0],
                5 => ['name' => 'Arrived', 'proficient' => 1, 'sortorder' => 4, 'default' => 0],
            ]
        );
        $rows = $DB->get_records('comp_scale_values', ['scaleid' => $data->scale->id], 'sortorder');
        foreach ($rows as $row) {
            $data->scalevalues[$row->sortorder] = new scale_value($row->id);
        }

        $framework = $hierarchygenerator->create_comp_frame(['scale' => $data->scale->id]);
        $comp = $hierarchygenerator->create_comp(['frameworkid' => $framework->id]);
        $data->comp = new competency($comp->id);

        $prefix = 'Course ';
        for ($i = 1; $i <= 5; $i++) {
            $record = [
                'shortname' => $prefix . $i,
                'fullname' => $prefix . $i,
            ];

            $data->courses[$i] = $this->getDataGenerator()->create_course($record);
        }

        // Create coursecompletion criteria
        //      1 - Course 1 AND Course 2
        //      2 - Course 1 OR Course 3 OR Course 5
        //      3 - Course 4 AND Course 5
        $crit_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $data->cc[1] = $crit_generator->create_coursecompletion([
            'aggregation'=> criterion::AGGREGATE_ALL,
            'courseids' =>[$data->courses[1]->id, $data->courses[2]->id],
        ]);

        $data->cc[2] = $crit_generator->create_coursecompletion([
            'aggregation' => criterion::AGGREGATE_ANY_N,
            'req_items' => 1,
            'courseids' => [$data->courses[1]->id, $data->courses[3]->id, $data->courses[5]->id],
        ]);

        $data->cc[3] = $crit_generator->create_coursecompletion([
            'aggregation'=> criterion::AGGREGATE_ALL,
            'courseids' =>[$data->courses[4]->id, $data->courses[5]->id],
        ]);

        // Validate existence of criteria
        $this->validate_num_rows([['totara_criteria', [], 3]]);

        return $data;
    }

    /**
     * Test save new
     */
    public function test_save_new() {

        $data = $this->setup_data();

        $instance = new criteria_group();
        $instance->set_competency($data->comp);
        $instance->add_criterion($data->cc[1]);
        $instance->add_criterion($data->cc[2]);
        $instance->set_scale_value(reset($data->scalevalues));

        // Check no existing pathway rows
        $this->validate_num_rows([
            ['totara_competency_pathway', [], 0],
            ['pathway_criteria_group', [], 0],
            ['pathway_criteria_group_criterion', [], 0],
        ]);

        // Save
        $instance->save();
        $this->assertFalse(empty($instance->get_id()));
        $this->assertFalse(empty($instance->get_path_instance_id()));
        $this->assertTrue($instance->is_active());

        $pw_id = $instance->get_id();
        $instance_id = $instance->get_path_instance_id();

        // Check the saved data
        $this->validate_num_rows([
            ['totara_competency_pathway', ['id' => $pw_id], 1],
            ['pathway_criteria_group', ['id' => $instance_id], 1],
            ['pathway_criteria_group_criterion', ['criteria_group_id' => $instance_id], 2],
        ]);

        // Two criteria
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id(), $data->cc[2]->get_id()]);
    }


    /**
     * Test saving when changing existing instances
     */
    public function test_saving_on_change() {
        global $DB;

        $data = $this->setup_data();

        $instance = new criteria_group();
        $instance->set_competency($data->comp);
        $instance->set_scale_value(reset($data->scalevalues));
        $instance->add_criterion($data->cc[1]);
        $instance->save();

        $pw_id = $instance->get_id();
        $instance_id = $instance->get_path_instance_id();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 1],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id()]);

        $pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);

        // Sleeping 1 second to ensure timestamps are different
        sleep(1);

        // Save without changes
        $instance->save();
        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 1],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id()]);

        // Nothing should have changed - including pathway_modified
        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $this->assertEquals($pw_row, $updated_pw_row);
        $updated_critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);
        $this->assertEquals($critgrp_row, $updated_critgrp_row);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        // Add criterion
        $instance->add_criterion($data->cc[2]);
        $instance->save();

        // Should still be the same row as what was being used originally. ie no archiving
        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertEquals($instance_id, $instance->get_path_instance_id());

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 2],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id(), $data->cc[2]->get_id()]);

        // pathway_emodifieds should have changed
        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $this->assertNotEquals($pw_row->pathway_modified, $updated_pw_row->pathway_modified);
        // Check other attributes
        unset($pw_row->pathway_modified);
        unset($updated_pw_row->pathway_modified);
        $this->assertEquals($pw_row, $updated_pw_row);

        $updated_critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);
        $this->assertEquals($critgrp_row, $updated_critgrp_row);


        // Add one criterion, remove another
        $instance->replace_criteria([$data->cc[2], $data->cc[3]]);
        $instance->save();

        // Should still be the same row as what was being used originally. ie no archiving
        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertEquals($instance_id, $instance->get_path_instance_id());

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 2],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[2]->get_id(), $data->cc[3]->get_id()]);

        $updated_critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);
        $this->assertEquals($critgrp_row, $updated_critgrp_row);


        // Change criterion items in an existing criterion
        $criteria = $instance->get_criteria();
        $initial_crit = reset($criteria);
        $initial_crit_id = $initial_crit->get_id();

        // Initial either cc[2] or cc[3] - none contains Course 2
        // Add another course - Course 2
        $initial_crit->add_items([$data->courses[2]->id]);

        // Save
        $instance->save();

        // Although the criterion changed, nothing should be changed on the group
        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertEquals($instance_id, $instance->get_path_instance_id());

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 2],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[2]->get_id(), $data->cc[3]->get_id()]);

        $updated_critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);
        $this->assertEquals($critgrp_row, $updated_critgrp_row);
    }


    /**
     * Test removing criteria
     */
    public function test_remove_criteria() {
        global $DB;

        $data = $this->setup_data();

        $instance = new criteria_group();
        $instance->set_competency($data->comp);
        $instance->set_scale_value(reset($data->scalevalues));
        $instance->add_criterion($data->cc[1]);
        $instance->add_criterion($data->cc[2]);
        $instance->save();

        $instance_id = $instance->get_path_instance_id();

        // A unique key is assigned to each criterion - this is used for deletion
        // Map the assigned keys to the criteria
        $crit_keys = [];
        foreach ($instance->get_criteria() as $key => $criterion) {
            $crit_keys[$criterion->get_id()] = $key;
        }

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 2],
            ['totara_criteria', ['id' => $data->cc[1]->get_id()], 1],
            ['totara_criteria_item', ['criterion_id' => $data->cc[1]->get_id()], 2],
            ['totara_criteria', ['id' => $data->cc[2]->get_id()], 1],
            ['totara_criteria_item', ['criterion_id' => $data->cc[2]->get_id()], 3],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id(), $data->cc[2]->get_id()]);

        $pw_id = $instance->get_id();
        $instance_id = $instance->get_path_instance_id();

        $pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        // Remove one of the criteria
        $instance->remove_criterion($crit_keys[$data->cc[2]->get_id()]);
        $instance->save();

        // Should still be the same pw row as what was being used originally - only criterion link and criterion itself are removed
        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertEquals($instance_id, $instance->get_path_instance_id());

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 1],
            ['totara_criteria', ['id' => $data->cc[1]->get_id()], 1],
            ['totara_criteria_item', ['criterion_id' => $data->cc[1]->get_id()], 2],
            ['totara_criteria', ['id' => $data->cc[2]->get_id()], 0],
            ['totara_criteria_item', ['criterion_id' => $data->cc[2]->get_id()], 0],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id()]);

        // Timestamps should be updated
        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $this->assertNotEquals($pw_row->pathway_modified, $updated_pw_row->pathway_modified);

        $updated_critgrp_row = $DB->get_record('pathway_criteria_group', ['id' => $instance_id]);
        $this->assertEquals($critgrp_row, $updated_critgrp_row);


        // Remove last criterion - this should also delete the pathway
        $instance->remove_criterion($crit_keys[$data->cc[1]->get_id()]);
        $instance->save();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 0],
            ['pathway_criteria_group_criterion', [], 0],
            ['totara_criteria', ['id' => $data->cc[1]->get_id()], 0],
            ['totara_criteria_item', ['criterion_id' => $data->cc[1]->get_id()], 0],
        ]);

        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertTrue($instance->is_archived());
        $this->assertTrue(empty($instance->get_path_instance_id()));
    }

    /**
     * Test delete
     */
    public function test_delete() {
        global $DB;

        $data = $this->setup_data();

        $instance = new criteria_group();
        $instance->set_competency($data->comp);
        $instance->set_scale_value(reset($data->scalevalues));
        $instance->add_criterion($data->cc[1]);
        $instance->add_criterion($data->cc[2]);
        $instance->save();

        $pw_id = $instance->get_id();
        $instance_id = $instance->get_path_instance_id();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 1],
            ['pathway_criteria_group_criterion', [], 2],
            ['totara_criteria', ['id' => $data->cc[1]->get_id()], 1],
            ['totara_criteria_item', ['criterion_id' => $data->cc[1]->get_id()], 2],
            ['totara_criteria', ['id' => $data->cc[2]->get_id()], 1],
            ['totara_criteria_item', ['criterion_id' => $data->cc[2]->get_id()], 3],
        ]);
        $this->validate_criteria_ids($instance_id, [$data->cc[1]->get_id(), $data->cc[2]->get_id()]);

        $pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        $instance->delete();
        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_criteria_group', [], 0],
            ['pathway_criteria_group_criterion', [], 0],
            ['totara_criteria', ['id' => $data->cc[1]->get_id()], 0],
            ['totara_criteria_item', ['criterion_id' => $data->cc[1]->get_id()], 0],
        ]);

        $this->assertEquals($pw_id, $instance->get_id());
        $this->assertTrue($instance->is_archived());
        $this->assertTrue(empty($instance->get_path_instance_id()));

        // pathway_modified should be updated
        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $this->assertNotEquals($pw_row->pathway_modified, $updated_pw_row->pathway_modified);
    }

    /**
     * Test dump_pathway_configuration
     */
    public function test_dump_pathway_configuration() {
        global $DB;

        $data = $this->setup_data();

        $instance = new criteria_group();
        $instance->set_competency($data->comp);
        $instance->set_scale_value(reset($data->scalevalues));
        $instance->add_criterion($data->cc[1]);
        $instance->add_criterion($data->cc[2]);
        $instance->save();

        $expected = $DB->get_record('pathway_criteria_group', ['id' => $instance->get_path_instance_id()]);
        $expected->criteria = $DB->get_records('pathway_criteria_group_criterion', ['criteria_group_id' => $instance->get_path_instance_id()]);
        foreach ($expected->criteria as $criterion) {
            $criterion->detail = coursecompletion::dump_criterion_configuration($criterion->criterion_id);
        }

        $actual = criteria_group::dump_pathway_configuration($instance->get_path_instance_id());
        $this->assertEqualsCanonicalizing($expected, $actual);
    }


    /**
     * Validate the number of rows in the specified tables
     *
     * @param array $totest Test definition. Each array element is an array containing
     *                      the table name, query conditions and expected number of rows
     */
    private function validate_num_rows(array $totest) {
        global $DB;

        foreach ($totest as $el) {
            if (count($el) < 3) {
                throw new coding_exception('validate_num_rows require 3 array elements for each table to test');
            }

            $rows = $DB->get_records($el[0], $el[1]);
            $this->assertSame((int)$el[2], count($rows));
        }
    }

    /**
     * Validate that the expected criteria is linked to the the group
     *
     * @param int $instance_id Criteria group instance id
     * @param array $expected_criterion_ids Array of expected criterion ids
     */
    private function validate_criteria_ids(int $instance_id, array $expected_criterion_ids) {
        global $DB;

        $rows = $DB->get_records('pathway_criteria_group_criterion', ['criteria_group_id' => $instance_id]);

        $this->assertSame(count($expected_criterion_ids), count($rows));
        while ($row = array_pop($rows)) {
            $this->assertTrue(in_array($row->criterion_id, $expected_criterion_ids));
        }
    }

    /**
     * Test that achievement detail includes no value and not related info if no criteria were there to complete.
     */
    public function test_aggregate_current_value_achievement_details_no_criteria() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $scale = $totara_hierarchy_generator->create_scale('comp');
        $scale = new scale($scale);
        $value1 = $scale->scale_values->first();

        $criteria_group = new criteria_group();
        $criteria_group->set_scale_value($value1);

        $details = $criteria_group->aggregate_current_value(100);

        $this->assertNull($details->get_scale_value_id());
        $this->assertEquals([], $details->get_related_info());
    }

    /**
     * The user must complete a single criteria and does so.
     *
     * The value should be achieved and the single criteria should be given in related info.
     */
    public function test_aggregate_current_value_achievement_details_single_criteria() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $scale = $totara_hierarchy_generator->create_scale('comp');
        $scale = new scale($scale);
        $value1 = $scale->scale_values->first();

        $criteria_group = new criteria_group();
        $criteria_group->set_scale_value($value1);

        $mock_criterion = $this->getMockBuilder(criterion::class)
                                ->setMethods(['aggregate'])
                                ->getMockForAbstractClass();
        $mock_criterion->method('aggregate')->willReturn(true);

        $criteria_group->add_criterion($mock_criterion);

        $details = $criteria_group->aggregate_current_value(100);

        $this->assertEquals($value1->id, $details->get_scale_value_id());
        $this->assertEquals([get_class($mock_criterion)], $details->get_related_info());
    }

    /**
     * The user needed to complete any of two criteria. They complete both.
     *
     * Both criteria are recorded in related info.
     */
    public function test_aggregate_current_value_achievement_details_both_satisfied() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $scale = $totara_hierarchy_generator->create_scale('comp');
        $scale = new scale($scale);
        $value1 = $scale->scale_values->first();

        $criteria_group = new criteria_group();
        $criteria_group->set_scale_value($value1);

        $mock_criterion1 = $this->getMockBuilder(criterion::class)
            ->setMethods(['aggregate'])
            ->getMockForAbstractClass();
        $mock_criterion1->method('aggregate')->willReturn(true);

        $mock_criterion2 = $this->getMockBuilder(criterion::class)
            ->setMethods(['aggregate'])
            ->getMockForAbstractClass();
        $mock_criterion2->method('aggregate')->willReturn(true);

        $criteria_group->add_criterion($mock_criterion1);
        $criteria_group->add_criterion($mock_criterion2);

        $details = $criteria_group->aggregate_current_value(100);

        $this->assertEquals($value1->id, $details->get_scale_value_id());
        $this->assertEqualsCanonicalizing([get_class($mock_criterion1), get_class($mock_criterion2)], $details->get_related_info());
    }

    /**
     * The user needed to complete all of two criteria. They complete one.
     *
     * The related info is empty if they did not complete the necessary criteria.
     */
    public function test_aggregate_current_value_achievement_details_half_satisfied() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $scale = $totara_hierarchy_generator->create_scale('comp');
        $scale = new scale($scale);
        $value1 = $scale->scale_values->first();

        $criteria_group = new criteria_group();
        $criteria_group->set_scale_value($value1);

        $mock_criterion1 = $this->getMockBuilder(criterion::class)
            ->setMethods(['aggregate'])
            ->getMockForAbstractClass();
        $mock_criterion1->method('aggregate')->willReturn(true);

        $mock_criterion2 = $this->getMockBuilder(criterion::class)
            ->setMethods(['aggregate'])
            ->getMockForAbstractClass();
        $mock_criterion2->method('aggregate')->willReturn(false);

        $criteria_group->add_criterion($mock_criterion1);
        $criteria_group->add_criterion($mock_criterion2);

        $details = $criteria_group->aggregate_current_value(100);

        $this->assertNull($details->get_scale_value_id());
        $this->assertEqualsCanonicalizing([], $details->get_related_info());
    }
}
