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
 */

use core\entities\user;
use mod_perform\data_providers\activity\subject_instance;
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\entities\activity\participant_instance;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @group perform
 */
class mod_perform_data_provider_subject_instances_testcase extends mod_perform_subject_instance_testcase {

    /**
     * Even unfiltered must only return activities the user is participating in.
     */
    public function test_get_unfiltered(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->fetch()
            ->get();

        self::assertCount(2, $returned_subject_instances);

        self::assert_same_subject_instance(
            self::$about_someone_else_and_participating, $returned_subject_instances->first()
        ); // 538003

        self::assert_same_subject_instance(
            self::$about_user_and_participating, $returned_subject_instances->last()
        ); // 538001
    }

    /**
     * @dataProvider subject_instance_provider
     * @param callable $get_query_activity
     * @param bool $expected_to_be_return
     */
    public function test_get_by_subject_instance_id(callable $get_query_activity, bool $expected_to_be_return): void {
        /** @var subject_instance_model $query_activity */
        $query_activity = $get_query_activity();

        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->set_subject_instance_id_filter($query_activity->get_id())
            ->fetch()
            ->get();

        if ($expected_to_be_return) {
            self::assertCount(1, $returned_subject_instances);
            self::assert_same_subject_instance($query_activity, $returned_subject_instances->first());
        } else {
            self::assertCount(0, $returned_subject_instances);
        }
    }

    public function test_get_only_about_user(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->set_about_filter([subject_instances_about::VALUE_ABOUT_SELF])
            ->fetch()
            ->get();

        self::assertCount(1, $returned_subject_instances);

        self::assert_same_subject_instance(self::$about_user_and_participating, $returned_subject_instances->first());
    }

    public function test_get_subject_instances_only_about_other_users(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->set_about_filter([subject_instances_about::VALUE_ABOUT_OTHERS])
            ->fetch()
            ->get();

        self::assertCount(1, $returned_subject_instances);

        self::assert_same_subject_instance(self::$about_someone_else_and_participating, $returned_subject_instances->first());
    }

    public function test_get_user_about_self_and_others_via_all_filter_options(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->set_about_filter([subject_instances_about::VALUE_ABOUT_SELF, subject_instances_about::VALUE_ABOUT_OTHERS])
            ->fetch()
            ->get();

        self::assertCount(2, $returned_subject_instances);

        self::assert_same_subject_instance(
            self::$about_someone_else_and_participating, $returned_subject_instances->first()
        ); // 538003

        self::assert_same_subject_instance(
            self::$about_user_and_participating, $returned_subject_instances->last()
        ); // 538001
    }

    /**
     * @dataProvider relationship_to_user_data_provider
     */
    public function test_relationships_to_user_population(string $fetching_as): void {
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

        $generator->create_participant_instance_and_section(
            $activity,
            $manager_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->activity_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $appraiser_user,
            $subject_instance->id,
            $section,
            $appraiser_section_relationship->activity_relationship_id
        );

        $subject_section = $generator->create_participant_instance_and_section(
            $activity,
            $subject_user->to_the_origins(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->activity_relationship_id
        );

        $user_id_map = [
            'Self' => $subject_user_id,
            'Manager' => $manager_user->id,
            'Appraiser' => $appraiser_user->id,
        ];

        $data_provider = new subject_instance($user_id_map[$fetching_as]);
        $subject_instances = $data_provider->fetch()->get();

        self::assertCount(1, $subject_instances);

        self::assertEquals($fetching_as, $subject_instances->first()->get_relationship_to_subject());
    }


    public function relationship_to_user_data_provider(): array {
        return [
            'fetching as subject/self' => ['Self'],
            'fetching as manager' => ['Manager'],
            'fetching as appraiser' => ['Appraiser'],
        ];
    }

    public function test_relationships_to_user_population_same_user_is_manager_and_appraiser(): void {
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

        $manager_appraiser_user = self::getDataGenerator()->create_user();

        $generator->create_participant_instance_and_section(
            $activity,
            $manager_appraiser_user,
            $subject_instance->id,
            $section,
            $manager_section_relationship->activity_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $manager_appraiser_user,
            $subject_instance->id,
            $section,
            $appraiser_section_relationship->activity_relationship_id
        );

        $generator->create_participant_instance_and_section(
            $activity,
            $subject_user->to_the_origins(),
            $subject_instance->id,
            $section,
            $subject_section_relationship->activity_relationship_id
        );

        $data_provider = new subject_instance($manager_appraiser_user->id);
        $subject_instances = $data_provider->fetch()->get();

        self::assertCount(1, $subject_instances);

        self::assertEquals('Appraiser', $subject_instances->first()->get_relationship_to_subject());
    }

    /**
     * Check that the result only includes the one participant section for the relevant participant.
     */
    public function test_attaches_only_relevant_participant_instance(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->set_about_filter([subject_instances_about::VALUE_ABOUT_SELF])
            ->fetch()
            ->get();

        $this->assertCount(1, $returned_subject_instances);

        /** @var subject_instance_model $returned_subject_instance */
        $returned_subject_instance = $returned_subject_instances->first();

        // Verify that there are two participant_instances for this subject_instance.
        $participant_instances = participant_instance::repository()
            ->where('subject_instance_id', $returned_subject_instance->get_id())
            ->get();
        $this->assertCount(2, $participant_instances);

        // Verify that only the participant_instance for the subject user is in the result.
        $subject_participant_instances = $participant_instances->filter('participant_id', self::$user->id);
        $returned_participant_instances = $returned_subject_instance->get_participant_instances();
        $this->assertCount(1, $subject_participant_instances);
        $this->assertCount(1, $returned_participant_instances);
        $this->assertSame($subject_participant_instances->first()->id, $returned_participant_instances->first()->get_id());
    }
}