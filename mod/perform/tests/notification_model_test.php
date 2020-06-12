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

use mod_perform\entities\activity\notification as notification_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\notification;
use mod_perform\notification\broker;
use mod_perform\notification\brokers\instance_created;
use mod_perform\notification\brokers\overdue;
use totara_core\relationship\relationship_provider;

class mod_perform_notification_model_testcase extends advanced_testcase {
    /** @var mod_perform_generator */
    private $perfgen;

    public function setUp(): void {
        $this->setAdminUser();
        $this->perfgen = $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    public function tearDown(): void {
        $this->perfgen = null;
    }

    /**
     * Create an activity for testing.
     *
     * @param array $data
     * @return activity
     */
    public function create_test_activity(array $data = []): activity {
        return $this->perfgen->create_activity_in_container($data);
    }

    /**
     * @return array
     */
    public function data_create_success(): array {
        return [
            ['instance_created', 'Participant instance created'],
        ];
    }

    /**
     * @param string $class_key
     * @param string $name_expected
     * @dataProvider data_create_success
     */
    public function test_create_success(string $class_key, string $name_expected) {
        $activity = $this->create_test_activity();
        $time = time();
        $notification = notification::create($activity, $class_key);
        $this->assertEquals($activity->id, $notification->activity->get_id());
        $this->assertEquals($name_expected, $notification->name); // FIXME: fill this field
        $this->assertFalse($notification->active);
        $this->assertSame(0, $notification->trigger_count);

        $entity = new notification_entity($notification->get_id());
        $this->assertEquals($class_key, $entity->class_key);
        $this->assertEqualsWithDelta($time, $entity->created_at, 2);
        $this->assertEqualsWithDelta($time, $entity->updated_at, 2);
    }

    public function test_create_failure() {
        $activity = $this->create_test_activity();
        try {
            $notification = notification::create($activity, 'he_who_must_not_be_named');
            $this->fail('coding_exception expected');
        } catch (\coding_exception $ex) {
        }
    }

    public function test_delete() {
        $activity = $this->create_test_activity();
        $notification = notification::create($activity, 'instance_created');
        $notification->delete();
    }

    public function test_recipients() {
        $activity = $this->create_test_activity();
        $notification = notification::create($activity, 'instance_created');
        // TODO: ?? figure out whether recipients returns all recipients in totara_core_relationship or just the ones in perform_relationship ??
        // $this->assertCount(3, $notification->recipients);
        $rels = relationship_provider::fetch_all_relationships();
        // FIXME: ... add recipients here
        // $notification->recipients;
        // FIXME: ... and test here
        $this->markTestIncomplete('todo');
    }

    public function test_activate() {
        $activity = $this->create_test_activity();
        $notification = notification::create($activity, 'instance_created');
        $this->assertFalse($notification->active);
        $notification->activate();
        $this->assertTrue($notification->active);
        $notification->activate(false);
        $this->assertFalse($notification->active);
        $notification->activate(true);
        $this->assertTrue($notification->active);
        $notification->delete();
        try {
            $notification->activate();
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
        }
    }
}
