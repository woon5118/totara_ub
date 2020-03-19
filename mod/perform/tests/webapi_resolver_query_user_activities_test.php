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
use mod_perform\entities\activity\filters\user_activities_about;
use mod_perform\models\activity\user_activity;
use mod_perform\webapi\resolver\query\user_activities;


/**
 * @group perform
 */
class webapi_resolver_query_user_activities_testcase extends advanced_testcase {

    public function test_get_user_activities_unfiltered(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_user_activities($target_user);

        self::setUser($target_user);

        $return_user_activities = user_activities::resolve([], $this->get_execution_context());

        self::assertCount(2, $return_user_activities);

        $this->assert_in_result($activity_about_target_user, $return_user_activities);
        $this->assert_in_result($activity_about_someone_else, $return_user_activities);

        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_user_activities);
    }

    public function test_get_user_activities_only_about_target_user(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_user_activities($target_user);

        self::setUser($target_user);

        $filters = ['about' => [user_activities_about::VALUE_ABOUT_SELF]];
        $return_user_activities = user_activities::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(1, $return_user_activities);

        $this->assert_in_result($activity_about_target_user, $return_user_activities);

        $this->assert_not_in_result($activity_about_someone_else, $return_user_activities);
        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_user_activities);
    }

    public function test_get_user_activities_only_about_other_users(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_user_activities($target_user);

        self::setUser($target_user);

        $filters = ['about' => [user_activities_about::VALUE_ABOUT_OTHERS]];
        $return_user_activities = user_activities::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(1, $return_user_activities);

        $this->assert_in_result($activity_about_someone_else, $return_user_activities);

        $this->assert_not_in_result($activity_about_target_user, $return_user_activities);
        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_user_activities);
    }

    public function test_get_user_about_self_and_others_via_all_filter_options(): void {
        self::setAdminUser();

        $target_user = self::getDataGenerator()->create_user();

        [
            $activity_about_target_user,
            $activity_target_user_is_not_participating_in,
            $activity_about_someone_else
        ] = $this->create_user_activities($target_user);

        self::setUser($target_user);

        $filters = ['about' => [user_activities_about::VALUE_ABOUT_SELF, user_activities_about::VALUE_ABOUT_OTHERS]];
        $return_user_activities = user_activities::resolve(['filters' => $filters], $this->get_execution_context());

        self::assertCount(2, $return_user_activities);

        $this->assert_in_result($activity_about_someone_else, $return_user_activities);
        $this->assert_in_result($activity_about_target_user, $return_user_activities);

        $this->assert_not_in_result($activity_target_user_is_not_participating_in, $return_user_activities);
    }

    private function assert_in_result($expected_to_find , collection $return_user_activities): void {
        self::assertInstanceOf(
            user_activity::class,
            $return_user_activities->find($this->by_user_activity($expected_to_find))
        );
    }

    private function assert_not_in_result($expected_not_to_find , collection $return_user_activities): void {
        self::assertNull($return_user_activities->find($this->by_user_activity($expected_not_to_find)));
    }

    /**
     * Filter function to find an user activity from a perform activity.
     *
     * @param user_activity $user_activity
     * @return callable
     */
    private function by_user_activity(user_activity $user_activity): callable {
        $user_activity_id = $user_activity->get_activity()->id;

        return function (user_activity $user_activity) use ($user_activity_id) {
            return $user_activity->get_activity()->id === $user_activity_id;
        };
    }

    /**
     * @param $target_user
     * @return user_activity[]
     * @throws coding_exception
     */
    protected function create_user_activities($target_user): array {
        $other_subject = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        $activity_about_target_user = $this->perform_generator()->create_user_activity([
            'activity_name' => 'activity_about_target_user',
            'subject_user_id' => $target_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => true,
        ]);

        $activity_target_user_is_not_participating_in = $this->perform_generator()->create_user_activity([
            'activity_name' => 'activity_target_user_is_not_participating_in',
            'subject_user_id' => $target_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => false,
        ]);

        $activity_about_someone_else = $this->perform_generator()->create_user_activity([
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