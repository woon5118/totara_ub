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

use core\orm\collection;
use core\webapi\execution_context;
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\models\activity\subject_instance;
use mod_perform\webapi\resolver\query\subject_instances;


/**
 * @group perform
 */
class webapi_resolver_query_subject_instances_testcase extends advanced_testcase {

    public function test_get_subject_instances_unfiltered(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_subject_instances($target_user);

        self::setUser($target_user);

        $return_subject_instances = subject_instances::resolve([], $this->get_execution_context());

        self::assertCount(2, $return_subject_instances);

        $this->assert_in_result($activity_about_target_user, $return_subject_instances);
        $this->assert_in_result($activity_about_someone_else, $return_subject_instances);

        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_subject_instances);
    }

    public function test_get_subject_instances_only_about_target_user(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_subject_instances($target_user);

        self::setUser($target_user);

        $filters = ['about' => [subject_instances_about::VALUE_ABOUT_SELF]];
        $return_subject_instances = subject_instances::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(1, $return_subject_instances);

        $this->assert_in_result($activity_about_target_user, $return_subject_instances);

        $this->assert_not_in_result($activity_about_someone_else, $return_subject_instances);
        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_subject_instances);
    }

    public function test_get_subject_instances_only_about_other_users(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_subject_instances($target_user);

        self::setUser($target_user);

        $filters = ['about' => [subject_instances_about::VALUE_ABOUT_OTHERS]];
        $return_subject_instances = subject_instances::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(1, $return_subject_instances);

        $this->assert_in_result($activity_about_someone_else, $return_subject_instances);

        $this->assert_not_in_result($activity_about_target_user, $return_subject_instances);
        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_subject_instances);
    }

    public function test_get_user_about_self_and_others_via_all_filter_options(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_subject_instances($target_user);

        self::setUser($target_user);

        $filters = ['about' => [subject_instances_about::VALUE_ABOUT_SELF, subject_instances_about::VALUE_ABOUT_OTHERS]];
        $return_subject_instances = subject_instances::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(2, $return_subject_instances);

        $this->assert_in_result($activity_about_someone_else, $return_subject_instances);
        $this->assert_in_result($activity_about_target_user, $return_subject_instances);

        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_subject_instances);
    }

    private function assert_in_result($expected_to_find , collection $return_subject_instances): void {
        self::assertInstanceOf(
            subject_instance::class,
            $return_subject_instances->find($this->by_subject_instance($expected_to_find))
        );
    }

    private function assert_not_in_result($expected_not_to_find , collection $return_subject_instances): void {
        self::assertNull($return_subject_instances->find($this->by_subject_instance($expected_not_to_find)));
    }

    /**
     * Filter function to find an user activity from a perform activity.
     *
     * @param subject_instance $subject_instance
     * @return callable
     */
    private function by_subject_instance(subject_instance $subject_instance): callable {
        $subject_instance_id = $subject_instance->get_activity()->id;

        return function (subject_instance $subject_instance) use ($subject_instance_id) {
            return $subject_instance->get_activity()->id === $subject_instance_id;
        };
    }

    /**
     * @param $target_user
     * @return subject_instance[]
     * @throws coding_exception
     */
    protected function create_subject_instances($target_user): array {
        $other_subject = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        $activity_about_target_user = $this->perform_generator()->create_subject_instance([
            'activity_name' => 'activity_about_target_user',
            'subject_user_id' => $target_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => true,
        ]);

        $activity_target_user_is_not_participating_in = $this->perform_generator()->create_subject_instance([
            'activity_name' => 'activity_target_user_is_not_participating_in',
            'subject_user_id' => $target_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => false,
        ]);

        $activity_about_someone_else = $this->perform_generator()->create_subject_instance([
            'activity_name' => 'activity_about_someone_else',
            'subject_user_id' => $other_subject->id,
            'other_participant_id' => $target_user->id,
            'subject_is_participating' => false,
        ]);

        return [$activity_about_target_user, $activity_target_user_is_not_participating_in, $activity_about_someone_else];
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    protected function perform_generator(): \mod_perform_generator {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::getDataGenerator()->get_plugin_generator('mod_perform');
    }

}