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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\controllers\activity\user_activities_select_participants;
use mod_perform\controllers\activity\view_user_activity;
use mod_perform\models\activity\participant_instance;
use mod_perform\expand_task;
use totara_job\job_assignment;
use mod_perform\notification\placeholder;

require_once(__DIR__ . '/notification_testcase.php');

/**
 * Class participant_section_creation_service_test
 *
 * @group perform
 */
class mod_perform_notification_placeholder_testcase extends mod_perform_notification_testcase {

    /**
     * Create an activity and some subject and participant instances for testing.
     *
     * @param bool $use_duedate
     * @return array
     */
    private function create_instances(bool $use_duedate = false): array {
        $this->setAdminUser();
        $users['user1'] = $this->getDataGenerator()->create_user(['username' => 'user1', 'firstname'=>'User', 'lastname' => 'One']);
        $users['user2'] = $this->getDataGenerator()->create_user(['username' => 'user2', 'firstname'=>'User', 'lastname' => 'Two']);
        $users['manager'] = $this->getDataGenerator()->create_user(['username' => 'manager', 'firstname'=>'Your', 'lastname' => 'Manager']);
        $users['appraiser'] = $this->getDataGenerator()->create_user(['username' => 'appraiser', 'firstname'=>'Our', 'lastname' => 'Appraiser']);

        $managerja = job_assignment::create_default($users['manager']->id);
        job_assignment::create_default($users['user1']->id, ['appraiserid' => $users['appraiser']->id, 'managerjaid' => $managerja->id]);
        job_assignment::create_default($users['user2']->id, ['managerjaid' => $managerja->id]);

        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $this->create_section_relationships($section);
        $track = $this->create_single_activity_track_and_assignment($activity, [$users['user1']->id, $users['user2']->id], $use_duedate);
        $element = $this->perfgen->create_element(['title' => 'Question one', 'plugin_name' => 'short_text']);
        $this->perfgen->create_section_element($section, $element);

        (new expand_task())->expand_multiple($track->assignments->map(function ($ass) {
            return $ass->id;
        })->all());

        $activity->activate();
        $this->assertTrue($activity->is_active());

        // Use real task to create subject instances.
        $sic = new \mod_perform\task\service\subject_instance_creation();
        $sic->generate_instances();

        $entities = \mod_perform\entities\activity\participant_instance::repository()->get()->map_to(participant_instance::class);
        return [$activity, $users, $entities];
    }

    /**
     * Tests that placeholders are correct for participant instances created without due date.
     *
     * @return void
     */
    public function test_correct_placeholder_values_from_open_participant_instance(): void {
        list($activity, $users, $participant_instances) = $this->create_instances();
        $this->assertCount(5, $participant_instances);
        foreach ($participant_instances as $px => $participant_instance) {
            $placeholders = placeholder::from_participant_instance($participant_instance);
            $participant = $users[$participant_instance->get_participant()->username];
            $subject_instance = $participant_instance->get_subject_instance();

            $pi_url = $participant_instance->get_participation_url();
            $pi_link = html_writer::link($pi_url, $activity->name);
            $ps_url = user_activities_select_participants::get_url();
            $ps_link = html_writer::link($ps_url, get_string('user_activities_select_participants_page_title', 'mod_perform'));

            $this->assertEquals(fullname($participant), $placeholders->recipient_fullname);
            $this->assertEquals($activity->name, $placeholders->activity_name);
            $this->assertEquals($activity->get_type()->get_display_name(), $placeholders->activity_type);
            $this->assertEquals($subject_instance->subject_user->fullname, $placeholders->subject_fullname);
            $this->assertEquals(fullname($participant), $placeholders->participant_fullname);
            $this->assertEquals($participant_instance->get_core_relationship()->get_name(), $placeholders->participant_relationship);
            $this->assertEquals(0, $placeholders->instance_duedate);
            $this->assertEquals('', $placeholders->conditional_duedate);
            $this->assertEquals($subject_instance->created_at, $placeholders->instance_created_at);
            $this->assertEquals(0, $placeholders->instance_days_active);
            $this->assertEquals(0, $placeholders->instance_days_remaining);
            $this->assertEquals(0, $placeholders->instance_days_overdue);
            $this->assertEquals($pi_url, $placeholders->activity_url);
            $this->assertEquals($pi_link, $placeholders->activity_link);
            $this->assertEquals($ps_url, $placeholders->participant_selection_url);
            $this->assertEquals($ps_link, $placeholders->participant_selection_link);
        }
    }

    /**
     * Tests that placeholders are correct for participant instances created with due date set.
     *
     * @return void
     */
    public function test_correct_placeholder_values_from_participant_instance_with_duedate(): void {
        // Expected to be due in two weeks.
        $duedate = time() + (2 * WEEKSECS);
        $strftimedate = get_string('strftimedate');
        $formatted_duedate = userdate($duedate, $strftimedate);
        $a = new stdClass();
        $a->duedate = $formatted_duedate;
        $conditional_duedate = get_string('conditional_duedate_participant_placeholder', 'mod_perform', $a);
        list($activity, $users, $participant_instances) = $this->create_instances(true);
        $this->assertCount(5, $participant_instances);
        foreach ($participant_instances as $px => $participant_instance) {
            $placeholders = placeholder::from_participant_instance($participant_instance);
            $this->assertEquals($formatted_duedate, $placeholders->instance_duedate);
            $this->assertEquals($conditional_duedate, $placeholders->conditional_duedate);
            $this->assertEquals(14, $placeholders->instance_days_remaining);
            $this->assertEquals(0, $placeholders->instance_days_overdue);
        }
    }

