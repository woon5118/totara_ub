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
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\entities\activity\participant_instance;
use mod_perform\models\activity\participant_source;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\models\activity\subject_instance;


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
        $participant_instances = participant_instance::repository()
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
    
}