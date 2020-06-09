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
use mod_perform\data_providers\response\participant_section_with_responses;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\response\participant_section;
use mod_perform\models\response\responder_group;
use mod_perform\models\response\section_element_response;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $data_provider->fetch();

        /** @var participant_section $fetched_participant_section */
        $fetched_participant_section = $data_provider->get();

        self::assert_same_participant_section($participant_section, $fetched_participant_section);

        $responses = $data_provider->get()->get_section_element_responses();
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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $responses =  $data_provider->fetch()->get()->get_section_element_responses();
        self::assertCount(2, $responses);

        // Set answers on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            $response->set_response_data($question_number);
            $response->save();
        }

        $responses =  $data_provider->fetch()->get()->get_section_element_responses();
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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $main_responses =  $data_provider->fetch()->get()->get_section_element_responses();
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

        $main_responses =  $data_provider->fetch()->get()->get_section_element_responses();
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

    public function test_cant_get_another_users_section_and_responses(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $another_user = self::getDataGenerator()->create_user();

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

        $data_provider = new participant_section_with_responses($another_user->id, $participant_section->id);

        $fetched_participant_section = $data_provider->fetch()->get();

        self::assertNull($fetched_participant_section);
    }

    /**
     * This covers the case where someone has one, none, or multiple job assignments so they can have any combinations
     * of managers or appraisers.
     *
     * @param int $expected_manager_count
     * @param int $expected_appraiser_count
     * @param string[] $relationship_class_names
     * @throws coding_exception
     * @dataProvider responder_group_population_provider
     */
    public function test_responder_group_population_for_subject(
        int $expected_manager_count,
        int $expected_appraiser_count,
        array $relationship_class_names
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
        $manager_section_relationship = $generator->create_section_relationship($section, ['class_name' => manager::class]);
        $appraiser_section_relationship = $generator->create_section_relationship($section, ['class_name' => appraiser::class]);
        $subject_section_relationship = $generator->create_section_relationship($section, ['class_name' => subject::class]);

        $element = $generator->create_element(['title' => 'Question one']);
        $generator->create_section_element($section, $element);

        foreach ($relationship_class_names as $relationship_class_name) {
            if ($relationship_class_name === manager::class) {
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
            $subject_user->to_the_origins(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $data_provider = new participant_section_with_responses($subject_user_id, $subject_section->id);
        $fetched_participant_section = $data_provider->fetch()->get();

        /** @var section_element_response $element_response */
        $element_response = $fetched_participant_section->get_section_element_responses()->first();

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
        self::assertCount($expected_manager_count, $manager_responder_group->get_responses());
        self::assertCount($expected_appraiser_count, $appraiser_responder_group->get_responses());
    }

    public function responder_group_population_provider(): array {
        return [
            'Two managers, one appraisers' => [2, 1, [manager::class, manager::class, appraiser::class]],
            'Two appraisers, one managers' => [1, 2, [manager::class, appraiser::class, appraiser::class]],
            'One manager, no appraiser' => [1, 0, [manager::class]],
            'No manager, no appraiser' => [0, 0, []],
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

        $manager_section_relationship = $generator->create_section_relationship($section, ['class_name' => manager::class]);
        $appraiser_section_relationship = $generator->create_section_relationship($section, ['class_name' => appraiser::class]);
        $subject_section_relationship = $generator->create_section_relationship($section, ['class_name' => subject::class]);

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
            $subject_user->to_the_origins(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $user_id = $fetching_as === 'Manager' ? $manager_user->id : $appraiser_user->id;
        $participant_section_id = $fetching_as === 'Manager' ? $manager_section->id : $appraiser_section->id;

        $data_provider = new participant_section_with_responses($user_id, $participant_section_id);
        $fetched_participant_section = $data_provider->fetch()->get();

        /** @var section_element_response $element_response */
        $element_response = $fetched_participant_section->get_section_element_responses()->first();

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

        $manager_section_relationship = $generator->create_section_relationship($section, ['class_name' => manager::class]);
        $subject_section_relationship = $generator->create_section_relationship($section, ['class_name' => subject::class]);

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
            $subject_user->to_the_origins(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->core_relationship_id
        );

        $user_id = $manager_user->id;
        $participant_section_id = $manager_section->id;

        $data_provider = new participant_section_with_responses($user_id, $participant_section_id);
        $fetched_participant_section = $data_provider->fetch()->get();

        /** @var section_element_response $element_response */
        $element_response = $fetched_participant_section->get_section_element_responses()->first();

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
        $subject_user_id = $subject_user->id;
        $manager_appraiser_user = self::getDataGenerator()->create_user();

        [$subject_section] = $generator->create_section_with_combined_manager_appraiser($subject_user, $manager_appraiser_user);

        $data_provider = new participant_section_with_responses($subject_user_id, $subject_section->id);
        $fetched_participant_section = $data_provider->fetch()->get();

        /** @var section_element_response $element_response */
        $element_response = $fetched_participant_section->get_section_element_responses()->first();

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