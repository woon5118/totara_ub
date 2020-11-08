<?php
/*
* This file is part of Totara Perform
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
* @author Simon Coggins <simon.coggins@totaralearning.com>
* @package mod_perform
*/

use container_perform\perform;
use mod_perform\entity\activity\activity;
use mod_perform\entity\activity\element;
use mod_perform\entity\activity\element_response;
use mod_perform\entity\activity\participant_instance;
use mod_perform\entity\activity\participant_section;
use mod_perform\entity\activity\section;
use mod_perform\entity\activity\section_element;
use mod_perform\entity\activity\subject_instance;
use mod_perform\entity\activity\track;
use mod_perform\entity\activity\track_user_assignment;
use mod_perform\state\subject_instance\complete;
use mod_perform\state\subject_instance\in_progress;
use mod_perform\userdata\purge_other_responses;
use mod_perform\userdata\purge_user_responses;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

/**
 * @group perform
 */
class mod_perform_userdata_purge_responses_testcase  extends advanced_testcase {

    public function test_purge_other_responses_removes_correct_records(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $target_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);
        $target_user_assignment_id = $target_subject_instance->track_user_assignment_id;

        // Swap roles so participant is now subject.
        $untouched_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $participant->id,
            'other_participant_id' => $subject->id,
            'include_questions' => true,
        ]);
        $untouched_user_assignment_id = $untouched_subject_instance->track_user_assignment_id;

        $generator->create_responses($target_subject_instance);
        $generator->create_responses($untouched_subject_instance);

        $this->assertEquals(2, (activity::repository())->count());
        $this->assertEquals(2, (track::repository())->count());
        $this->assertEquals(2, (section::repository())->count());
        $this->assertEquals(4, (section_element::repository())->count());
        $this->assertEquals(4, (element::repository())->count());
        $this->assertEquals(2, (subject_instance::repository())->count());
        $this->assertEquals(4, (participant_instance::repository())->count());
        $this->assertEquals(4, (participant_section::repository())->count());
        $this->assertEquals(8, (element_response::repository())->count());

        $targetuser1 = new target_user($subject);
        $status = purge_other_responses::execute_purge($targetuser1, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);

        // Activity, track, section, section_elements and elements left alone
        $this->assertEquals(2, (activity::repository())->count());
        $this->assertEquals(2, (track::repository())->count());
        $this->assertEquals(2, (section::repository())->count());
        $this->assertEquals(4, (section_element::repository())->count());
        $this->assertEquals(4, (element::repository())->count());

        // Purged user's subject instances removed but not other one.
        $subject_instances = (subject_instance::repository())->get();
        $this->assertEquals(1, $subject_instances->count());
        $subject_instance = $subject_instances->first();
        $this->assertEquals($participant->id, $subject_instance->subject_user_id);
        // Purged user's subject instances have participant instances removed too.
        $participant_instances = (participant_instance::repository())->get();
        $this->assertEquals(2, $participant_instances->count());
        // But subject instance still has participant instances in other user's subject instances.
        $participant_ids = $participant_instances->pluck('participant_id');
        sort($participant_ids);
        $this->assertEquals([$subject->id, $participant->id], $participant_ids);
        $subject_instance_ids = $participant_instances->pluck('subject_instance_id');
        $this->assertEquals([$subject_instance->id, $subject_instance->id], $subject_instance_ids);
        // Sections exist for the remaining participant instances only.
        $participant_sections = (participant_section::repository())->get();
        $this->assertEquals(2, $participant_sections->count());
        $section_pi_ids = $participant_sections->pluck('participant_instance_id');
        sort($section_pi_ids);
        $participant_instance_ids = $participant_instances->pluck('id');
        sort($participant_instance_ids);
        $this->assertEquals($participant_instance_ids, $section_pi_ids);
        // Responses belong to remaining participant sections.
        $responses = (element_response::repository())->get();
        $this->assertEquals(4, $responses->count());
        $response_ps_ids = $responses->pluck('participant_instance_id');
        sort($response_ps_ids);
        $this->assertEquals(
            [$participant_instance_ids[0], $participant_instance_ids[0], $participant_instance_ids[1], $participant_instance_ids[1]],
            $response_ps_ids
        );

        // Deleted user's user_assignment is now deleted
        $target_subject_instance_user_assignment = track_user_assignment::repository()->find($target_user_assignment_id);
        $this->assertEquals(1, $target_subject_instance_user_assignment->deleted);

        // Other subject instance user assignment is not deleted.
        $untouched_subject_instance_user_assignment = track_user_assignment::repository()->find($untouched_user_assignment_id);
        $this->assertEquals(0, $untouched_subject_instance_user_assignment->deleted);
    }

    public function test_purge_user_responses_removes_correct_records(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        $subject_instance2 = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $participant->id,
            'other_participant_id' => $subject->id,
            'include_questions' => true,
        ]);

        $generator->create_responses($subject_instance);
        $generator->create_responses($subject_instance2);

        $this->assertEquals(2, (activity::repository())->count());
        $this->assertEquals(2, (track::repository())->count());
        $this->assertEquals(2, (section::repository())->count());
        $this->assertEquals(4, (section_element::repository())->count());
        $this->assertEquals(4, (element::repository())->count());
        $this->assertEquals(2, (subject_instance::repository())->count());
        $this->assertEquals(4, (participant_instance::repository())->count());
        $this->assertEquals(4, (participant_section::repository())->count());
        $this->assertEquals(8, (element_response::repository())->count());

        $targetuser1 = new target_user($subject);
        $status = purge_user_responses::execute_purge($targetuser1, context_system::instance());

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);

        // Activity, track, section, section_elements and elements left alone
        $this->assertEquals(2, (activity::repository())->count());
        $this->assertEquals(2, (track::repository())->count());
        $this->assertEquals(2, (section::repository())->count());
        $this->assertEquals(4, (section_element::repository())->count());
        $this->assertEquals(4, (element::repository())->count());

        // Purged user's subject instance is not removed.
        $subject_instances = (subject_instance::repository())->get();
        $this->assertEquals(2, $subject_instances->count());

        $target_subject_instance_count = $subject_instances->filter(
            function (subject_instance $subject_instance) use ($subject): bool {
                return (int)$subject_instance->subject_user_id === (int)$subject->id;
            }
        )->count();
        $this->assertEquals(1, $target_subject_instance_count);

        // Purged user's participant instances removed from both subject instances, but not other participant's.
        $participant_instances = (participant_instance::repository())->order_by('id')->get();
        $participant_ids = $participant_instances->pluck('participant_id');
        $this->assertEquals([$participant->id, $participant->id], $participant_ids);

        // Sections exist for the remaining participant instances only.
        $participant_sections = (participant_section::repository())->get();
        $this->assertEquals(2, $participant_sections->count());
        $section_pi_ids = $participant_sections->pluck('participant_instance_id');
        sort($section_pi_ids);
        $this->assertEquals($participant_instances->pluck('id'), $section_pi_ids);

        // Responses belong to remaining participant sections.
        $responses = (element_response::repository())->get();
        $this->assertEquals(4, $responses->count());
        $response_ps_ids = $responses->pluck('participant_instance_id');
        sort($response_ps_ids);
        $participant_instance_ids = $participant_instances->pluck('id');
        $this->assertEquals(
            [$participant_instance_ids[0], $participant_instance_ids[0], $participant_instance_ids[1], $participant_instance_ids[1]],
            $response_ps_ids
        );
    }

    /**
     * Strategy here is create three subject instances on activities in different contexts:
     * System context -> Default performance category -> Activity 1
     * System context -> Default performance category -> Activity 2
     * Tenant context -> Default tenant performance category -> Activity 3
     *
     * First purge is in course context of activity 1 so only deletes activity 1.
     * Second purge is in default (system) performance category, so deletes 2 but not 3
     * Third purge is system context so should delete 3
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_purge_other_responses_with_context_restriction() {
        global $DB;
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $course_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'activity_name' => 'One',
        ]);
        $course_instance_course_id = $course_subject_instance
            ->track
            ->activity
            ->course;
        $course_instance_course_context = context_course::instance($course_instance_course_id);

        $category_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'activity_name' => 'Two',
        ]);
        $default_category_context = context_coursecat::instance(perform::get_default_category_id());

        // Enable multi-tenancy so we can create an activity in another category context.
        /** @var totara_tenant_generator $tenantgenerator */
        $tenantgenerator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $tenantgenerator->enable_tenants();

        $tenant1 = $tenantgenerator->create_tenant();
        $category1 = $tenant1->categoryid;
        $user1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $manager_role_id = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $this->getDataGenerator()->role_assign($manager_role_id, $user1->id, (\context_coursecat::instance($category1))->id);
        self::setUser($user1);
        $other_category_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'activity_name' => 'Three',
        ]);
        self::setAdminUser();

        $system_context = context_system::instance();

        $this->assertEquals(3, (subject_instance::repository())->get()->count());

        $targetuser1 = new target_user($subject);
        $status = purge_other_responses::execute_purge($targetuser1, $course_instance_course_context);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertEquals(2, (subject_instance::repository())->get()->count());

        $targetuser1 = new target_user($subject);
        $status = purge_other_responses::execute_purge($targetuser1, $default_category_context);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertEquals(1, (subject_instance::repository())->get()->count());

        $targetuser1 = new target_user($subject);
        $status = purge_other_responses::execute_purge($targetuser1, $system_context);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertEquals(0, (subject_instance::repository())->get()->count());
    }

    public function test_purge_updates_activity_progress() {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);
        $subject_instance_model = \mod_perform\models\activity\subject_instance::load_by_entity($subject_instance);

        $generator->create_responses($subject_instance);

        $subject_participant_instance_entity = participant_instance::repository()
            ->where('participant_id', $subject->id)
            ->where('subject_instance_id', $subject_instance->id)
            ->one();

        // Force the subject's instance to complete.
        $subject_participant_instance_entity->progress = \mod_perform\state\participant_instance\complete::get_code();
        $subject_participant_instance_entity->update();

        $participant_sections = participant_section::repository()
            ->where('participant_instance_id', $subject_participant_instance_entity->id);
        foreach ($participant_sections as $participant_section) {
            $participant_section->progress = \mod_perform\state\participant_section\complete::get_code();
            $participant_section->update();
        }

        $subject_instance->progress = in_progress::get_code();
        $subject_instance->update();

        // Progress on subject instance should be not started (as the above was a db update).
        $this->assertEquals(in_progress::get_name(), $subject_instance_model->get_progress_status());

        // Purge the only other participant in this subject instance.
        $targetuser = new target_user($participant);
        $status = purge_user_responses::execute_purge($targetuser, context_system::instance());

        // Reload entity and model to ensure up to date info.
        $new_subject_instance = subject_instance::repository()->find($subject_instance->id);
        $new_subject_instance_model = \mod_perform\models\activity\subject_instance::load_by_entity($new_subject_instance);

        // Progress in the subject instance should now be complete (because only incomplete participant instance was purged).
        $this->assertEquals(complete::get_name(), $new_subject_instance_model->get_progress_status());
    }
}