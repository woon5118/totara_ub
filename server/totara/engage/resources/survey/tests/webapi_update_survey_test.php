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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package engage_survey
 */
defined('MOODLE_INTERNAL') || die();

use totara_engage\answer\answer_type;
use engage_survey\totara_engage\resource\survey;
use totara_webapi\phpunit\webapi_phpunit_helper;

class engage_survey_webapi_update_survey_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_survey_answer_validation(): void {
        $this->setAdminUser();
        /** @var totara_topic_generator $topicgen */
        $topicgen = $this->getDataGenerator()->get_plugin_generator('totara_topic');
        $topics[] = $topicgen->create_topic('topic1')->get_id();
        $topics[] = $topicgen->create_topic('topic2')->get_id();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var survey $survey */
        $survey = survey::create(
            [
                'questions' => [
                    [
                        'value' => "Hello world ?",
                        'answertype' => answer_type::SINGLE_CHOICE,
                        'options' => [
                            'Yes',
                            'no'
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            81,
            core_text::strlen("OoCtvoljRosLba2P8FxNULYk41c6KSdeSGIX3IAj15ayYsbIvS3bSoZubTTwxugQOACkrPMbvHeNmC8E5")
        );

        $questions = $survey->get_questions();
        $question = reset($questions);

        $args = [
            'resourceid' => $survey->get_id(),
            'questions' => [
                [
                    'id' => $question->get_id(),
                    'value' => 'Is this not a hello world',
                    'answertype' => answer_type::SINGLE_CHOICE,
                    'options' => [
                        'Yes',
                        'OoCtvoljRosLba2P8FxNULYk41c6KSdeSGIX3IAj15ayYsbIvS3bSoZubTTwxugQOACkrPMbvHeNmC8E5'
                    ]
                ]
            ],
            'access' => 'PUBLIC',
            'topics' => $topics
        ];

        $this->expectException('coding_exception');
        $this->expectExceptionMessage("Coding error detected, it must be fixed by a programmer: Validation run for property 'questions' has been failed");
        $this->resolve_graphql_mutation(
            'engage_survey_update',
            $args
        );
    }

    /**
     * @return void
     */
    public function test_survey_question_validation_via_graphql(): void {
        $this->setAdminUser();
        /** @var totara_topic_generator $topicgen */
        $topicgen = $this->getDataGenerator()->get_plugin_generator('totara_topic');
        $topics[] = $topicgen->create_topic('topic1')->get_id();
        $topics[] = $topicgen->create_topic('topic2')->get_id();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var survey $survey */
        $survey = survey::create(
            [
                'questions' => [
                    [
                        'value' => "Hello world ?",
                        'answertype' => answer_type::SINGLE_CHOICE,
                        'options' => [
                            'Yes',
                            'no'
                        ]
                    ]
                ]
            ]
        );

        $questions = $survey->get_questions();
        $question = reset($questions);

        $args = [
            'resourceid' => $survey->get_id(),
            'questions' => [
                [
                    'id' => $question->get_id(),
                    'value' => "TfIKQ8IXoycfkcbGaav6B1XVVibwtIYTlyGIOiJukJ4xVOVd4dlbDBnVioSmM5LwdJ7lEv7MCNax",
                    'answertype' => answer_type::SINGLE_CHOICE,
                    'options' => [
                        'Yes',
                        'No'
                    ]
                ]
            ],
            'access' => 'PUBLIC',
            'topics' => $topics
        ];

        $this->assertEquals(
            76,
            core_text::strlen("TfIKQ8IXoycfkcbGaav6B1XVVibwtIYTlyGIOiJukJ4xVOVd4dlbDBnVioSmM5LwdJ7lEv7MCNax")
        );

        $this->expectException('coding_exception');
        $this->expectExceptionMessage("Coding error detected, it must be fixed by a programmer: Validation run for property 'questions' has been failed");
        $this->resolve_graphql_mutation(
            'engage_survey_create',
            $args
        );
    }

}