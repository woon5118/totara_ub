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

use core\webapi\execution_context;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\subject_instance;
use mod_perform\entities\activity\participant_section;

/**
 * Class mod_perform_subject_instance_testcase
 *
 * @group perform
 */
abstract class mod_perform_subject_instance_testcase extends advanced_testcase {

    /** @var stdClass */
    protected static $user;

    /** @var subject_instance */
    protected static $about_user_and_participating;

    /** @var subject_instance */
    protected static $about_someone_else_and_participating;

    /** @var subject_instance That the self::$user is about someone else but the user is a participant */
    protected static $about_user_but_not_participating;

    /** @var subject_instance A user activity that doesn't actually exist in the database anymore */
    protected static $non_existing;

    protected function setUp() {
        self::$user = self::getDataGenerator()->create_user();

        self::create_user_activities(self::$user);

        self::setUser(self::$user);
    }

    protected function tearDown() {
        parent::tearDown();

        self::$user = null;
        self::$about_someone_else_and_participating = null;
        self::$about_user_and_participating = null;
        self::$about_user_but_not_participating = null;
        self::$non_existing = null;
    }

    /**
     * @param $target_user
     * @throws coding_exception
     */
    protected static function create_user_activities($target_user): void {
        // Change to the Admin user for creating the perform activity container.
        self::setAdminUser();

        $other_subject = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        self::$about_user_and_participating = subject_instance::load_by_entity( self::perform_generator()->create_subject_instance([
            'activity_name' => 'activity_about_target_user',
            'subject_user_id' => $target_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => true,
        ]));

        self::$about_user_but_not_participating = subject_instance::load_by_entity(
            self::perform_generator()->create_subject_instance([
                'activity_name' => 'activity_target_user_is_not_participating_in',
                'subject_user_id' => $target_user->id,
                'other_participant_id' => $other_participant->id,
                'subject_is_participating' => false,
            ])
        );

        self::$about_someone_else_and_participating = subject_instance::load_by_entity(
            self::perform_generator()->create_subject_instance([
                'activity_name' => 'activity_about_someone_else',
                'subject_user_id' => $other_subject->id,
                'other_participant_id' => $target_user->id,
                'subject_is_participating' => false,
            ])
        );

        self::$non_existing = subject_instance::load_by_entity(
            self::perform_generator()->create_subject_instance([
                'activity_name' => 'subject_instance_will_be_deleted',
                'subject_user_id' => $other_subject->id,
                'other_participant_id' => $target_user->id,
                'subject_is_participating' => false,
            ])
        );

        foreach (self::$non_existing->get_participant_instances() as $participant_instance) {
            participant_section::repository()->where('participant_instance_id', $participant_instance->get_id())->delete();
        }
        $subject_instance_id = self::$non_existing->id;
        (new subject_instance_entity($subject_instance_id))->delete();
    }

    /**
     * A data provider for most the primary user activity subject/participant combinations.
     *
     * Because data providers are called before set up, the user activity models must be delivered in closures.
     *
     * @return subject_instance[]
     */
    public function subject_instance_provider(): array {
        return [
            'activity about the user and the user is a participant' => [
                function () {
                    return self::$about_user_and_participating;
                }, true
            ],
            'activity about someone else the user is a participant' => [
                function () {
                    return self::$about_someone_else_and_participating;
                }, true
            ],
            'activity about the user but they are not participating' => [
                function () {
                    return self::$about_user_but_not_participating;
                }, false
            ],
            'activity that does not exist' => [
                function () {
                    return self::$non_existing;
                }, false
            ],
        ];
    }

    /**
     * @param subject_instance $expected
     * @param subject_instance $actual
     */
    protected static function assert_same_subject_instance(subject_instance $expected, subject_instance $actual): void {
        self::assertEquals($expected->id, $actual->id,
            'Expected subject instance ids to match'
        );

        self::assertEquals($expected->subject_user->id, $actual->subject_user->id,
            'Expected subject ids match'
        );

        self::assertEquals($expected->get_activity()->id, $actual->get_activity()->id,
            'Expected activity ids to match'
        );
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    protected function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    protected static function perform_generator(): mod_perform_generator {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::getDataGenerator()->get_plugin_generator('mod_perform');
    }


}