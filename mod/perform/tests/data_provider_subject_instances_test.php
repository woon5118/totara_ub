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

use mod_perform\data_providers\activity\subject_instance;
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\models\activity\subject_instance as subject_instance_model;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @group perform
 */
class data_provider_subject_instances_testcase extends mod_perform_subject_instance_testcase {

    /**
     * Even unfiltered must only return activities the user is participating in.
     */
    public function test_get_unfiltered(): void {
        $returned_subject_instances = (new subject_instance(self::$user->id))
            ->fetch()
            ->get();

        self::assertCount(2, $returned_subject_instances);

        self::assert_same_subject_instance(self::$about_user_and_participating, $returned_subject_instances->first());
        self::assert_same_subject_instance(self::$about_someone_else_and_participating, $returned_subject_instances->last());
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

        self::assert_same_subject_instance(self::$about_user_and_participating, $returned_subject_instances->first());
        self::assert_same_subject_instance(self::$about_someone_else_and_participating, $returned_subject_instances->last());
    }

}