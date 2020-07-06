<?php
/*
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

use core\collection;
use core\orm\entity\entity;
use core\orm\query\builder;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\track_user_assignment_via;
use mod_perform\event\activity_deleted;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\entities\activity\element as element_entity;
use mod_perform\models\response\section_element_response;
use mod_perform\models\activity\helpers\activity_deletion;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @group perform
 */
class mod_perform_activity_deletion_model_helper_testcase extends advanced_testcase {

    /**
     * @param bool $include_assignment_and_instances Include assignments and subject/participant instances
     * @param bool $include_responses Questions have answers
     * @param bool $use_shared_elements Share elements with other perform activities
     * @dataProvider delete_provider
     */
    public function test_delete(
        bool $include_assignment_and_instances,
        bool $include_responses,
        bool $use_shared_elements
    ): void {
        self::setAdminUser();

        $perform_generator = $this->perform_generator();

        $config = new mod_perform_activity_generator_configuration();
        $config->set_number_of_users_per_user_group_type($include_assignment_and_instances ? 2 : 0);
        /** @var activity $activity */
        $activity = $perform_generator->create_full_activities($config)->first();
        $activity_id = $activity->id;

        $section1 = $activity->get_sections()->first();
        $element1 = $perform_generator->create_element(['context' => $activity->get_context()]);
        $perform_generator->create_section_element($section1, $element1);

        if ($include_responses) {
            /** @var participant_instance $participant_instance_entity */
            $participant_instance_entity = participant_instance::repository()->order_by('id')->first(true);

            /** @var section_element_entity $section_element_entity */
            $section_element_entity = section_element_entity::repository()->order_by('id')->first(true);

            $section_element_response = new section_element_response(
                $participant_instance_entity,
                $section_element_entity,
                null,
                new collection()
            );

            $section_element_response->set_response_data('question answer')->save();
        }

        if ($use_shared_elements) {
            // Now this element is created in the top level perform context.
            $shared_element = $perform_generator->create_element(['context' => $activity->get_context()->get_parent_context()]);
            $perform_generator->create_section_element($section1, $shared_element);
        }

        $context_id = $activity->get_context()->id;

        $context_row = builder::create()->from('context')->find($context_id);
        self::assertNotNull($context_row, 'perform context should be found');

        $expected_counts = [
            activity_entity::class => self::equalTo(1),
            track_assignment::class => self::greaterThan(0),
            section_entity::class => self::greaterThan(0),
            section_element_entity::class => self::greaterThan(0),
            section_relationship_entity::class => self::greaterThan(0),

            track_user_assignment_via::class => self::equalTo(0),
            track_user_assignment::class => self::equalTo(0),
            subject_instance::class => self::equalTo(0),
            participant_instance::class => self::equalTo(0),

            element_entity::class => self::equalTo(1),
        ];

        if ($include_assignment_and_instances) {
            $expected_counts[track_user_assignment::class] = self::greaterThan(0);
            $expected_counts[track_user_assignment_via::class] = self::greaterThan(0);
            $expected_counts[subject_instance::class] = self::greaterThan(0);
            $expected_counts[participant_instance::class] = self::greaterThan(0);
        }

        if ($include_responses) {
            $expected_counts[element_response_entity::class] = self::equalTo(1);
        }

        if ($use_shared_elements) {
            $expected_counts[element_entity::class] = self::equalTo(2);
        }

        $this->assert_row_counts($expected_counts);

        $sink = $this->redirectEvents();

        // The actual method call.
        (new activity_deletion($activity))->delete();

        $context_row = builder::create()->from('context')->find($context_id);
        self::assertNotNull(
            $context_row,
            'context should not be deleted by this class, that is the responsibility of the perform container'
        );

        $expected_counts = [
            activity_entity::class => self::equalTo(0),
            track_assignment::class => self::equalTo(0),
            section_entity::class => self::equalTo(0),
            section_element_entity::class => self::equalTo(0),
            section_relationship_entity::class => self::equalTo(0),

            track_user_assignment_via::class => self::equalTo(0),
            track_user_assignment::class => self::equalTo(0),
            subject_instance::class => self::equalTo(0),
            participant_instance::class => self::equalTo(0),

            element_entity::class => self::equalTo(0),
        ];

        if ($use_shared_elements) {
            $expected_counts[element_entity::class] = self::equalTo(1);
        }

        $this->assert_row_counts($expected_counts);

        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $event = array_shift($events);
        $this->assertInstanceOf(activity_deleted::class, $event);
        $this->assertEquals($activity_id, $event->objectid);
        $this->assertEquals($context_id, $event->contextid);
    }

    public function delete_provider(): array {
        return [
            // assignments/instances, response, shared element
            'with subject instances but no responses' => [true, false, false],
            'with subject instances and responses' => [true, true, false],
            'without subject instances or subject instances' => [false, false, false],
            'with shared elements' => [true, true, true],
        ];
    }

    public function test_other_activities_are_not_deleted(): void {
        self::setAdminUser();

        $perform_generator = $this->perform_generator();

        $target_activity = $perform_generator->create_activity_in_container();
        $target_activity_id = $target_activity->id;
        $other_activity1_id = $perform_generator->create_activity_in_container()->id;
        $other_activity2_id = $perform_generator->create_activity_in_container()->id;

        (new activity_deletion($target_activity))->delete();

        self::assertFalse(activity_entity::repository()->where('id', $target_activity_id)->exists());
        self::assertTrue(activity_entity::repository()->where('id', $other_activity1_id)->exists());
        self::assertTrue(activity_entity::repository()->where('id', $other_activity2_id)->exists());
    }

    protected function assert_row_counts($expectations): void {
        /** @var Constraint $constraint */
        foreach ($expectations as $entity_class => $constraint) {

            /** @var $entity_class entity */
            $actual_count = $entity_class::repository()->count();

            $message = sprintf('%s count should be %s', $entity_class::TABLE, $constraint->toString());
            self::assertThat($actual_count, $constraint, $message);
        }
    }

    /**
     * @return mod_perform_generator
     * @throws coding_exception
     */
    private function perform_generator(): mod_perform_generator {
        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        return $generator;
    }

}
