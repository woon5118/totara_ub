<?php
/*
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

use core\orm\query\exceptions\record_not_found_exception;
use mod_perform\entities\activity\activity_relationship as activity_relationship_entity;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section_relationship;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_section_relationship_model_testcase extends mod_perform_relationship_testcase {

    public function test_create_invalid_relationship_id() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);

        $this->expectException(record_not_found_exception::class);

        section_relationship::create(
            $section1->get_id(),
            -1,
            true
        );
    }

    public function test_create_invalid_section_id() {
        $this->setAdminUser();
        $non_existent_section_id = 1234;
        while (section::repository()->where('id', $non_existent_section_id)->exists()) {
            $non_existent_section_id ++;
        }
        $this->expectException(record_not_found_exception::class);

        section_relationship::create(
            $non_existent_section_id,
            $this->perform_generator()->get_relationship(subject::class)->id,
            true
        );
    }

    public function test_create_missing_capability() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $subject_id = $perform_generator->get_relationship(subject::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('you do not currently have permissions to do that (Manage performance activities)');

        section_relationship::create(
            $section1->get_id(),
            $subject_id,
            true
        );
    }

    public function test_create_successful() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $subject_id = $perform_generator->get_relationship(subject::class)->id;
        $manager_id = $perform_generator->get_relationship(manager::class)->id;
        $appraiser_id = $perform_generator->get_relationship(appraiser::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);

        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        $section_relationship = section_relationship::create(
            $section1->get_id(),
            $subject_id,
            true
        );
        $this->assertInstanceOf(section_relationship::class, $section_relationship);
        $this->assertEquals($section1->get_id(), $section_relationship->section_id);
        $this->assert_activity_relationships($activity1, [subject::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [subject::class]);
        $this->assert_section_relationships($section2, []);

        // Try to create the same - nothing should change.
        section_relationship::create(
            $section1->get_id(),
            $subject_id,
            true
        );
        $this->assert_activity_relationships($activity1, [subject::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [subject::class]);
        $this->assert_section_relationships($section2, []);

        // Add another one to the same section.
        section_relationship::create(
            $section1->get_id(),
            $manager_id,
            true
        );
        $this->assert_activity_relationships($activity1, [subject::class, manager::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [subject::class, manager::class]);
        $this->assert_section_relationships($section2, []);

        // Add another one to the other section.
        section_relationship::create(
            $section2->get_id(),
            $appraiser_id,
            true
        );
        $this->assert_activity_relationships($activity1, [subject::class, manager::class, appraiser::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [subject::class, manager::class]);
        $this->assert_section_relationships($section2, [appraiser::class]);
    }

    public function test_delete_invalid_relationship_id() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);

        $this->expectException(record_not_found_exception::class);

        section_relationship::delete_with_properties($section1->get_id(), -1);
    }

    public function test_delete_invalid_section_id() {
        $this->setAdminUser();
        $subject_id = $this->perform_generator()->get_relationship(subject::class)->id;
        $non_existent_section_id = 1234;
        while (section::repository()->where('id', $non_existent_section_id)->exists()) {
            $non_existent_section_id ++;
        }
        $this->expectException(record_not_found_exception::class);

        section_relationship::delete_with_properties($non_existent_section_id, $subject_id);
    }

    public function test_delete_missing_capability() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $subject = $perform_generator->get_relationship(subject::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('you do not currently have permissions to do that (Manage performance activities)');

        section_relationship::delete_with_properties($section1->get_id(), $subject);
    }

    public function test_delete_invalid_db_state() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $manager_id = $perform_generator->get_relationship(manager::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);
        /** @var activity $section2 */
        $section2 = $perform_generator->create_section($activity2);
        /** @var section_relationship $section_relationship */
        $section_relationship = $perform_generator->create_section_relationship($section1, ['class_name' => manager::class]);
        $activity_relationship_entity = activity_relationship_entity::repository()
            ->where('activity_id', $activity1->get_id())
            ->where('core_relationship_id', $manager_id)
            ->one(true);

        // Update DB record directly with wrong section id.
        /** @var section_relationship_entity $section_relationship_entity */
        $section_relationship_entity = section_relationship_entity::repository()->find($section_relationship->get_id());
        $correct_section_id = $section1->get_id();
        $bad_section_id = $section2->get_id();
        $section_relationship_entity->section_id = $bad_section_id;
        $section_relationship_entity->save();

        $this->expectException(invalid_state_exception::class);
        $this->expectExceptionMessage(
            "Record found in perform_relationship without corresponding section_relationship record. "
            . "section_id {$correct_section_id}, activity_relationship_id {$activity_relationship_entity->id}"
        );

        section_relationship::delete_with_properties($section1->get_id(), $manager_id);
    }

    public function test_delete_successful() {
        $this->setAdminUser();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->perform_generator();
        $appraiser_id = $perform_generator->get_relationship(appraiser::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);

        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        $perform_generator->create_section_relationship($section1, ['class_name' => manager::class]);
        $perform_generator->create_section_relationship($section1, ['class_name' => subject::class]);
        $perform_generator->create_section_relationship($section1, ['class_name' => appraiser::class]);
        $perform_generator->create_section_relationship($section2, ['class_name' => appraiser::class]);
        $this->assert_activity_relationships($activity1, [manager::class, subject::class, appraiser::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [manager::class, subject::class, appraiser::class]);
        $this->assert_section_relationships($section2, [appraiser::class]);

        section_relationship::delete_with_properties($section1->get_id(), $appraiser_id);
        $this->assert_activity_relationships($activity1, [manager::class, subject::class, appraiser::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [manager::class, subject::class]);
        $this->assert_section_relationships($section2, [appraiser::class]);

        section_relationship::delete_with_properties($section2->get_id(), $appraiser_id);
        $this->assert_activity_relationships($activity1, [manager::class, subject::class]);
        $this->assert_activity_relationships($activity2, []);
        $this->assert_section_relationships($section1, [manager::class, subject::class]);
        $this->assert_section_relationships($section2, []);
    }
}