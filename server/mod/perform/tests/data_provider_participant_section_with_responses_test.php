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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\entities\user;
use mod_perform\constants;
use mod_perform\data_providers\response\participant_section_with_responses;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\subject_instance;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\participant_source;
use mod_perform\models\activity\settings\visibility_conditions\all_responses;
use mod_perform\models\activity\settings\visibility_conditions\none;
use mod_perform\models\activity\settings\visibility_conditions\own_response;
use mod_perform\models\response\participant_section;
use mod_perform\models\response\responder_group;
use mod_perform\models\response\section_element_response;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\open;

/**
 * @group perform
 */
class mod_perform_data_provider_participant_section_with_responses_testcase extends advanced_testcase {


    public function test_get_unanswered(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new participant_section_with_responses($participant_section);

        /** @var participant_section $fetched_participant_section */
        $fetched_participant_section = $data_provider->build();

        self::assert_same_participant_section($participant_section, $fetched_participant_section);

        $responses = $fetched_participant_section->get_section_element_responses();
        self::assertCount(2, $responses);

        foreach ($responses as $response) {
            self::assertNull($response->response_data);
        }
    }

    public function test_get_answered(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new participant_section_with_responses($participant_section);

        $responses =  $data_provider->build()->get_section_element_responses();
        self::assertCount(2, $responses);

        // Set answers on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            $response->set_response_data($question_number);
            $response->save();
        }

        $responses =  $data_provider->build()->get_section_element_responses();
        self::assertCount(2, $responses);