    /**
     * Tests that placehoders are correct when created from subject instance without a due date.
     */
    public function test_correct_placeholder_values_from_subject_instance(): void {
        list($activity, $users, $participant_instances) = $this->create_instances();
        $this->assertCount(5, $participant_instances);
        $subject_instance = $participant_instances->first()->get_subject_instance();
        $placeholders = placeholder::from_subject_instance($subject_instance);
        $subject = $users[$subject_instance->get_subject_user()->username];

        $si_url = view_user_activity::get_url();
        $si_link = html_writer::link($si_url, $activity->name);
        $ps_url = user_activities_select_participants::get_url();
        $ps_link = html_writer::link($ps_url, get_string('user_activities_select_participants_page_title', 'mod_perform'));

        $this->assertEquals(fullname($subject), $placeholders->recipient_fullname);
        $this->assertEquals($activity->name, $placeholders->activity_name);
        $this->assertEquals($activity->get_type()->get_display_name(), $placeholders->activity_type);
        $this->assertEquals($subject_instance->subject_user->fullname, $placeholders->subject_fullname);
        $this->assertEquals(fullname($subject), $placeholders->participant_fullname);
        $this->assertEquals('subject', $placeholders->participant_relationship);
        $this->assertEquals(0, $placeholders->instance_duedate);
        $this->assertEquals('', $placeholders->conditional_duedate);
        $this->assertEquals($subject_instance->created_at, $placeholders->instance_created_at);
        $this->assertEquals(0, $placeholders->instance_days_active);
        $this->assertEquals(0, $placeholders->instance_days_remaining);
        $this->assertEquals(0, $placeholders->instance_days_overdue);
        $this->assertEquals($si_url, $placeholders->activity_url);
        $this->assertEquals($si_link, $placeholders->activity_link);
        $this->assertEquals($ps_url, $placeholders->participant_selection_url);
        $this->assertEquals($ps_link, $placeholders->participant_selection_link);

        // Also test updating the placeholders with a participant and relationship.
        foreach ($participant_instances as $px => $participant_instance) {
            $participant = $users[$participant_instance->get_participant()->username];
            $placeholders->set_participant($participant_instance->get_participant()->get_user(), $participant_instance->get_core_relationship());
            $this->assertEquals(fullname($participant), $placeholders->recipient_fullname);
            $this->assertEquals($activity->name, $placeholders->activity_name);
            $this->assertEquals($activity->get_type()->get_display_name(), $placeholders->activity_type);
            $this->assertEquals($subject_instance->subject_user->fullname, $placeholders->subject_fullname);
            $this->assertEquals(fullname($participant), $placeholders->participant_fullname);
            $this->assertEquals($participant_instance->get_core_relationship()->get_name(), $placeholders->participant_relationship);
            $this->assertEquals(0, $placeholders->instance_duedate);
            $this->assertEquals('', $placeholders->conditional_duedate);
            $this->assertEquals($subject_instance->created_at, $placeholders->instance_created_at);
            $this->assertEquals(0, $placeholders->instance_days_active);
            $this->assertEquals(0, $placeholders->instance_days_remaining);
            $this->assertEquals(0, $placeholders->instance_days_overdue);
            // Note that placeholders created from a subject instance do not have link to a specific participant instance.
            $this->assertEquals($si_url, $placeholders->activity_url);
            $this->assertEquals($si_link, $placeholders->activity_link);
            $this->assertEquals($ps_url, $placeholders->participant_selection_url);
            $this->assertEquals($ps_link, $placeholders->participant_selection_link);
        }
    }

    /**
     * Tests that placeholders are correct when created from a subject instance with a due date set.
     *
     * @return void
     */
    public function test_correct_placeholder_values_from_subject_instance_with_duedate(): void {
        // Expected to be due in two weeks.
        $duedate = time() + (2 * WEEKSECS);
        $strftimedate = get_string('strftimedate');
        $formatted_duedate = userdate($duedate, $strftimedate);
        $a = new stdClass();
        $a->duedate = $formatted_duedate;
        $conditional_duedate = get_string('conditional_duedate_subject_placeholder', 'mod_perform', $a);
        list($activity, $users, $participant_instances) = $this->create_instances(true);
        $subject_instance = $participant_instances->first()->get_subject_instance();
        $placeholders = placeholder::from_subject_instance($subject_instance);
        $this->assertEquals($formatted_duedate, $placeholders->instance_duedate);
        $this->assertEquals($conditional_duedate, $placeholders->conditional_duedate);
        $this->assertEquals(14, $placeholders->instance_days_remaining);
        $this->assertEquals(0, $placeholders->instance_days_overdue);
    }

    /**
     * Tests the placeholder::format_duration method.
     *
     * @return void
     */
    public function test_placeholder_duration_in_days_formatter(): void {
        $now = time();
        $tests = [
            0 => 0,
            HOURSECS => 0,
            ((8 * HOURSECS) - 10) => 0,
            (8 * HOURSECS) => 1,
            DAYSECS => 1,
            (DAYSECS + HOURSECS) => 1,
            (DAYSECS + (8 * HOURSECS)) => 2,
            (DAYSECS * 3) => 3,
            WEEKSECS => 7
        ];
        foreach ($tests as $delta => $days) {
            $this->assertEquals($days, placeholder::format_duration($now, $now + $delta));
        }
    }
}