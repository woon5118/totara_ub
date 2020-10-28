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

use core\orm\query\builder;
use core\pagination\cursor;
use mod_perform\data_providers\activity\subject_instance_for_participant;
use mod_perform\entity\activity\filters\subject_instances_about;
use mod_perform\entity\activity\activity_type as activity_type_entity;
use mod_perform\entity\activity\participant_instance as participant_instance_entity;
use mod_perform\entity\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\participant_source;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\state\participant_instance\complete as participant_instance_complete;
use mod_perform\state\participant_instance\in_progress as participant_instance_in_progress;
use mod_perform\state\participant_instance\not_started as participant_instance_not_started;
use mod_perform\state\subject_instance\complete as subject_instance_complete;
use mod_perform\state\subject_instance\in_progress as subject_instance_in_progress;
use mod_perform\state\subject_instance\not_started as subject_instance_not_started;

require_once(__DIR__ . '/subject_instance_testcase.php');

/**
 * @group perform
 */
class mod_perform_data_provider_subject_instances_testcase extends mod_perform_subject_instance_testcase {

    /**
     * Even unfiltered must only return activities the user is participating in.
     */
    public function test_get_unfiltered(): void {
        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
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
     * Hidden activities should be filtered out
     */
    public function test_get_excludes_hidden_courses(): void {
        // Hide one of the activities
        builder::table('course')
            ->where('id', self::$about_user_and_participating->get_activity()->course)
            ->update([
                'visible' => 0,
                'visibleold' => 0
            ]);

        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->fetch()
            ->get();

        self::assertCount(1, $returned_subject_instances);

        self::assert_same_subject_instance(
            self::$about_someone_else_and_participating, $returned_subject_instances->first()
        ); // 538003
    }

    /**
     * @dataProvider subject_instance_provider
     * @param callable $get_query_activity
     * @param bool $expected_to_be_return
     */
    public function test_get_by_subject_instance_id(callable $get_query_activity, bool $expected_to_be_return): void {
        /** @var subject_instance_model $query_activity */
        $query_activity = $get_query_activity();

        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->add_filters(['subject_instance_id' => $query_activity->get_id()])
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
        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->add_filters(['about' => [subject_instances_about::VALUE_ABOUT_SELF]])
            ->fetch()
            ->get();

        self::assertCount(1, $returned_subject_instances);

        self::assert_same_subject_instance(self::$about_user_and_participating, $returned_subject_instances->first());
    }

    public function test_get_subject_instances_only_about_other_users(): void {
        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->add_filters(['about' => [subject_instances_about::VALUE_ABOUT_OTHERS]])
            ->fetch()
            ->get();

        self::assertCount(1, $returned_subject_instances);

        self::assert_same_subject_instance(self::$about_someone_else_and_participating, $returned_subject_instances->first());
    }

    public function test_get_user_about_self_and_others_via_all_filter_options(): void {
        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->add_filters(['about' =>
                [subject_instances_about::VALUE_ABOUT_SELF, subject_instances_about::VALUE_ABOUT_OTHERS]
            ])
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
     * Check that the result includes all participant instances not just the one for $user->id.
     */
    public function test_attaches_all_participant_instance(): void {
        $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
            ->add_filters(['about' => [subject_instances_about::VALUE_ABOUT_SELF]])
            ->fetch()
            ->get();

        $this->assertCount(1, $returned_subject_instances);

        /** @var subject_instance_model $returned_subject_instance */
        $returned_subject_instance = $returned_subject_instances->first();

        // Verify that there are two participant_instances for this subject_instance.
        $participant_instances = participant_instance_entity::repository()
            ->where('subject_instance_id', $returned_subject_instance->get_id())
            ->get();
        $this->assertCount(2, $participant_instances);

        // Verify that the participant_instance for the subject user is in the result.
        $subject_participant_instances = $participant_instances->filter('participant_id', self::$user->id);
        $returned_participant_instances = $returned_subject_instance->get_participant_instances();
        $this->assertCount(1, $subject_participant_instances);
        $this->assertCount(2, $returned_participant_instances);
        $this->assertContains($subject_participant_instances->first()->id, $returned_participant_instances->pluck('id'));
    }

    /**
     * @dataProvider cursor_size_provider
     * @param int $page_size
     * @param array $item_counts
     */
    public function test_with_pagination(int $page_size, array $item_counts): void {
        // Create activities
        $all_subject_instances = [self::$about_user_and_participating->id];

        // Remember we already have 1 - thus <
        for ($i = 1; $i < 4; $i++) {
            $si = self::perform_generator()->create_subject_instance([
                'activity_name' => "activity{$i}",
                'subject_user_id' => self::$user->id,
                'subject_is_participating' => true,
            ]);
            $all_subject_instances[] = $si->id;
        }

        // We order by created_at desc, id
        $expected_subject_instances = array_chunk(
            array_reverse($all_subject_instances),
            $page_size
        );
        // Just verifying test parameters here ...
        $this->assertSame(count($expected_subject_instances), count($item_counts));

        $cursor = cursor::create()->set_limit($page_size);

        for ($i = 0; $i < count($item_counts); $i++) {
            $paginator = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
                ->add_filters(['about' => [subject_instances_about::VALUE_ABOUT_SELF]])
                ->get_next($cursor);

            $items = $paginator->get_items();
            $this->assertCount($item_counts[$i], $items);
            $actual_ids = $items->pluck('id');
            // Order should be the same
            $this->assertSame($expected_subject_instances[$i], $actual_ids);

            $cursor = $paginator->get_next_cursor();
        }

        $this->assertNull($cursor);
    }

    /**
     * Data provider for cursor sizes
     */
    public function cursor_size_provider() {
        return [
            ['page_size' => 1, 'item_counts' => [1, 1, 1, 1]],
            ['page_size' => 2, 'item_counts' => [2, 2]],
            ['page_size' => 3, 'item_counts' => [3, 1]],
            ['page_size' => 4, 'item_counts' => [4]],
            ['page_size' => 5, 'item_counts' => [4]],
        ];
    }

    public function test_get_by_activity_type(): void {
        $activity_types = activity_type_entity::repository()
            ->order_by('name')
            ->get();
        $activity_types = array_combine($activity_types->pluck('name'), $activity_types->pluck('id'));

        // Create a set for each activity type
        $instances = self::create_activities_for_all_types($activity_types);

        // Now filter on each type
        foreach ($activity_types as $type => $id) {
            $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
                ->add_filters(['activity_type' => $id])
                ->fetch()
                ->get();

            self::assertCount(2, $returned_subject_instances);
            self::assert_same_subject_instance(
                $instances[$type]['about_someone_else_and_participating'], $returned_subject_instances->first()
            );
            self::assert_same_subject_instance(
                $instances[$type]['about_user_and_participating'], $returned_subject_instances->last()
            );
        }
    }

    public function test_get_by_own_progress(): void {
        // Create a set for each activity type
        $activity_types = activity_type_entity::repository()
            ->order_by('name')
            ->get();
        $activity_types = array_combine($activity_types->pluck('name'), $activity_types->pluck('id'));
        $instances = self::create_activities_for_all_types($activity_types);

        // Progress some instances
        self::set_participant_instance_progress($instances['appraisal']['about_user_and_participating'], participant_instance_not_started::get_code());
        self::set_participant_instance_progress($instances['appraisal']['about_someone_else_and_participating'], participant_instance_in_progress::get_code());
        self::set_participant_instance_progress($instances['check-in']['about_user_and_participating'], participant_instance_in_progress::get_code());
        self::set_participant_instance_progress($instances['check-in']['about_someone_else_and_participating'], participant_instance_complete::get_code());
        self::set_participant_instance_progress($instances['feedback']['about_user_and_participating'], participant_instance_complete::get_code());
        self::set_participant_instance_progress($instances['feedback']['about_someone_else_and_participating'], participant_instance_complete::get_code());

        // Now test filter
        // Ordered descending ...
        $to_test = [
            participant_instance_not_started::get_name() => [
                $instances['appraisal']['about_user_and_participating'],
            ],
            participant_instance_in_progress::get_name() => [
                $instances['check-in']['about_user_and_participating'],
                $instances['appraisal']['about_someone_else_and_participating'],
            ],
            participant_instance_complete::get_name() => [
                $instances['feedback']['about_someone_else_and_participating'],
                $instances['feedback']['about_user_and_participating'],
                $instances['check-in']['about_someone_else_and_participating'],
            ],
        ];

        foreach ($to_test as $progress_value => $expected_results) {
            $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
                ->add_filters(['participant_progress' => $progress_value])
                ->fetch()
                ->get();

            self::assertCount(count($expected_results), $returned_subject_instances);
            foreach ($expected_results as $expected_si) {
                self::assert_same_subject_instance($expected_si, $returned_subject_instances->shift());
            }
        }
    }

    public function test_get_by_overdue(): void {
        $activity_types = activity_type_entity::repository()
            ->order_by('name')
            ->get();
        $activity_types = array_combine($activity_types->pluck('name'), $activity_types->pluck('id'));

        // Create a set for each activity type
        $instances = self::create_activities_for_all_types($activity_types);

        // Set overdue and progress some instances
        self::set_subject_instance_progress($instances['appraisal']['about_user_and_participating'], subject_instance_not_started::get_code());
        self::set_participant_instance_progress($instances['appraisal']['about_user_and_participating'], participant_instance_not_started::get_code());

        self::set_subject_instance_progress($instances['appraisal']['about_someone_else_and_participating'], subject_instance_in_progress::get_code());
        self::set_participant_instance_progress($instances['appraisal']['about_someone_else_and_participating'], participant_instance_complete::get_code());

        self::set_subject_instance_progress($instances['check-in']['about_user_and_participating'], subject_instance_in_progress::get_code());
        self::set_subject_instance_due_date($instances['check-in']['about_user_and_participating'], strtotime("-1 day"));
        self::set_participant_instance_progress($instances['check-in']['about_user_and_participating'], participant_instance_in_progress::get_code());

        self::set_subject_instance_progress($instances['check-in']['about_someone_else_and_participating'], subject_instance_complete::get_code());
        self::set_subject_instance_due_date($instances['check-in']['about_someone_else_and_participating'], strtotime("-1 day"));
        self::set_participant_instance_progress($instances['check-in']['about_someone_else_and_participating'], participant_instance_complete::get_code());

        self::set_subject_instance_progress($instances['feedback']['about_user_and_participating'], subject_instance_not_started::get_code());
        self::set_subject_instance_due_date($instances['feedback']['about_user_and_participating'], strtotime("+1 day"));
        self::set_participant_instance_progress($instances['feedback']['about_user_and_participating'], participant_instance_not_started::get_code());

        self::set_subject_instance_progress($instances['feedback']['about_someone_else_and_participating'], subject_instance_in_progress::get_code());
        self::set_subject_instance_due_date($instances['feedback']['about_someone_else_and_participating'], strtotime("+1 day"));
        self::set_participant_instance_progress($instances['feedback']['about_someone_else_and_participating'], participant_instance_not_started::get_code());

        // Now test filters
        // Ordered descending ...
        $to_test = [
            [
                'filters' => [
                    'activity_type' => $activity_types['check-in'],
                    'participant_progress' => participant_instance_complete::get_name(),
                ],
                'expected' => [
                    $instances['check-in']['about_someone_else_and_participating'],
                ],
            ],
            [
                'filters' => [
                    'activity_type' => $activity_types['check-in'],
                    'participant_progress' => participant_instance_complete::get_name(),
                    'overdue' => 1,
                ],
                'expected' => [],
            ],
            [
                'filters' => [
                    'activity_type' => $activity_types['check-in'],
                    'participant_progress' => participant_instance_complete::get_name(),
                    'overdue' => 0,
                ],
                'expected' => [
                    $instances['check-in']['about_someone_else_and_participating'],
                ],
            ],
            [
                'filters' => [
                    'participant_progress' => participant_instance_not_started::get_name(),
                    'overdue' => 0,
                ],
                'expected' => [
                    $instances['feedback']['about_someone_else_and_participating'],
                    $instances['feedback']['about_user_and_participating'],
                    $instances['appraisal']['about_user_and_participating'],
                ],
            ],
        ];

        foreach ($to_test as $data) {
            $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
                ->add_filters($data['filters'])
                ->fetch()
                ->get();
            self::assertCount(count($data['expected']), $returned_subject_instances);
            foreach ($data['expected'] as $expected_si) {
                self::assert_same_subject_instance($expected_si, $returned_subject_instances->shift());
            }
        }
    }

    public function test_combined_filters(): void {
        $activity_types = activity_type_entity::repository()
            ->order_by('name')
            ->get();
        $activity_types = array_combine($activity_types->pluck('name'), $activity_types->pluck('id'));

        // Create a set for each activity type
        $instances = self::create_activities_for_all_types($activity_types);

        // Set overdue and progress some instances
        self::set_subject_instance_progress($instances['appraisal']['about_user_and_participating'], subject_instance_not_started::get_code());
        self::set_subject_instance_progress($instances['appraisal']['about_someone_else_and_participating'], subject_instance_in_progress::get_code());

        self::set_subject_instance_progress($instances['check-in']['about_user_and_participating'], subject_instance_in_progress::get_code());
        self::set_subject_instance_due_date($instances['check-in']['about_user_and_participating'], strtotime("-1 day"));
        self::set_subject_instance_progress($instances['check-in']['about_someone_else_and_participating'], subject_instance_complete::get_code());
        self::set_subject_instance_due_date($instances['check-in']['about_someone_else_and_participating'], strtotime("-1 day"));

        self::set_subject_instance_progress($instances['feedback']['about_user_and_participating'], subject_instance_not_started::get_code());
        self::set_subject_instance_due_date($instances['feedback']['about_user_and_participating'], strtotime("+1 day"));
        self::set_subject_instance_progress($instances['feedback']['about_someone_else_and_participating'], subject_instance_in_progress::get_code());
        self::set_subject_instance_due_date($instances['feedback']['about_someone_else_and_participating'], strtotime("+1 day"));

        // Now test filter
        // Ordered descending ...
        $to_test = [
            1 => [
                $instances['check-in']['about_user_and_participating'],
            ],
            0 => [
                $instances['feedback']['about_someone_else_and_participating'],
                $instances['feedback']['about_user_and_participating'],
                $instances['check-in']['about_someone_else_and_participating'],
                $instances['appraisal']['about_someone_else_and_participating'],
                $instances['appraisal']['about_user_and_participating'],
            ],
        ];

        foreach ($to_test as $is_overdue => $expected_results) {
            $returned_subject_instances = (new subject_instance_for_participant(self::$user->id, participant_source::INTERNAL))
                ->add_filters(['overdue' => $is_overdue])
                ->fetch()
                ->get();
            self::assertCount(count($expected_results), $returned_subject_instances);
            foreach ($expected_results as $expected_si) {
                self::assert_same_subject_instance($expected_si, $returned_subject_instances->shift());
            }
        }
    }

    /**
     * @param array $activity_types
     * @return array
     * @throws coding_exception
     */
    protected static function create_activities_for_all_types(array $activity_types): array {
        // Create a set for each activity type
        // Initial instances are all 'appraisals'
        $instances = ['appraisal' =>
            [
                'about_user_and_participating' => self::$about_user_and_participating,
                'about_someone_else_and_participating' => self::$about_someone_else_and_participating,
                'about_user_but_not_participating' => self::$about_user_but_not_participating,
                'non_existing' => self::$non_existing,
            ]
        ];

        foreach ($activity_types as $type => $id) {
            if ($type === 'appraisal') {
                continue;
            }

            self::create_user_activities(self::$user, $type);
            $instances[$type] = [
                'about_user_and_participating' => self::$about_user_and_participating,
                'about_someone_else_and_participating' => self::$about_someone_else_and_participating,
                'about_user_but_not_participating' => self::$about_user_but_not_participating,
                'non_existing' => self::$non_existing,
            ];
        }
        return $instances;
    }

    /**
     * @param subject_instance_model $si
     * @param int $progress
     */
    protected static function set_participant_instance_progress(subject_instance_model $si, int $progress): void {
        $pi = participant_instance_entity::repository()
            ->where('subject_instance_id', $si->get_id())
            ->where('participant_id', self::$user->id)
            ->order_by('id')
            ->first();
        $pi->progress = $progress;
        $pi->save();
    }

    /**
     * @param subject_instance_model $si
     * @param int $progress
     */
    protected static function set_subject_instance_progress(subject_instance_model $si, int $progress): void {
        $si = new subject_instance_entity($si->get_id());
        $si->progress = $progress;
        $si->save();
    }

    /**
     * @param subject_instance_model $si
     * @param int $due_date
     */
    protected static function set_subject_instance_due_date(subject_instance_model $si, int $due_date): void {
        $si = new subject_instance_entity($si->get_id());
        $si->due_date = $due_date;
        $si->save();
    }
}