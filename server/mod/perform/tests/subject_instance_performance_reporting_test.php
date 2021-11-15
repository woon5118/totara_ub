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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use core\orm\query\builder;
use mod_perform\constants;
use mod_perform\dates\date_offset;
use mod_perform\entity\activity\subject_instance;
use mod_perform\entity\activity\track as track_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use mod_perform\task\service\subject_instance_creation;

/**
 * @group perform
 */
class mod_perform_subject_instance_performance_reporting_testcase extends advanced_testcase {

    public function test_report_data_with_repeating_subject_instances() {
        self::setAdminUser();

        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $config = mod_perform_activity_generator_configuration::new()
            ->disable_subject_instances()
            ->enable_appraiser_for_each_subject_user()
            ->enable_manager_for_each_subject_user()
            ->set_relationships_per_section([
                constants::RELATIONSHIP_SUBJECT,
                constants::RELATIONSHIP_MANAGER,
                constants::RELATIONSHIP_APPRAISER
            ])
            ->set_number_of_users_per_user_group_type(2);
        /** @var activity $activity */
        $activity = $generator->create_full_activities($config)->first();
        /** @var track $track */
        $track = $activity->get_tracks()->first();

        // Set repeat to one day after creation.
        $offset = new date_offset(1, date_offset::UNIT_DAY);
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
            $offset
        );
        $track->update();

        // Create initial instances.
        (new subject_instance_creation())->generate_instances();
        $subject_instances = subject_instance::repository()->get()->all();
        self::assertCount(2, $subject_instances);

        /** @var subject_instance $subject_instance_1 */
        $subject_instance_1 = $subject_instances[0];

        // Have a repeating instance created by manipulating created_date.
        $subject_instance_1->created_at = time() - (2 * 86400);
        $subject_instance_1->update();
        (new subject_instance_creation())->generate_instances();
        self::assertCount(3, subject_instance::repository()->get());

        // Pick the subject user with the repeating instance.
        $subject_user_1_id = $subject_instance_1->subject_user_id;
        $subject_instances_user_1 = subject_instance::repository()
            ->where('subject_user_id', $subject_user_1_id)
            ->get();
        self::assertCount(2, $subject_instances_user_1);

        // Set up report.
        $config = new rb_config();
        $config->set_embeddata(['subject_user_id' => $subject_user_1_id]);
        $report = reportbuilder::create_embedded('perform_response_subject_instance', $config);
        [$sql, $sqlparams, ] = $report->build_query(false, false, false);
        $records = builder::get_db()->get_records_sql($sql, $sqlparams);

        $report_subject_instance_ids = [];
        $report_instance_numbers = [];
        foreach ($records as $record) {
            $report_subject_instance_ids[] = $record->id;
            $report_instance_numbers[] = $record->subject_instance_instance_number;
            self::assertEquals(3, $record->subject_instance_participant_count_performance_reporting);
        }
        self::assertEqualsCanonicalizing($subject_instances_user_1->pluck('id'), $report_subject_instance_ids);
        self::assertEqualsCanonicalizing([1, 2], $report_instance_numbers);
    }

    public function test_capabilities() {
        self::setAdminUser();

        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $config = mod_perform_activity_generator_configuration::new()
            ->enable_manager_for_each_subject_user()
            ->set_number_of_users_per_user_group_type(1);
        /** @var activity $activity */
        $generator->create_full_activities($config);

        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()->one();
        $subject_user_id = $subject_instance->subject_user_id;

        // Set up report.
        $config = new rb_config();
        $config->set_embeddata(['subject_user_id' => $subject_user_id]);
        $report = reportbuilder::create_embedded('perform_response_subject_instance', $config);
        $embedded_object = $report->embedobj;

        self::assertTrue($embedded_object->is_capable(get_admin()->id, $report));

        $user = self::getDataGenerator()->create_user();
        self::assertFalse($embedded_object->is_capable($user->id, $report));

        $user_role = builder::get_db()->get_record('role', ['shortname' => 'user']);
        $subject_user_context_id = context_user::instance($subject_user_id)->id;
        assign_capability(
            'mod/perform:report_on_subject_responses',
            CAP_ALLOW,
            $user_role->id,
            $subject_user_context_id,
            true
        );
        self::assertTrue($embedded_object->is_capable($user->id, $report));

        unassign_capability('mod/perform:report_on_subject_responses', $user_role->id, $subject_user_context_id);
        self::assertFalse($embedded_object->is_capable($user->id, $report));

        assign_capability(
            'mod/perform:report_on_all_subjects_responses',
            CAP_ALLOW,
            $user_role->id,
            context_user::instance($user->id)->id,
            true
        );
        self::assertTrue($embedded_object->is_capable($user->id, $report));
    }
}
