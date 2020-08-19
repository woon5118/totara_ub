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

use core\orm\query\builder;
use mod_perform\constants;
use mod_perform\entities\activity\notification;
use mod_perform\entities\activity\subject_instance;
use mod_perform\expand_task;
use mod_perform\notification\exceptions\class_key_not_available;
use mod_perform\notification\factory;
use totara_job\job_assignment;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_cartel_testcase extends mod_perform_notification_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->mock_loader(null);
    }

    public function test_dispatch() {
        $this->setAdminUser();
        $user1 = $this->getDataGenerator()->create_user(['username' => 'user1']);
        $user2 = $this->getDataGenerator()->create_user(['username' => 'user2']);
        $manager = $this->getDataGenerator()->create_user(['username' => 'manager']);
        $appraiser = $this->getDataGenerator()->create_user(['username' => 'appraiser']);
        $user1ja = job_assignment::create_default($user1->id, ['appraiserid' => $appraiser->id]);
        job_assignment::create_default($manager->id, ['managerjaid' => $user1ja->id]);
        $user2ja = job_assignment::create_default($user2->id);
        job_assignment::create_default($manager->id, ['managerjaid' => $user2ja->id]);

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
        $this->assertCount(3, $entities);
        $this->assertEquals(2, subject_instance::repository()->count());
        $cartel = factory::create_cartel_on_participant_instances($entities);

        $sink = factory::create_sink();

        $sink->clear();
        $cartel->dispatch('mock_one');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        $sink->clear();
        $cartel->dispatch('mock_two');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(3, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        $sink->clear();
        $cartel->dispatch('mock_three');
        $this->assertEquals(0, $sink->get_by_class_key('mock_one')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_two')->count());
        $this->assertEquals(0, $sink->get_by_class_key('mock_three')->count());

        try {
            $cartel->dispatch('mock_zero');
            $this->fail('class_key_not_available expected');
        } catch (class_key_not_available $ex) {
        }
    }
}
