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
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\entities\activity\participant_instance;
use mod_perform\models\activity\subject_instance;
use totara_webapi\graphql;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @group perform
 */
class mod_perform_webapi_resolver_query_subject_instances_testcase extends advanced_testcase {

    public function test_query_successful(): void {
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_full_activities()->first();
        /** @var participant_instance $participant_instance */
        $participant_instance = participant_instance::repository()->get()->first();
        $subject_instance = subject_instance::load_by_id($participant_instance->subject_instance_id);

        self::setUser($participant_instance->participant_id);

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_SELF]
            ]
        ];

        $result = graphql::execute_operation(
            execution_context::create('ajax', 'mod_perform_subject_instances'),
            $args
        )->toArray(true);

        $actual = $result['data']['mod_perform_subject_instances'];

        $expected = [
            [
                'id' => (int) $subject_instance->id,
                'progress_status' => $subject_instance->get_progress_status(),
                'activity' => [
                    'name' => $activity->name
                ],
                'subject_user' => [
                    'fullname' => $subject_instance->subject_user->fullname
                ],
                'participant_instances' => [
                    [
                        'progress_status' => 'NOT_STARTED',
                        'relationship_name' => 'Subject',
                        'participant_id' => $participant_instance->participant_id,
                    ]
                ],
            ]
        ];

        self::assertEquals($expected, $actual);
    }

    public function test_query_invalid_filter(): void {
        $args = [
            'filters' => [
                'not_real_filter' => 1,
            ],
        ];

        $errors = graphql::execute_operation(
            execution_context::create('ajax', 'mod_perform_subject_instances'),
            $args
        )->errors;

        self::assertCount(1, $errors);

        $expected_error_message = 'Variable "$filters" got invalid value {"not_real_filter":1}; ';
        $expected_error_message .= 'Field "not_real_filter" is not defined by type mod_perform_subject_instance_filters.';

        self::assertEquals(
            $expected_error_message,
            $errors[0]->message
        );
    }

}