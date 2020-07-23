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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

use core\date_format;
use core\entities\user;
use core\webapi\formatter\field\date_field_formatter;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\expand_task;
use mod_perform\models\activity\activity_setting;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\models\activity\subject_instance;
use mod_perform\models\response\participant_section as participant_section_model;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;
use mod_perform\state\participant_instance\in_progress as participant_instance_in_progress;
use mod_perform\state\participant_instance\not_started as participant_instance_not_started;
use mod_perform\state\participant_instance\open as participant_instance_open;
use mod_perform\state\participant_section\in_progress as section_in_progress;
use mod_perform\state\participant_section\not_started as section_not_started;
use mod_perform\state\participant_section\open;
use mod_perform\state\subject_instance\in_progress as subject_instance_in_progress;
use mod_perform\state\subject_instance\open as subject_instance_open;
use mod_perform\task\service\subject_instance_creation;
use totara_core\advanced_feature;
use totara_core\entities\relationship_resolver;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_query_subject_instances_testcase extends advanced_testcase {

    private const QUERY = 'mod_perform_my_subject_instances';

    use webapi_phpunit_helper;

    public function test_query_successful_with_single_section(): void {
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_full_activities()->first();
        /** @var participant_instance $participant_instance */
        $participant_instance = participant_instance::repository()->get()->first();
        $participant_instance_model = participant_instance_model::load_by_entity($participant_instance);
        $subject_instance = subject_instance::load_by_id($participant_instance->subject_instance_id);

        $subject_relationship = $perform_generator->get_core_relationship(subject::class);

        $participant_id = $participant_instance->participant_id;
        self::setUser($participant_id);

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_SELF]
            ]
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $actual = $this->get_webapi_operation_data($result);
        $this->assertCount(1, $actual, 'wrong subject count');

        $subject = $actual[0];
        $expected_subject = [
            'id' => (string) $subject_instance->id,
            'progress_status' => $subject_instance->get_progress_status(),
            'availability_status' => $subject_instance->get_availability_status(),
            'created_at' => (new date_field_formatter(date_format::FORMAT_DATE, $subject_instance->get_context()))
                ->format($subject_instance->created_at),
            'due_date' => null,
            'is_overdue' => false,
            'activity' => [
                'name' => $activity->name,
                'settings' => [
                    activity_setting::MULTISECTION => false
                ],
                'type' => [
                    'display_name' => $activity->type->display_name
                ],
                'anonymous_responses' => false,
            ],
            'subject_user' => [
                'fullname' => $subject_instance->subject_user->fullname
            ],
            'participant_instances' => [
                [
                    'progress_status' => participant_instance_not_started::get_name(),
                    'core_relationship' => [
                        'id' => $subject_relationship->id,
                        'name' => $subject_relationship->get_name(),
                    ],
                    'participant_id' => $participant_id,
                    'id' => (string) $participant_instance->id,
                    'availability_status' => participant_instance_open::get_name(),
                    'is_overdue' => false,
                ]
            ]
        ];
        $this->assertEquals($expected_subject, $subject['subject']);

        $participant = new user($participant_id);
        $profile_image_url = (new user_picture($participant->get_record(), 0))->get_url($GLOBALS['PAGE'])->out(false);

        $section = $activity->sections->first();
        $expected_section = [
            'section' => [
                'id' => $section->id,
                'display_title' => $section->display_title,
                'sort_order' => 1,
            ],
            'participant_sections' => [
                [
                    'id' => $participant_instance->participant_sections->first()->id,
                    'participant_instance' => [
                        'progress_status' => participant_instance_not_started::get_name(),
                        'participant_id' => $participant_id,
                        'participant' => [
                            'fullname' => $participant->fullname,
                            'profileimageurlsmall' => $profile_image_url
                        ],
                        'core_relationship' => [
                            'id' => $subject_relationship->id,
                            'name' => $subject_relationship->get_name(),
                        ],
                        'is_for_current_user' => true,
                    ],
                    'progress_status' => section_not_started::get_name(),
                    'availability_status' => open::get_name(),
                    'is_overdue' => false,
                ],
            ],
            'can_participate' => true,
        ];

        $this->assertCount(1, $subject["sections"], 'wrong sections count');
        $this->assertEquals($expected_section, $subject['sections'][0]);
    }

    public function test_query_successful_with_single_section_anonymous_responses(): void {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $configuration = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(1)
            ->set_number_of_tracks_per_activity(1)
            ->set_cohort_assignments_per_activity(1)
            ->set_number_of_users_per_user_group_type(1)
            ->enable_appraiser_for_each_subject_user()
            ->enable_anonymous_responses()
            ->set_relationships_per_section([subject::class, manager::class, appraiser::class]);

        $activity = $perform_generator->create_full_activities($configuration)->first();

        $subject_core_relationship_id = $this->get_core_relationship_id(subject::class);

        /** @var participant_instance $subject_participant_instance */
        $subject_participant_instance = participant_instance::repository()
            ->where('core_relationship_id', $subject_core_relationship_id)
            ->one();

        $subject_instance = subject_instance::load_by_id($subject_participant_instance->subject_instance_id);

        $subject_relationship = $perform_generator->get_core_relationship(subject::class);

        $participant_id = $subject_participant_instance->participant_id;
        self::setUser($participant_id);

        $appraiser_core_relationship_id = $this->get_core_relationship_id(appraiser::class);
        $appraiser_participant_instance = participant_instance::repository()
            ->where('core_relationship_id', $appraiser_core_relationship_id)
            ->one();

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_SELF]
            ]
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $actual = $this->get_webapi_operation_data($result);
        $this->assertCount(1, $actual, 'wrong subject count');

        $subject = $actual[0];
        $expected_subject = [
            'id' => (string) $subject_instance->id,
            'progress_status' => $subject_instance->get_progress_status(),
            'availability_status' => $subject_instance->get_availability_status(),
            'created_at' => (new date_field_formatter(date_format::FORMAT_DATE, $subject_instance->get_context()))
                ->format($subject_instance->created_at),
            'due_date' => null,
            'is_overdue' => false,
            'activity' => [
                'name' => $activity->name,
                'settings' => [
                    activity_setting::MULTISECTION => false
                ],
                'type' => [
                    'display_name' => $activity->type->display_name
                ],
                'anonymous_responses' => true,
            ],
            'subject_user' => [
                'fullname' => $subject_instance->subject_user->fullname
            ],
            'participant_instances' => [
                [
                    'progress_status' => participant_instance_not_started::get_name(),
                    'core_relationship' => [
                        'id' => $subject_relationship->id,
                        'name' => $subject_relationship->get_name(),
                    ],
                    'participant_id' => $participant_id,
                    'id' => (string) $subject_participant_instance->id,
                    'availability_status' => participant_instance_open::get_name(),
                    'is_overdue' => false,
                ],
                [
                    'progress_status' => participant_instance_not_started::get_name(),
                    'core_relationship' => null,
                    'participant_id' => null,
                    'id' => (string) $appraiser_participant_instance->id,
                    'availability_status' => participant_instance_open::get_name(),
                    'is_overdue' => false,
                ],
            ]
        ];
        $this->assertEquals($expected_subject, $subject['subject']);

        $participant = new user($participant_id);
        $profile_image_url = (new user_picture($participant->get_record(), 0))->get_url($GLOBALS['PAGE'])->out(false);

        $section = $activity->sections->first();
        $expected_section = [
            'section' => [
                'id' => $section->id,
                'display_title' => $section->display_title,
                'sort_order' => 1,
            ],
            'participant_sections' => [
                [
                    'id' => $subject_participant_instance->participant_sections->first()->id,
                    'participant_instance' => [
                        'progress_status' => participant_instance_not_started::get_name(),
                        'participant_id' => $participant_id,
                        'participant' => [
                            'fullname' => $participant->fullname,
                            'profileimageurlsmall' => $profile_image_url
                        ],
                        'core_relationship' => [
                            'id' => $subject_relationship->id,
                            'name' => $subject_relationship->get_name(),
                        ],
                        'is_for_current_user' => true,
                    ],
                    'progress_status' => section_not_started::get_name(),
                    'availability_status' => open::get_name(),
                    'is_overdue' => false,
                ],
                [
                    'id' => $appraiser_participant_instance->participant_sections->first()->id,
                    'participant_instance' => [
                        'progress_status' => participant_instance_not_started::get_name(),
                        'participant_id' => null,
                        'participant' => null,
                        'core_relationship' => null,
                        'is_for_current_user' => false,
                    ],
                    'progress_status' => section_not_started::get_name(),
                    'availability_status' => open::get_name(),
                    'is_overdue' => false,
                ],
            ],
            'can_participate' => true,
        ];

        $this->assertCount(1, $subject["sections"], 'wrong sections count');
        $this->assertEquals($expected_section, $subject['sections'][0]);
    }

    public function test_get_subject_instances_with_multiple_sections(): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $activity1 = $perform_generator->create_activity_in_container([
            'activity_status' => draft::get_code(),
            'create_track' => true,
            'create_section' => false
        ]);
        // Activate multisection
        $activity1->toggle_multisection_setting(true);

        // Now activate this activity, directly in the database to avoid state change checks
        $activity_entity = activity_entity::repository()->find($activity1->id);
        $activity_entity->status = active::get_code();
        $activity_entity->save();

        // Create sections, deliberately create in different order to test sort order
        $section3 = $perform_generator->create_section($activity1, ['title' => 'Section 3']);
        $section1 = $perform_generator->create_section($activity1, ['title' => 'Section 1']);
        $section2 = $perform_generator->create_section($activity1, ['title' => 'Section 2']);

        $perform_generator->create_section_relationship($section1, ['class_name' => subject::class]);
        $perform_generator->create_section_relationship($section1, ['class_name' => manager::class]);

        // This section should only be answered by the subject
        $perform_generator->create_section_relationship($section2, ['class_name' => subject::class]);

        // This section should only be answered by the manager
        $perform_generator->create_section_relationship($section3, ['class_name' => manager::class]);

        $element = $perform_generator->create_element();
        $perform_generator->create_section_element($section1, $element);

        $element = $perform_generator->create_element();
        $perform_generator->create_section_element($section2, $element);

        $element = $perform_generator->create_element();
        $perform_generator->create_section_element($section3, $element);

        $cohort1 = $this->getDataGenerator()->create_cohort();

        /** @var totara_job_generator $job_generator */
        $job_generator = $this->getDataGenerator()->get_plugin_generator('totara_job');

        $user1 = $this->getDataGenerator()->create_user();
        // This user has user1 as the manager
        [$user2, $job_assignment1] = $job_generator->create_user_and_job([], $user1->id);
        $user3 = $this->getDataGenerator()->create_user();

        // Add two users to the cohort
        cohort_add_member($cohort1->id, $user2->id);
        cohort_add_member($cohort1->id, $user3->id);

        $perform_generator->create_track_assignments_with_existing_groups($activity1->tracks->first(), [$cohort1->id]);

        // Make sure we have the instances
        (new expand_task())->expand_all();
        (new subject_instance_creation())->generate_instances();

        // Newest subject instances at the top of the list
        $subject_instances = subject_instance_entity::repository()
            ->filter_by_activity_id($activity1->id)
            ->where('subject_user_id', $user2->id)
            ->order_by('created_at', 'desc')
            ->order_by('id', 'desc')
            ->get()
            ->map_to(subject_instance::class);

        $this->assertCount(1, $subject_instances);

        /** @var subject_instance $subject_instance */
        $subject_instance = $subject_instances->first();

        $participant_instances = $subject_instance->participant_instances;
        $this->assertCount(2, $participant_instances);

        $subject_relationship = $perform_generator->get_core_relationship(subject::class);
        $manager_relationship = $perform_generator->get_core_relationship(manager::class);

        /** @var participant_instance_model $participant_instance_for_subject */
        $participant_instance_for_subject = $subject_instance->get_participant_instances()
            ->find('core_relationship_id', $subject_relationship->id);
        /** @var participant_section_model $participant_section1_subject */
        $participant_section1_subject = $participant_instance_for_subject->participant_sections->find('section_id', $section1->id);
        /** @var participant_section_model $participant_section2_subject */
        $participant_section2_subject = $participant_instance_for_subject->participant_sections->find('section_id', $section2->id);

        // Now put this section into progress
        $participant_section2_subject->on_participant_access();

        /** @var participant_instance_model $participant_instance_for_manager */
        $participant_instance_for_manager = $subject_instance->get_participant_instances()
            ->find('core_relationship_id', $manager_relationship->id);
        /** @var participant_section_model $participant_section3_manager */
        $participant_section3_manager = $participant_instance_for_manager->participant_sections->find('section_id', $section3->id);
        /** @var participant_section_model $participant_section1_manager */
        $participant_section1_manager = $participant_instance_for_manager->participant_sections->find('section_id', $section1->id);

        $subject_user = new user($user2->id);
        $subject_user_profile_image_url = (new user_picture($user2, 0))->get_url($GLOBALS['PAGE'])->out(false);

        $manager_user = new user($user1->id);
        $manager_user_profile_image_url = (new user_picture($user1, 0))->get_url($GLOBALS['PAGE'])->out(false);


        // Now fetch the users own instances

        $this->setUser($user2);

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_SELF]
            ]
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $actual = $this->get_webapi_operation_data($result);
        $this->assertCount(1, $actual, 'wrong subject count');

        $expected_relationships = [subject::get_name(), manager::get_name()];

        $expected_participant_instances = [];
        foreach ($participant_instances as $participant_instance) {
            // We expect to have one manager and subject, so make sure the database rows
            // match what we expect so that the later assert is correct.
            $core_relationship = $participant_instance->get_core_relationship();
            $this->assertContains($core_relationship->name, $expected_relationships);
            unset($expected_relationships[array_search($core_relationship->name, $expected_relationships)]);

            // Only the subjects participant instance got started
            $state = $core_relationship->get_name() === manager::get_name()
                ? participant_instance_not_started::get_name()
                : participant_instance_in_progress::get_name();

            $expected_participant_instances[] = [
                'progress_status' => $state,
                'core_relationship' => [
                    'id' => $core_relationship->id,
                    'name' => $core_relationship->name,
                ],
                'participant_id' => $participant_instance->participant_id,
                'id' => (string) $participant_instance->id,
                'availability_status' => participant_instance_open::get_name(),
                'is_overdue' => false,
            ];
        }

        $this->assertEmpty($expected_relationships);

        $subject = $actual[0];
        $expected_subject = [
            'id' => $subject_instance->id,
            'progress_status' => subject_instance_in_progress::get_name(),
            'availability_status' => subject_instance_open::get_name(),
            'created_at' => (new date_field_formatter(date_format::FORMAT_DATE, $subject_instance->get_context()))
                ->format($subject_instance->created_at),
            'due_date' => null,
            'is_overdue' => false,
            'activity' => [
                'name' => $activity1->name,
                'settings' => [
                    activity_setting::MULTISECTION => true
                ],
                'type' => [
                    'display_name' => $activity1->type->display_name
                ],
                'anonymous_responses' => false,
            ],
            'subject_user' => [
                'fullname' => $subject_instance->subject_user->fullname
            ],
            'participant_instances' => $expected_participant_instances
        ];
        $this->assertEquals($expected_subject, $subject['subject']);

        $expected_sections = [
            [
                'section' => [
                    'id' => $section3->id,
                    'display_title' => $section3->display_title,
                    'sort_order' => $section3->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section3_manager->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_not_started::get_name(),
                            'participant_id' => $manager_user->id,
                            'participant' => [
                                'fullname' => $manager_user->fullname,
                                'profileimageurlsmall' => $manager_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $manager_relationship->id,
                                'name' => $manager_relationship->get_name(),
                            ],
                            'is_for_current_user' => false,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => false,
            ],
            [
                'section' => [
                    'id' => $section1->id,
                    'display_title' => $section1->display_title,
                    'sort_order' => $section1->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section1_subject->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_in_progress::get_name(),
                            'participant_id' => $subject_user->id,
                            'participant' => [
                                'fullname' => $subject_user->fullname,
                                'profileimageurlsmall' => $subject_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $subject_relationship->id,
                                'name' => $subject_relationship->get_name(),
                            ],
                            'is_for_current_user' => true,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                    [
                        'id' => $participant_section1_manager->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_not_started::get_name(),
                            'participant_id' => $manager_user->id,
                            'participant' => [
                                'fullname' => $manager_user->fullname,
                                'profileimageurlsmall' => $manager_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $manager_relationship->id,
                                'name' => $manager_relationship->get_name(),
                            ],
                            'is_for_current_user' => false,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => true,
            ],
            [
                'section' => [
                    'id' => $section2->id,
                    'display_title' => $section2->display_title,
                    'sort_order' => $section2->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section2_subject->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_in_progress::get_name(),
                            'participant_id' => $subject_user->id,
                            'participant' => [
                                'fullname' => $subject_user->fullname,
                                'profileimageurlsmall' => $subject_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $subject_relationship->id,
                                'name' => $subject_relationship->get_name(),
                            ],
                            'is_for_current_user' => true,
                        ],
                        'progress_status' => section_in_progress::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => true,
            ],
        ];

        $this->assertCount(3, $subject['sections'], 'wrong sections count');
        $this->assertEquals($expected_sections, $subject['sections']);

        // Now run a query on others which should return empty result

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_OTHERS]
            ]
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $actual = $this->get_webapi_operation_data($result);
        $this->assertEmpty($actual, 'wrong subject count');

        // Now as the manager look at what I get back for others

        $this->setUser($user1);

        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_OTHERS]
            ]
        ];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $actual = $this->get_webapi_operation_data($result);
        $this->assertCount(1, $actual, 'wrong subject count');

        $subject = $actual[0];
        // The subject instance data should be the same as for the other user
        $this->assertEquals($expected_subject, $subject['subject']);

        $expected_sections = [
            [
                'section' => [
                    'id' => $section3->id,
                    'display_title' => $section3->display_title,
                    'sort_order' => $section3->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section3_manager->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_not_started::get_name(),
                            'participant_id' => $manager_user->id,
                            'participant' => [
                                'fullname' => $manager_user->fullname,
                                'profileimageurlsmall' => $manager_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $manager_relationship->id,
                                'name' => $manager_relationship->get_name(),
                            ],
                            'is_for_current_user' => true,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => true,
            ],
            [
                'section' => [
                    'id' => $section1->id,
                    'display_title' => $section1->display_title,
                    'sort_order' => $section1->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section1_subject->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_in_progress::get_name(),
                            'participant_id' => $subject_user->id,
                            'participant' => [
                                'fullname' => $subject_user->fullname,
                                'profileimageurlsmall' => $subject_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $subject_relationship->id,
                                'name' => $subject_relationship->get_name(),
                            ],
                            'is_for_current_user' => false,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                    [
                        'id' => $participant_section1_manager->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_not_started::get_name(),
                            'participant_id' => $manager_user->id,
                            'participant' => [
                                'fullname' => $manager_user->fullname,
                                'profileimageurlsmall' => $manager_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $manager_relationship->id,
                                'name' => $manager_relationship->get_name(),
                            ],
                            'is_for_current_user' => true,
                        ],
                        'progress_status' => section_not_started::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => true,
            ],
            [
                'section' => [
                    'id' => $section2->id,
                    'display_title' => $section2->display_title,
                    'sort_order' => $section2->sort_order,
                ],
                'participant_sections' => [
                    [
                        'id' => $participant_section2_subject->id,
                        'participant_instance' => [
                            'progress_status' => participant_instance_in_progress::get_name(),
                            'participant_id' => $subject_user->id,
                            'participant' => [
                                'fullname' => $subject_user->fullname,
                                'profileimageurlsmall' => $subject_user_profile_image_url
                            ],
                            'core_relationship' => [
                                'id' => $subject_relationship->id,
                                'name' => $subject_relationship->get_name(),
                            ],
                            'is_for_current_user' => false,
                        ],
                        'progress_status' => section_in_progress::get_name(),
                        'availability_status' => open::get_name(),
                        'is_overdue' => false,
                    ],
                ],
                'can_participate' => false,
            ],
        ];

        $this->assertCount(3, $subject['sections'], 'wrong sections count');
        $this->assertEquals($expected_sections, $subject['sections']);
    }

    public function test_query_invalid_filter(): void {
        $this->setAdminUser();

        $args = [
            'filters' => [
                'not_real_filter' => 1,
            ],
        ];

        $expected_error_message = 'Variable "$filters" got invalid value {"not_real_filter":1}; ';
        $expected_error_message .= 'Field value.about of required type [mod_perform_subject_instance_about_filter!]! was not provided.';
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, $expected_error_message);
    }

    public function test_failed_ajax_query(): void {
        $args = [
            'filters' => [
                'about' => [subject_instances_about::VALUE_ABOUT_OTHERS]
            ]
        ];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $this->setUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'not logged in');
    }

    private function get_core_relationship_id(string $relationship_resolver_class) {
        return relationship_resolver::repository()
            ->where('class_name', $relationship_resolver_class)
            ->order_by('id')
            ->first()
            ->relationship_id;
    }
}
