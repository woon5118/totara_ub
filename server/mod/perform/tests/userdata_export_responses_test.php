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
use mod_perform\entities\activity\activity;
use mod_perform\entities\activity\element;
use mod_perform\entities\activity\element_response;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\participant_section;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_element;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track;
use mod_perform\userdata\export_user_responses;
use mod_perform\userdata\export_other_visible_responses;
use mod_perform\userdata\export_other_hidden_responses;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

/**
 * @group perform
 */
class mod_perform_userdata_export_responses_testcase  extends advanced_testcase {

    public function test_export_user_responses_exports_correct_records(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        $participant_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $participant->id,
            'other_participant_id' => $subject->id,
            'include_questions' => true,
        ]);

        $generator->create_responses($subject_subject_instance);
        $generator->create_responses($participant_subject_instance);

        $targetuser1 = new target_user($subject);
        $export = export_user_responses::execute_export($targetuser1, context_system::instance());

        $this->assertCount(4, $export->data);

        foreach ($export->data as $record) {
            // All records must have subject as the participant.
            $this->assertEquals($subject->id, $record['participant_id']);
        }
    }

    public function test_export_other_responses_exports_correct_records(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        // Swap roles so participant is now subject.
        $participant_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $participant->id,
            'other_participant_id' => $subject->id,
            'include_questions' => true,
        ]);

        $generator->create_responses($subject_subject_instance);
        $generator->create_responses($participant_subject_instance);

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, context_system::instance());

        $this->assertCount(2, $export->data);

        $targetuser1 = new target_user($subject);
        $export = export_other_hidden_responses::execute_export($targetuser1, context_system::instance());

        $this->assertCount(0, $export->data);
    }

    public function test_export_user_responses_with_context_restriction() {
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
        $generator->create_responses($course_subject_instance);
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
        $generator->create_responses($category_subject_instance);
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
        $generator->create_responses($other_category_subject_instance);
        self::setAdminUser();

        $system_context = context_system::instance();

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, $course_instance_course_context);

        // Subjects responses to Q1 and Q2 in 1 activity only (course context)
        $this->assertCount(2, $export->data);

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, $default_category_context);

        // Subjects responses to Q1 and Q2 in 2 activities only (category context)
        $this->assertCount(4, $export->data);

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, $system_context);

        // Subjects responses to Q1 and Q2 in all 3 activities (system context)
        $this->assertCount(6, $export->data);
    }

    public function test_ensure_anonymous_responses_are_exported_correctly() {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);
        // Make the activity anonymous.
        $activity = $subject_subject_instance->track->activity;
        $activity->anonymous_responses = 1;
        $activity->save();

        // Mark all sections as complete.
        $participant_instances = $subject_subject_instance->participant_instances;
        /** @var participant_instance $participant_instance */
        foreach ($participant_instances as $participant_instance) {
            $participant_sections = $participant_instance->participant_sections;
            /** @var participant_section $participant_section */
            foreach ($participant_sections as $participant_section) {
                $participant_section->progress = mod_perform\state\participant_section\complete::get_code();
                $participant_section->save();
            }
        }

        // Swap roles so participant is now subject.
        $participant_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $participant->id,
            'other_participant_id' => $subject->id,
            'include_questions' => true,
        ]);
        // Make the activity anonymous.
        $activity = $participant_subject_instance->track->activity;
        $activity->anonymous_responses = 1;
        $activity->save();

        // Mark all sections as complete.
        $participant_instances = $participant_subject_instance->participant_instances;
        /** @var participant_instance $participant_instance */
        foreach ($participant_instances as $participant_instance) {
            $participant_sections = $participant_instance->participant_sections;
            /** @var participant_section $participant_section */
            foreach ($participant_sections as $participant_section) {
                $participant_section->progress = mod_perform\state\participant_section\complete::get_code();
                $participant_section->save();
            }
        }

        $generator->create_responses($subject_subject_instance);
        $generator->create_responses($participant_subject_instance);

        $targetuser1 = new target_user($subject);
        $export = export_user_responses::execute_export($targetuser1, context_system::instance());

        // Subject's own responses in their as well as participant's subject instances.
        $this->assertCount(4, $export->data);

        foreach ($export->data as $response) {
            // My own participant id is not anonymised
            $this->assertEquals($subject->id, $response['participant_id']);
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, context_system::instance());

        // Responses in the subject's own subject instance (from them and others).
        $this->assertCount(4, $export->data);
        foreach ($export->data as $response) {
            if (isset($response['participant_id'])) {
                // My own participant id is not anonymised
                $this->assertEquals($subject->id, $response['participant_id']);
            }
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_hidden_responses::execute_export($targetuser1, context_system::instance());

        // There are no records the subject can't see that belong to them in this setup.
        $this->assertCount(0, $export->data);
    }

    public function test_ensure_drafts_are_excluded_from_export_correctly() {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
            'update_participant_sections_status' => 'draft',
        ]);
        $generator->create_responses($subject_subject_instance);

        // By default all progress is 'Not started' so responses are all in draft.
        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, context_system::instance());

        // The subject should still be able to see their own responses, but not the other participants.
        $this->assertCount(2, $export->data);
        foreach ($export->data as $response) {
            // All visible responses should be subject's.
            $this->assertEquals($subject->id, $response['participant_id']);
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_hidden_responses::execute_export($targetuser1, context_system::instance());

        // Even though there are participant responses, they cannots see them because they are drafts.
        $this->assertCount(0, $export->data);

        // Now mark all sections as complete.
        $participant_instances = $subject_subject_instance->participant_instances;
        /** @var participant_instance $participant_instance */
        foreach ($participant_instances as $participant_instance) {
            $participant_sections = $participant_instance->participant_sections;
            /** @var participant_section $participant_section */
            foreach ($participant_sections as $participant_section) {
                $participant_section->progress = mod_perform\state\participant_section\complete::get_code();
                $participant_section->save();
            }
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, context_system::instance());

        // The subject should now be able to see their own responses as well as the other participants.
        $this->assertCount(4, $export->data);
        $own = 0;
        $others = 0;
        foreach ($export->data as $response) {
            if ($subject->id == $response['participant_id']) {
                $own++;
            } else {
                $others++;
            }
        }
        $this->assertEquals(2, $own);
        $this->assertEquals(2, $others);

        $targetuser1 = new target_user($subject);
        $export = export_other_hidden_responses::execute_export($targetuser1, context_system::instance());

        // Still none, subject can see all responses.
        $this->assertCount(0, $export->data);

        // Update all section relationships so no-one can view other's answers.
        $activity_sections = $subject_subject_instance->track->activity->sections;
        /** @var section $activity_section */
        foreach ($activity_sections as $activity_section) {
            $section_relationships = $activity_section->section_relationships;
            /** @var \mod_perform\entities\activity\section_relationship $section_relationship */
            foreach ($section_relationships as $section_relationship) {
                $section_relationship->can_view = 0;
                $section_relationship->save();
            }
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_visible_responses::execute_export($targetuser1, context_system::instance());
        // Subject can still see their own answers
        $this->assertCount(2, $export->data);
        foreach ($export->data as $response) {
            $this->assertEquals($subject->id, $response['participant_id']);
        }

        $targetuser1 = new target_user($subject);
        $export = export_other_hidden_responses::execute_export($targetuser1, context_system::instance());
        // Subject can't normally see the other participant's answers, but are available in hidden responses.
        $this->assertCount(2, $export->data);
        foreach ($export->data as $response) {
            $this->assertEquals($participant->id, $response['participant_id']);
        }
    }
}