        // Should be an answer on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            self::assertEquals($question_number, $response->response_data);
        }
    }

    public function test_get_others_answered_responses(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new participant_section_with_responses($participant_section);

        $main_responses =  $data_provider->build()->get_section_element_responses();
        self::assertCount(2, $main_responses);

        // Set the manager's response on each question.
        foreach ($main_responses->all(false) as $question_number => $main_response) {
            self::assertNull($main_response->response_data);

            $other_responder_groups = $main_response->get_other_responder_groups();
            self::assertCount(1, $other_responder_groups);

            /** @var responder_group $manager_response_group */
            $manager_response_group = $other_responder_groups->first();
            self::assertEquals($manager_response_group->get_relationship_name(), 'Manager');

            $manager_responses =  $manager_response_group->get_responses();
            self::assertCount(1, $manager_responses);

            /** @var section_element_response $manager_response */
            $manager_response = $manager_responses->first();
            self::assertNull($manager_response->response_data);

            $manager_response->set_response_data($question_number);
            $manager_response->save();
        }

        $main_responses =  $data_provider->build()->get_section_element_responses();
        self::assertCount(2, $main_responses);

        // Set the manager's response on each question.
        foreach ($main_responses->all(false) as $question_number => $main_response) {
            self::assertNull($main_response->response_data);

            $other_responder_groups = $main_response->get_other_responder_groups();
            self::assertCount(1, $other_responder_groups);

            /** @var responder_group $manager_response_group */
            $manager_response_group = $other_responder_groups->first();
            self::assertEquals($manager_response_group->get_relationship_name(), 'Manager');

            $manager_responses =  $manager_response_group->get_responses();
            self::assertCount(1, $manager_responses);

            /** @var section_element_response $manager_response */
            $manager_response = $manager_responses->first();
            self::assertEquals($question_number, $manager_response->response_data);
        }
    }

    /**
     * This covers the case where someone has one, none, or multiple job assignments so they can have any combinations
     * of managers or appraisers.
     *
     * @param int $expected_manager_count
     * @param int $expected_appraiser_count
     * @param string[] $relationships
     * @param bool $subject_can_view_others_responses
     *
     * @throws coding_exception
     * @dataProvider responder_group_population_provider
     */
    public function test_responder_group_population_for_subject(
        int $expected_manager_count,
        int $expected_appraiser_count,
        array $relationships,
        bool $subject_can_view_others_responses = true
    ): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = user::logged_in();
        $subject_user_id = $subject_user->id;

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject_user_id,
            'other_participant_id' => null,
            'include_questions' => false,
        ]);

        $activity = new activity($subject_instance->activity());

        $section = $generator->create_section($activity, ['title' => 'Part one']);

        // Always create both the manager and appraiser section_relationships
        $manager_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_MANAGER]);
        $appraiser_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_APPRAISER]);
        $subject_section_relationship = $generator->create_section_relationship(
            $section,
            ['relationship' => constants::RELATIONSHIP_SUBJECT],
            $subject_can_view_others_responses
        );

        $element = $generator->create_element(['title' => 'Question one']);
        $generator->create_section_element($section, $element);

        foreach ($relationships as $relationship_class_name) {
            if ($relationship_class_name === constants::RELATIONSHIP_MANAGER) {
                $core_relationship_id = $manager_section_relationship->core_relationship_id;
            } else {
                $core_relationship_id = $appraiser_section_relationship->core_relationship_id;
            }

            $participant_user = self::getDataGenerator()->create_user();
            $generator->create_participant_instance_and_section(
                $activity,
                $participant_user,
                $subject_instance->id,
                $section,
                $core_relationship_id
            );
        }

        $subject_section = $generator->create_participant_instance_and_section(
            $activity,
            $subject_user->get_record(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $data_provider = new participant_section_with_responses(participant_section::load_by_id($subject_section->id));

        /** @var section_element_response $element_response */
        $element_response = $data_provider->build()->get_section_element_responses()->first();

        static::assertEquals('Subject', $element_response->get_relationship_name());

        /** @var responder_group $manager_responder_group */
        $manager_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Manager';
        });

        /** @var responder_group $appraiser_responder_group */
        $appraiser_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Appraiser';
        });

        if (!$subject_can_view_others_responses) {
            self::assertCount(0, $element_response->get_other_responder_groups());
        } else {
            // There should always be two groups if the subject has visibility, the manager and appraiser group.
            self::assertCount(2, $element_response->get_other_responder_groups());

            // Note these are all empty responses.
            self::assertCount($expected_manager_count, $manager_responder_group->get_responses());
            self::assertCount($expected_appraiser_count, $appraiser_responder_group->get_responses());
        }
    }

    public function responder_group_population_provider(): array {
        return [
            'Two managers, one appraisers' => [
                2, 1, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_APPRAISER]
            ],
            'Two managers, one appraisers - no visibility of other responses' => [
                0, 0, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_APPRAISER], false
            ],
            'Two appraisers, one managers' => [
                1, 2, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_APPRAISER]
            ],
            'Two appraisers, one managers - no visibility of other responses' => [
                0, 0, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_APPRAISER], false
            ],
            'One manager, no appraiser' => [1, 0, [constants::RELATIONSHIP_MANAGER]],
            'One manager, no appraiser - no visibility of other responses' => [0, 0, [constants::RELATIONSHIP_MANAGER], false],
            'No manager, no appraiser' => [0, 0, []],
            'No manager, no appraiser  - no visibility of other responses' => [0, 0, [], false],
        ];
    }

    /**
     * @dataProvider responder_group_population_for_non_subject_provider
     * @param string $fetching_as
     */
    public function test_responder_group_population_for_non_subject(string $fetching_as): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = user::logged_in();
        $subject_user_id = $subject_user->id;

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject_user_id,
            'other_participant_id' => null,
            'include_questions' => false,
        ]);

        $activity = new activity($subject_instance->activity());

        $section = $generator->create_section($activity, ['title' => 'Part one']);

        $manager_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_MANAGER]);
        $appraiser_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_APPRAISER]);
        $subject_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_SUBJECT]);

        $element = $generator->create_element(['title' => 'Question one']);
        $generator->create_section_element($section, $element);

        $manager_user = self::getDataGenerator()->create_user();
        $appraiser_user = self::getDataGenerator()->create_user();

        $manager_section = $generator->create_participant_instance_and_section(
            $activity,
            $manager_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->core_relationship_id
        );

        $appraiser_section = $generator->create_participant_instance_and_section(
            $activity,
            $appraiser_user,
            $subject_instance->id,
            $section,
            $appraiser_section_relationship->core_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $subject_user->get_record(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $participant_section_id = $fetching_as === 'Manager' ? $manager_section->id : $appraiser_section->id;

        $data_provider = new participant_section_with_responses(participant_section::load_by_id($participant_section_id));

        /** @var section_element_response $element_response */
        $element_response = $data_provider->build()->get_section_element_responses()->first();

        static::assertEquals($fetching_as, $element_response->get_relationship_name());

        /** @var responder_group $manager_responder_group */
        $manager_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Manager';
        });

        /** @var responder_group $appraiser_responder_group */
        $appraiser_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Appraiser';
        });

        /** @var responder_group $appraiser_responder_group */
        $subject_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Subject';
        });

        // There should always be two groups, the subject and another for either appraiser/manager group.
        self::assertCount(2, $element_response->get_other_responder_groups());

        // Note these are all empty responses.
        self::assertCount(1, $subject_responder_group->get_responses());

        if ($fetching_as === 'Manager') {
            self::assertNull(
                $manager_responder_group,
                'When fetching as manager there should not be a manager other responder group'
            );

            self::assertCount(1,
                $appraiser_responder_group->get_responses(),
                'When fetching as manager there should be an empty appraiser response'
            );
        } else {
            self::assertNull(
                $appraiser_responder_group,
                'When fetching as appraiser there should not be a appraiser other responder group'
            );

            self::assertCount(1,
                $manager_responder_group->get_responses(),
                'When fetching as appraiser there should be an empty manager response'
            );
        }
    }

    public function responder_group_population_for_non_subject_provider(): array {
        return [
            'Fetching for Manager' => ['Manager'],
            'Fetching for Appraiser' => ['Appraiser'],
        ];
    }

    /**
     * This covers the case where we are fetching for a non subject participant, where there are more
     * participants in the same relationship.
     *
     * For example a manager fetching the participant section for a subject that has two job assignments
     * and therefor two managers. The other manager should not be excluded from the other responder groups.
     *
     * @throws coding_exception
     */
    public function test_responder_group_population_for_manager_where_there_is_another_manager(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = user::logged_in();
        $subject_user_id = $subject_user->id;

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject_user_id,
            'other_participant_id' => null,
            'include_questions' => false,
        ]);

        $activity = new activity($subject_instance->activity());

        $section = $generator->create_section($activity, ['title' => 'Part one']);

        $manager_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_MANAGER]);
        $subject_section_relationship = $generator->create_section_relationship($section, ['relationship' => constants::RELATIONSHIP_SUBJECT]);

        $element = $generator->create_element(['title' => 'Question one']);
        $generator->create_section_element($section, $element);

        $manager_user = self::getDataGenerator()->create_user();
        $other_manager_user = self::getDataGenerator()->create_user();

        // The manager we are fetching for's section.
        $manager_section = $generator->create_participant_instance_and_section(
            $activity,
            $manager_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->core_relationship_id
        );

        // The other managers section.
        $generator->create_participant_instance_and_section(
            $activity,
            $other_manager_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->core_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $subject_user->get_record(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $data_provider = new participant_section_with_responses(participant_section::load_by_id($manager_section->id));

        /** @var section_element_response $element_response */
        $element_response = $data_provider->build()->get_section_element_responses()->first();

        static::assertEquals('Manager', $element_response->get_relationship_name());

        /** @var responder_group $other_manager_responder_group */
        $other_manager_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Manager';
        });

        /** @var responder_group $appraiser_responder_group */
        $subject_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Subject';
        });

        // There should always be two groups, the subject and another for either appraiser/manager group.
        self::assertCount(2, $element_response->get_other_responder_groups());

        // Note these are all empty responses.
        self::assertCount(1, $subject_responder_group->get_responses());

        self::assertCount(1, $other_manager_responder_group->get_responses());

        /** @var section_element_response $other_managers_response */
        $other_managers_response = $other_manager_responder_group->get_responses()->first();

        self::assertEquals(
            $other_managers_response->get_participant_instance()->participant_id,
            $other_manager_user->id,
            'Only the other manager should be included in the "manager" other responder group'
        );
    }

    /**
     * This simulates the case where within one job assignment a user has the same manager and appraiser.
     *
     * @param int $expected_manager_count
     * @param int $expected_appraiser_count
     * @param string[] $relationship_class_names
     * @throws coding_exception
     */
    public function test_responder_group_population_same_user_is_manager_and_appraiser(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = user::logged_in();
        $manager_appraiser_user = self::getDataGenerator()->create_user();

        [$subject_section] = $generator->create_section_with_combined_manager_appraiser($subject_user, $manager_appraiser_user);

        $data_provider = new participant_section_with_responses(participant_section::load_by_id($subject_section->id));

        /** @var section_element_response $element_response */
        $element_response = $data_provider->build()->get_section_element_responses()->first();

        static::assertEquals('Subject', $element_response->get_relationship_name());

        /** @var responder_group $manager_responder_group */
        $manager_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Manager';
        });

        /** @var responder_group $appraiser_responder_group */
        $appraiser_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Appraiser';
        });

        // There should always be two groups, the manager and appraiser group.
        self::assertCount(2, $element_response->get_other_responder_groups());

        // Note these are all empty responses.
        self::assertCount(1, $manager_responder_group->get_responses());
        self::assertCount(1, $appraiser_responder_group->get_responses());

        /** @var participant_instance $manager_participant_instance */
        $manager_participant_instance = $manager_responder_group->get_responses()->first()->get_participant_instance();

        /** @var participant_instance $appraiser_participant_instance */
        $appraiser_participant_instance = $appraiser_responder_group->get_responses()->first()->get_participant_instance();


        self::assertEquals($manager_appraiser_user->id, $manager_participant_instance->participant_id);
        self::assertEquals($manager_appraiser_user->id, $appraiser_participant_instance->participant_id);

        self::assertNotEquals(
            $manager_participant_instance->get_id(),
            $appraiser_participant_instance->get_id(),
            'Manager and appraiser relationship should have separate participant instances'
        );
    }

    public function test_responder_group_population_for_anonymous_activity(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = user::logged_in();
        $manager_appraiser_user = self::getDataGenerator()->create_user();

        $subject_instance = $generator->create_subject_instance(
            [
                'activity_name'            => 'anonymous activity',
                'subject_is_participating' => false, // The subject actually is participating, but we will create the instance below.
                'subject_user_id'          => $subject_user->id,
                'other_participant_id'     => null,
                'include_questions'        => false,
                'anonymous_responses'      => 'true',
            ]
        );

        $activity = new activity($subject_instance->activity());
        $section = $generator->create_section($activity, ['title' => 'Part one']);

        $manager_section_relationship = $generator->create_section_relationship(
            $section,
            ['relationship' => constants::RELATIONSHIP_MANAGER]
        );
        $appraiser_section_relationship = $generator->create_section_relationship(
            $section,
            ['relationship' => constants::RELATIONSHIP_APPRAISER]
        );
        $subject_section_relationship = $generator->create_section_relationship(
            $section,
            ['relationship' => constants::RELATIONSHIP_SUBJECT]
        );

        $element = $generator->create_element(['title' => 'Question one']);
        $generator->create_section_element($section, $element);

        $generator->create_participant_instance_and_section(
            $activity,
            $manager_appraiser_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->core_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $manager_appraiser_user,
            $subject_instance->id,
            $section,
            $appraiser_section_relationship->core_relationship_id
        );

        $subject_section = $generator->create_participant_instance_and_section(
            $activity,
            $subject_user,
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $data_provider = new participant_section_with_responses(participant_section::load_by_entity($subject_section));

        /** @var section_element_response $element_response */
        $element_response = $data_provider->build()->get_section_element_responses()->first();

        static::assertEquals('Subject', $element_response->get_relationship_name());

        /** @var responder_group $manager_responder_group */
        $manager_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Manager';
        });

        /** @var responder_group $appraiser_responder_group */
        $appraiser_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'Appraiser';
        });

        $anonymous_responder_group = $element_response->get_other_responder_groups()->find(function (responder_group $group) {
            return $group->get_relationship_name() === 'anonymous';
        });

        // There should always one group
        self::assertCount(1, $element_response->get_other_responder_groups());

        // Note these are all empty responses.
        self::assertEmpty($manager_responder_group);
        self::assertEmpty($appraiser_responder_group);

        // anonymous group contains all data
        self::assertCount(2, $anonymous_responder_group->get_responses());
    }

    /**
     * Test none visibility condition always allows other responses to be viewed.
     *
     * @return void
     */
    public function test_none_visibility_conditions_applies(): void {
        $activity = $this->create_activity();
        $this->update_activity_visibility_condition($activity, none::VALUE);

        $subject_participant_section = $this->get_subject_participant_section();
        self::setUser($subject_participant_section->participant_instance->participant_id);
        $subject_participant_section_availabilities = [open::get_code(), closed::get_code()];

        foreach ($subject_participant_section_availabilities as $availability) {
            $this->set_participant_instance_availability($subject_participant_section->participant_instance_id, $availability);
            $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, open::get_code());
            $subject_participant_section->refresh();

            // all other participant_sections open.
            $this->assert_responder_groups_are_not_empty($subject_participant_section);

            // Set 1 of the other participant_sections as closed.
            $this->set_one_of_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
            $this->assert_responder_groups_are_not_empty($subject_participant_section);

            // Set all the other participant_sections as closed.
            $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
            $this->assert_responder_groups_are_not_empty($subject_participant_section);
        }
    }

    /**
     * Test own response visibility condition always allows other responses to be viewed only when
     * participant's instance has been closed.
     *
     * @return void
     */
    public function test_own_response_visibility_condition_applies(): void {
        $activity = $this->create_activity();
        $this->update_activity_visibility_condition($activity, own_response::VALUE);

        $subject_participant_section = $this->get_subject_participant_section();
        $subject_participant_section_availabilities = [open::get_code(), closed::get_code()];
        self::setUser($subject_participant_section->participant_instance->participant_id);

        foreach ($subject_participant_section_availabilities as $availability) {
            $this->set_participant_instance_availability($subject_participant_section->participant_instance_id, $availability);
            $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, open::get_code());
            $subject_participant_instance = participant_instance::load_by_id($subject_participant_section->participant_instance_id);

            // all other participant_sections open.
            $subject_participant_instance->get_availability_state()::get_code() === open::get_code()
                ? $this->assert_responder_groups_are_empty($subject_participant_section)
                : $this->assert_responder_groups_are_not_empty($subject_participant_section);

            // Set 1 of the other participant_sections as closed.
            $this->set_one_of_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
            $subject_participant_instance->get_availability_state()::get_code() === open::get_code()
                ? $this->assert_responder_groups_are_empty($subject_participant_section)
                : $this->assert_responder_groups_are_not_empty($subject_participant_section);

            // Set all the other participant_sections as closed.
            $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
            $subject_participant_instance->get_availability_state()::get_code() === open::get_code()
                ? $this->assert_responder_groups_are_empty($subject_participant_section)
                : $this->assert_responder_groups_are_not_empty($subject_participant_section);
        }
    }

    /**
     * Test own response visibility condition always allows other responses to be viewed only when
     * all participant instances for the subject has been closed.
     *
     * @return void
     */
    public function test_all_responses_visibility_condition_applies(): void {
        $activity = $this->create_activity();
        $this->update_activity_visibility_condition($activity, all_responses::VALUE);

        $subject_participant_section = $this->get_subject_participant_section();
        self::setUser($subject_participant_section->participant_instance->participant_id);

        $availability = open::get_code();
        $this->set_participant_instance_availability($subject_participant_section->participant_instance_id, $availability);
        $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, open::get_code());

        // all other participant_sections open.
        $this->assert_responder_groups_are_empty($subject_participant_section);

        // Set 1 of the other participant_sections as closed.
        $this->set_one_of_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
        $this->assert_responder_groups_are_empty($subject_participant_section);

        // Set all the other participant_sections as closed.
        $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
        $this->assert_responder_groups_are_empty($subject_participant_section);


        //test when subject participant section is closed.
        $availability = closed::get_code();
        $this->set_participant_instance_availability($subject_participant_section->participant_instance_id, $availability);
        $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, open::get_code());

        // all other participant_sections open.
        $this->assert_responder_groups_are_empty($subject_participant_section);

        // Set 1 of the other participant_sections as closed.
        $this->set_one_of_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
        $this->assert_responder_groups_are_empty($subject_participant_section);

        // Set all the other participant_sections as closed.
        $this->set_all_other_participant_instances_availability($subject_participant_section->participant_instance_id, closed::get_code());
        $this->assert_responder_groups_are_not_empty($subject_participant_section);

        // test when activity is anonymous
        $this->update_activity_anonymous_setting($activity, true);
        $this->assert_responder_groups_are_not_empty($subject_participant_section);
    }

    /**
     * Confirms the responder groups are empty.
     *
     * @param participant_section $participant_section
     * @return void
     */
    private function assert_responder_groups_are_empty(participant_section $participant_section): void {
        $selected_participant_section = participant_section::load_by_id($participant_section->id);
        $data_provider = new participant_section_with_responses($selected_participant_section);
        foreach ($data_provider->build()->get_section_element_responses() as $section_element_response) {
            $this->assertTrue($section_element_response->get_other_responder_groups()->count() === 0);
        }
    }

    /**
     * Confirms the responder groups are not empty.
     *
     * @param participant_section $participant_section
     * @return void
     */
    private function assert_responder_groups_are_not_empty(participant_section $participant_section) {
        $selected_participant_section = participant_section::load_by_id($participant_section->id);
        $data_provider = new participant_section_with_responses($selected_participant_section);
        foreach ($data_provider->build()->get_section_element_responses() as $section_element_response) {
            $this->assertTrue($section_element_response->get_other_responder_groups()->count() > 0);
        }
    }

    /**
     * Gets the subject user's participant section.
     *
     * @return participant_section
     */
    private function get_subject_participant_section(): participant_section {
        $subject_instance = subject_instance::repository()->get()->first();

        /**@var participant_instance_entity $subject_participant_instance*/
        $subject_participant_instance = participant_instance_entity::repository()
            ->where('participant_source', participant_source::INTERNAL)
            ->where('participant_id', $subject_instance->subject_user_id)
            ->get()
            ->first();
        return participant_section::load_by_entity($subject_participant_instance->participant_sections->first());
    }

    /**
     * Creates activity used for visibility conditions tests.
     *
     * @return activity
     * @throws coding_exception
     */
    private function create_activity(): activity {
        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity_config = new mod_perform_activity_generator_configuration();
        $activity_config->set_number_of_elements_per_section(2)
            ->set_relationships_per_section([constants::RELATIONSHIP_SUBJECT, constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_APPRAISER])
            ->set_activity_status(active::get_code())
            ->set_number_of_users_per_user_group_type(1)
            ->enable_appraiser_for_each_subject_user()
            ->enable_manager_for_each_subject_user();
        $activities = $generator->create_full_activities($activity_config);

        return $activities->first();
    }

    /**
     * Set availability of one of the other participant instances.
     *
     * @param int $participant_instance_id
     * @param int $availability
     * @return void
     */
    private function set_one_of_other_participant_instances_availability(int $participant_instance_id, int $availability): void {
        participant_instance_entity::repository()
            ->where('id', '!=', $participant_instance_id)
            ->get()
            ->first()
            ->set_attribute('availability', $availability)
            ->update();
    }

    /**
     * Set availability of all the other participant instances.
     *
     * @param int $participant_instance_id
     * @param int $availability
     * @return void
     */
    private function set_all_other_participant_instances_availability(int $participant_instance_id, int $availability): void {
        participant_instance_entity::repository()
            ->where('id', '!=', $participant_instance_id)
            ->update([
                'availability' => $availability
            ]);
    }

    /**
     * Set availability of the other participant instance.
     *
     * @param int $participant_instance_id
     * @param int $availability
     * @return void
     */
    private function set_participant_instance_availability(int $participant_instance_id, int $availability): void {
        participant_instance_entity::repository()
            ->where('id', $participant_instance_id)
            ->update([
                'availability' => $availability
            ]);
    }

    /**
     * Updates activity visibility condition.
     *
     * @param $activity
     * @param $visibility_condition
     * @return void
     */
    private function update_activity_visibility_condition($activity, $visibility_condition): void {
        // set to draft state to update visibility conditions.
        activity_entity::repository()
            ->where('id', $activity->id)
            ->update([
                'status' => draft::get_code(),
            ]);
        self::setAdminUser();
        $activity->update_visibility_condition($visibility_condition);
        $activity->activate();
    }

    /**
     * Update the anonymous setting of an activity.
     *
     * @param $activity
     * @param bool $value
     * @return void
     */
    private function update_activity_anonymous_setting($activity, bool $value): void {
        activity_entity::repository()
            ->where('id', $activity->id)
            ->update([
                'anonymous_responses' => $value,
            ]);
    }

    protected static function assert_same_participant_section(participant_section $expected, participant_section $other): void {
        self::assertEquals(
            $expected->id,
            $other->id
        );

        self::assertEquals(
            $expected->get_section()->id,
            $other->get_section()->id
        );

        self::assertEquals(
            $expected->get_participant_instance()->get_id(),
            $other->get_participant_instance()->get_id()
        );
    }

}