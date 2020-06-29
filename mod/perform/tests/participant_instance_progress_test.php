<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use core\collection;
use core\entities\user;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\response\participant_section;
use mod_perform\state\participant_instance\complete as complete_instance;
use mod_perform\state\participant_instance\condition\all_sections_complete;
use mod_perform\state\participant_instance\condition\at_least_one_section_started;
use mod_perform\state\participant_instance\condition\not_all_sections_complete;
use mod_perform\state\participant_instance\in_progress;
use mod_perform\state\participant_instance\not_started;
use mod_perform\state\participant_section\complete as complete_section;
use mod_perform\state\participant_section\in_progress as in_progress_section;
use mod_perform\state\participant_section\not_started as not_started_section;
use mod_perform\state\state_helper;
use mod_perform\state\participant_instance\participant_instance_progress;

require_once(__DIR__ . '/generator/activity_generator_configuration.php');
require_once(__DIR__ . '/state_testcase.php');


/**
 * @group perform
 */
class mod_perform_participant_instance_progress_testcase extends state_testcase {

    protected static function get_object_type(): string {
        return 'participant_instance';
    }

    public function condition_all_sections_data_provider(): array {
        $n = not_started_section::get_code();
        $i = in_progress_section::get_code();
        $c = complete_section::get_code();
        return [
            [$n, $n, false],
            [$n, $c, false],
            [$i, $i, false],
            [$c, $i, false],
            [$c, $c, true],
        ];
    }

    /**
     * @dataProvider condition_all_sections_data_provider
     * @param $section1_progress
     * @param $section2_progress
     * @param $expected_result
     */
    public function test_condition_all_sections_complete($section1_progress, $section2_progress, $expected_result) {
        $section1 = (object)['progress' => $section1_progress];
        $section2 = (object)['progress' => $section2_progress];

        $mock = $this->getMockBuilder(participant_instance::class)
            ->setMethods(['__get'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('__get')
            ->with('participant_sections')
            ->willReturn(new collection([$section1, $section2]));

        $all_sections_complete = new all_sections_complete($mock);
        $this->assertEquals($expected_result, $all_sections_complete->pass());

        $not_all_sections_complete = new not_all_sections_complete($mock);
        $this->assertEquals(!$expected_result, $not_all_sections_complete->pass());
    }

    public function at_least_one_section_started_data_provider() {
        $n = not_started_section::get_code();
        $i = in_progress_section::get_code();
        $c = complete_section::get_code();
        return [
            [$n, $n, false],
            [$n, $i, true],
            [$n, $c, true],
            [$i, $c, true],
            [$c, $c, true],
        ];
    }

    /**
     * @dataProvider at_least_one_section_started_data_provider
     * @param $section1_progress
     * @param $section2_progress
     * @param $expected_result
     */
    public function test_condition_at_least_one_section_started($section1_progress, $section2_progress, $expected_result) {
        $section1 = (object)['progress' => $section1_progress];
        $section2 = (object)['progress' => $section2_progress];

        $mock = $this->getMockBuilder(participant_instance::class)
            ->setMethods(['__get'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('__get')
            ->with('participant_sections')
            ->willReturn(new collection([$section1, $section2]));

        $at_least_one_section_started = new at_least_one_section_started($mock);
        $this->assertEquals($expected_result, $at_least_one_section_started->pass());
    }


    /**
     * Check interactions between section and instance states.
     */
    public function test_section_switch_triggers_instance_switch(): void {
        $this->setAdminUser();

        $configuration = mod_perform_activity_generator_configuration::new()
            ->set_number_of_sections_per_activity(2)
            ->set_number_of_users_per_user_group_type(2);

        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $generator->create_full_activities($configuration);
        $participant_instances = participant_instance_entity::repository()->get()->all();
        $this->assertCount(2, $participant_instances);
        $participant_instance1 = $participant_instances[0];
        $participant_instance2 = $participant_instances[1];
        /** @var user $subject_user1 */
        $subject_user1 = user::repository()->find($participant_instance1->participant_id);
        /** @var user $subject_user2 */
        $subject_user2 = user::repository()->find($participant_instance2->participant_id);

        // We have two sections and two participant_instances. We only progress for one participant_instance
        // and check that the other one is not affected.
        $participant_sections = $participant_instance1->participant_sections->all();
        $this->assertCount(2, $participant_sections);
        $participant_section1_entity = $participant_sections[0];
        $participant_section2_entity = $participant_sections[1];

        // Make sure we actually have two different users for the two instances.
        $this->assertNotEquals($subject_user1->id, $subject_user2->id);
        $this->assert_participant_instance_progress([
            $participant_instance1->id => not_started::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);

        $participant_section1 = participant_section::load_by_entity($participant_section1_entity);
        $participant_section2 = participant_section::load_by_entity($participant_section2_entity);

        $this->setUser($subject_user1->get_record());

        // Progress section1 to in_progress - instance is expected to be in_progress as a result.
        $participant_section1->switch_state(in_progress_section::class);
        $this->assert_participant_instance_progress([
            $participant_instance1->id => in_progress::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);

        // Progress section1 to complete - no change to instance (still in_progress).
        $participant_section1->switch_state(complete_section::class);
        $this->assert_participant_instance_progress([
            $participant_instance1->id => in_progress::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);

        // Progress section2 to in_progress - no change to instance (still in_progress).
        $participant_section2->switch_state(in_progress_section::class);
        $this->assert_participant_instance_progress([
            $participant_instance1->id => in_progress::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);

        // Progress section2 to complete - instance should progress to complete.
        $participant_section2->switch_state(complete_section::class);
        $this->assert_participant_instance_progress([
            $participant_instance1->id => complete_instance::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);

        // Add a new (not_started) participant section to the participant instance.
        $generator->create_participant_section(
            $participant_section2->section->get_activity(),
            $participant_instance1,
            false
        );
        $participant_instance1_model = participant_instance::load_by_id($participant_instance1->id);
        // Adding a new section doesn't trigger any event (yet), so we call the update method manually.
        $participant_instance1_model->update_progress_status();
        // Participant instance should be regressed back to "in_progress".
        $this->assert_participant_instance_progress([
            $participant_instance1->id => in_progress::get_code(),
            $participant_instance2->id => not_started::get_code(),
        ]);
    }

    public function test_get_all_translated() {
        $this->assertEqualsCanonicalizing([
            20 => 'Complete',
            10 => 'In progress',
            0 => 'Not started',
        ], state_helper::get_all_display_names('participant_instance', participant_instance_progress::get_type()));
    }

    /**
     * @param array $instance_states
     */
    private function assert_participant_instance_progress(array $instance_states) {
        foreach ($instance_states as $instance_id => $state_code) {
            $participant_instance = participant_instance::load_by_id($instance_id);
            $this->assertEquals($state_code, $participant_instance->progress);
        }
    }
}
