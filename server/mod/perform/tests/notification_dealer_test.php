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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\constants;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\expand_task;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\notification\dealer;
use mod_perform\notification\exceptions\class_key_not_available;
use mod_perform\notification\factory;
use totara_job\job_assignment;

require_once(__DIR__ . '/notification_testcase.php');

/**
 * @coversDefaultClass mod_perform\notification\dealer
 * @covers mod_perform\notification\factory
 * @group perform
 */
class mod_perform_notification_dealer_testcase extends mod_perform_notification_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->mock_loader(null);
        $this->setAdminUser();
    }

    /**
     * @covers ::__construct
     */
    public function test_constructor() {
        $user1 = $this->getDataGenerator()->create_user(['username' => 'user1']);
        $manager = $this->getDataGenerator()->create_user(['username' => 'manager']);
        $appraiser = $this->getDataGenerator()->create_user(['username' => 'appraiser']);
        $supervisor = $this->getDataGenerator()->create_user(['username' => 'supervisor']);
        $manmanja = job_assignment::create_default($supervisor->id);
        $manja = job_assignment::create_default($manager->id, ['managerjaid' => $manmanja->id]);
        $user1ja = job_assignment::create_default($user1->id, ['appraiserid' => $appraiser->id, 'managerjaid' => $manja->id]);
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $this->create_section_relationships($section);
        $track = $this->create_single_activity_track_and_assignment($activity, [$user1->id]);
        $element = $this->perfgen->create_element(['title' => 'Question one', 'plugin_name' => 'short_text']);
        $this->perfgen->create_section_element($section, $element);
        (new expand_task())->expand_all();

        $entities = $this->create_participant_instances_on_track($track);
        $this->assertCount(4, $entities);
        $this->assertCount(1, $entities->filter('core_relationship_id', $this->get_core_relationship(constants::RELATIONSHIP_SUBJECT)->id));
        $this->assertCount(1, $entities->filter('core_relationship_id', $this->get_core_relationship(constants::RELATIONSHIP_APPRAISER)->id));
        $this->assertEquals(1, subject_instance_entity::repository()->count());

        $prop = new ReflectionProperty(dealer::class, 'participant_instances');
        $prop->setAccessible(true);

        $dealer = factory::create_dealer_on_participant_instances($entities->all());
        $this->assertCount(4, $prop->getValue($dealer));

        $dealer = new dealer($entities->map_to(participant_instance_model::class)->all());
        $this->assertCount(4, $prop->getValue($dealer));

        // passing an empty array succeeds.
        $dealer = new dealer([]);
        $this->assertCount(0, $prop->getValue($dealer));

        try {
            new dealer($entities->all());
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString('participant_instances must be an array of participant_instance models', $ex->getMessage());
        }

        try {
            factory::create_dealer_on_participant_instances($entities->map(function(participant_instance_entity $e) {
                return $e->id;
            })->all());
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString('unknown element at 0', $ex->getMessage());
        }
    }

    /**
     * @covers ::dispatch
     */
    public function test_dispatch() {
        $user1 = $this->getDataGenerator()->create_user(['username' => 'user1']);
        $user2 = $this->getDataGenerator()->create_user(['username' => 'user2']);
        $manager = $this->getDataGenerator()->create_user(['username' => 'manager']);
        $appraiser = $this->getDataGenerator()->create_user(['username' => 'appraiser']);
        $manja = job_assignment::create_default($manager->id);
        $user1ja = job_assignment::create_default($user1->id, ['appraiserid' => $appraiser->id, 'managerjaid' => $manja->id]);
        $user2ja = job_assignment::create_default($user2->id, ['managerjaid' => $manja->id]);

        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $this->create_section_relationships($section);
        $track = $this->create_single_activity_track_and_assignment($activity, [$user1->id, $user2->id]);
        $element = $this->perfgen->create_element(['title' => 'Question one', 'plugin_name' => 'short_text']);
        $this->perfgen->create_section_element($section, $element);

        (new expand_task())->expand_multiple($track->assignments->map(function ($ass) {
            return $ass->id;
        })->all());

        $notif1 = $this->create_notification($activity, 'mock_one', false);
        $notif2 = $this->create_notification($activity, 'mock_two', true);
        $this->toggle_recipients($notif1, [constants::RELATIONSHIP_SUBJECT => true]);
        $this->toggle_recipients($notif2, [constants::RELATIONSHIP_SUBJECT => true, constants::RELATIONSHIP_APPRAISER => true]);

        $activity->activate();
        $this->assertTrue($activity->is_active());

        $entities = $this->create_participant_instances_on_track($track);
        $this->assertCount(5, $entities);
        $this->assertCount(2, $entities->filter('core_relationship_id', $this->get_core_relationship(constants::RELATIONSHIP_SUBJECT)->id));
        $this->assertCount(1, $entities->filter('core_relationship_id', $this->get_core_relationship(constants::RELATIONSHIP_APPRAISER)->id));
        $this->assertCount(2, $entities->filter('core_relationship_id', $this->get_core_relationship(constants::RELATIONSHIP_MANAGER)->id));
        $this->assertEquals(2, subject_instance_entity::repository()->count());
        $dealer = factory::create_dealer_on_participant_instances($entities->all());

        $sink = factory::create_sink();

        $sink->clear();
        $dealer->dispatch('mock_one');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        $sink->clear();
        $dealer->dispatch('mock_two');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(3, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        $sink->clear();
        $dealer->dispatch('mock_three');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        try {
            $dealer->dispatch('mock_zero');
            $this->fail('class_key_not_available expected');
        } catch (class_key_not_available $ex) {
        }
    }
}
