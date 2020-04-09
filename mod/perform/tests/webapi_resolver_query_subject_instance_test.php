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

use totara_webapi\graphql;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @group perform
 */
class mod_perform_webapi_resolver_query_subject_instance_testcase extends mod_perform_subject_instance_testcase {

    public function test_query_successful(): void {
        $args = [
            'subject_instance_id' => self::$about_user_and_participating->get_id()
        ];

        $result = graphql::execute_operation(
            $this->get_execution_context('ajax', 'mod_perform_subject_instance'),
            $args
        )->toArray(true);

        $actual = $result['data']['mod_perform_subject_instance'];

        $profile_image_small_url = (new \user_picture(
            self::$about_user_and_participating->subject_user->to_the_origins(),
            0
        ))->get_url($GLOBALS['PAGE'])->out(false);

        $expected = [
            'id' => self::$about_user_and_participating->id,
            'progress_status' => self::$about_user_and_participating->get_progress_status(),
            'activity' => [
                'name' => self::$about_user_and_participating->get_activity()->name
            ],
            'subject_user' => [
                'fullname' => self::$about_user_and_participating->subject_user->fullname,
                'profileimageurlsmall' => $profile_image_small_url,
            ],
            'relationship_to_subject' => 'Self',
            'is_self' => true,
        ];

        self::assertEquals($expected, $actual);
    }

    public function test_query_missing_id(): void {
        $args = [
            'subject_instance_id' => null
        ];

        $errors = graphql::execute_operation(
            $this->get_execution_context('ajax', 'mod_perform_subject_instance'),
            $args
        )->errors;

        self::assertCount(1, $errors);

        self::assertEquals(
            'Variable "$subject_instance_id" got invalid value null; Expected non-nullable type core_id! not to be null.',
            $errors[0]->message
        );
    }

}