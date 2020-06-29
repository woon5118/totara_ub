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

use mod_perform\models\activity\participant_instance;
use mod_perform\state\participant_instance\not_started;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @coversDefaultClass participant_section
 *
 * @group perform
 */
class mod_perform_webapi_resolver_query_subject_instance_testcase extends mod_perform_subject_instance_testcase {
    private const QUERY = 'mod_perform_subject_instance';

    use webapi_phpunit_helper;

    public function test_query_successful(): void {
        $args = [
            'subject_instance_id' => self::$about_user_and_participating->get_id()
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);
        $actual = $this->get_webapi_operation_data($result);

        $profile_image_small_url = (new \user_picture(
            self::$about_user_and_participating->subject_user->get_record(),
            0
        ))->get_url($GLOBALS['PAGE'])->out(false);

        /** @var participant_instance $subject_participant_instance */
        $subject_participant_instance = self::$about_user_and_participating->participant_instances->find(
            function (participant_instance $pi) {
                return (int) $pi->participant_id === (int) self::$about_user_and_participating->subject_user->id;
            }
        );

        /** @var participant_instance $manager_participant_instance */
        $manager_participant_instance = self::$about_user_and_participating->participant_instances->find(
            function (participant_instance $pi) {
                return (int) $pi->participant_id !== (int) self::$about_user_and_participating->subject_user->id;
            }
        );

        $expected = [
            'id' => (string) self::$about_user_and_participating->id,
            'progress_status' => self::$about_user_and_participating->get_progress_status(),
            'activity' => [
                'name' => self::$about_user_and_participating->get_activity()->name,
                'settings' => [
                    'close_on_completion' => false,
                ]
            ],
            'subject_user' => [
                'id' => self::$about_user_and_participating->subject_user->id,
                'fullname' => self::$about_user_and_participating->subject_user->fullname,
                'profileimageurlsmall' => $profile_image_small_url,
            ],
            'participant_instances' => [
                [
                    'id' => (string)$subject_participant_instance->get_id(),
                    'progress_status' => not_started::get_name(),
                    'core_relationship' => [
                        'name' => 'Subject'
                    ],
                    'participant_id' => $subject_participant_instance->participant_id,
                ],
                [
                    'id' => (string)$manager_participant_instance->get_id(),
                    'progress_status' => not_started::get_name(),
                    'core_relationship' => [
                        'name' => 'Manager'
                    ],
                    'participant_id' => $manager_participant_instance->participant_id,
                ],
            ]
        ];
        self::assertEqualsCanonicalizing($expected['participant_instances'], $actual['participant_instances']);
        unset($expected['participant_instances']);
        unset($actual['participant_instances']);
        self::assertEquals($expected, $actual);
    }

    public function test_failed_ajax_query(): void {
        $args = [
            'subject_instance_id' => self::$about_user_and_participating->get_id()
        ];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed($result, 'subject_instance_id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['subject_instance_id' => 0]);
        $this->assert_webapi_operation_failed($result, 'subject instance id');

        $this->setUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'not logged in');
    }
}