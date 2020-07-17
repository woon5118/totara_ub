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

use mod_perform\notification\broker;
use mod_perform\notification\brokers\due_date_reminder;
use mod_perform\notification\brokers\instance_created;
use mod_perform\notification\brokers\overdue_reminder;
use mod_perform\notification\loader;
use mod_perform\notification\trigger;

class mod_perform_notification_loader_testcase extends advanced_testcase {
    /**
     * Ensure that the $child class inherits the $parent class.
     *
     * @param string $parent
     * @param string $child
     */
    private function assertSubclassOf(string $parent, string $child) {
        if (!class_exists($parent) && !interface_exists($parent)) {
            $this->fail("Class {$parent} does not exist");
        }
        if (!class_exists($child)) {
            $this->fail("Class {$child} does not exist");
        }
        $rc = new \ReflectionClass($child);
        if (!$rc->isSubclassOf($parent)) {
            $this->fail("Class {$child} is not a subclass of {$parent}");
        }
    }

    /**
     * @return array
     */
    public function data_validate_failure(): array {
        return [
            'empty' => [[], 'notification data is empty'],
            'missing class' => [[
                'kia_ora' => ['name' => 'kia ora', 'trigger_type' => trigger::TYPE_UNSUPPORTED]
            ], 'class is missing for kia_ora'],
            'missing name' => [[
                'kia_ora' => ['class' => 'kia\\ora', 'trigger_type' => trigger::TYPE_UNSUPPORTED]
            ], 'name is missing for kia_ora'],
            'missing trigger type' => [[
                'kia_ora' => ['name' => 'kia ora', 'class' => 'kia\\ora']
            ], 'trigger_type is missing for kia_ora'],
            'missing trigger label 1' => [[
                'kia_ora' => ['name' => 'kia ora', 'class' => 'kia\\ora', 'trigger_type' => trigger::TYPE_BEFORE]
            ], 'trigger_label is missing for kia_ora'],
            'missing trigger label 2' => [[
                'kia_ora' => ['name' => 'kia ora', 'class' => 'kia\\ora', 'trigger_type' => trigger::TYPE_AFTER]
            ], 'trigger_label is missing for kia_ora'],
        ];
    }

    /**
     * @param array $notifications
     * @param string $expected_message
     * @dataProvider data_validate_failure
     */
    public function test_validate_throws_exception(array $notifications, string $expected_message) {
        try {
            loader::create($notifications);
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString($expected_message, $ex->a);
        }
    }

    public function test_get_classes_default() {
        $loader = loader::create();
        $brokers = $loader->get_classes();
        foreach ($brokers as $broker) {
            $this->assertSubclassOf(broker::class, $broker);
        }
    }

    private function create_loader() {
        $notifications = [
            'test_instance_created' => [
                'class' => instance_created::class,
                'name' => ['notification_broker_instance_created', 'mod_perform'],
                'trigger_type' => trigger::TYPE_UNSUPPORTED,
            ],
            'test_overdue_reminder' => [
                'class' => overdue_reminder::class,
                'name' => ['screenshot', 'moodle'],
                'trigger_type' => trigger::TYPE_AFTER,
                'trigger_label' => ['readme'],
            ],
            'test_due_date_reminder' => [
                'class' => due_date_reminder::class,
                'name' => ['downloadfile'],
                'trigger_type' => trigger::TYPE_BEFORE,
                'trigger_label' => ['tags', 'moodle'],
            ],
            'kia_ora_koutou_katoa' => [
                'class' => mod_perform_notification_loader_testcase::class,
                'name' => ['ok'],
                'trigger_type' => trigger::TYPE_UNSUPPORTED,
                'trigger_label' => ['ok'],
            ],
        ];
        return loader::create($notifications);
    }

    public function test_get_classes_custom() {
        $loader = $this->create_loader();
        $brokers = $loader->get_classes();
        $this->assertCount(4, $brokers);
        $keys = array_keys($brokers);
        $this->assertEquals('test_instance_created', $keys[0]);
        $this->assertEquals('test_overdue_reminder', $keys[1]);
        $this->assertEquals('test_due_date_reminder', $keys[2]);
        $this->assertEquals('kia_ora_koutou_katoa', $keys[3]);
        $values = array_values($brokers);
        $this->assertEquals(instance_created::class, $values[0]);
        $this->assertEquals(overdue_reminder::class, $values[1]);
        $this->assertEquals(due_date_reminder::class, $values[2]);
        $this->assertEquals(mod_perform_notification_loader_testcase::class, $values[3]);
    }

    public function test_get_class_of() {
        $loader = $this->create_loader();
        $this->assertEquals(instance_created::class, $loader->get_class_of('test_instance_created'));
        $this->assertEquals(overdue_reminder::class, $loader->get_class_of('test_overdue_reminder'));
        $this->assertEquals(due_date_reminder::class, $loader->get_class_of('test_due_date_reminder'));
        $this->assertEquals(mod_perform_notification_loader_testcase::class, $loader->get_class_of('kia_ora_koutou_katoa'));
        try {
            $loader->get_class_of('he_who_must_not_be_named');
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->debuginfo);
        }
    }

    public function test_get_name_of() {
        $loader = $this->create_loader();
        $this->assertEquals(get_string('notification_broker_instance_created', 'mod_perform'), $loader->get_name_of('test_instance_created'));
        $this->assertEquals(get_string('screenshot', 'moodle'), $loader->get_name_of('test_overdue_reminder'));
        $this->assertEquals(get_string('downloadfile'), $loader->get_name_of('test_due_date_reminder'));
        $this->assertEquals(get_string('ok'), $loader->get_name_of('kia_ora_koutou_katoa'));
        try {
            $loader->get_name_of('he_who_must_not_be_named');
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->debuginfo);
        }
    }

    public function test_get_trigger_type_of() {
        $loader = $this->create_loader();
        $this->assertEquals(trigger::TYPE_UNSUPPORTED, $loader->get_trigger_type_of('test_instance_created'));
        $this->assertEquals(trigger::TYPE_AFTER, $loader->get_trigger_type_of('test_overdue_reminder'));
        $this->assertEquals(trigger::TYPE_BEFORE, $loader->get_trigger_type_of('test_due_date_reminder'));
        $this->assertEquals(trigger::TYPE_UNSUPPORTED, $loader->get_trigger_type_of('kia_ora_koutou_katoa'));
    }

    public function test_get_trigger_label_of() {
        $loader = $this->create_loader();
        $this->assertNull($loader->get_trigger_label_of('test_instance_created'));
        $this->assertEquals(get_string('trigger_after', 'mod_perform', ['name' => get_string('readme')]), $loader->get_trigger_label_of('test_overdue_reminder'));
        $this->assertEquals(get_string('trigger_before', 'mod_perform', ['name' => get_string('tags', 'moodle')]), $loader->get_trigger_label_of('test_due_date_reminder'));
        $this->assertNull($loader->get_trigger_label_of('kia_ora_koutou_katoa'));
    }

    public function test_support_triggers() {
        $loader = $this->create_loader();
        $this->assertFalse($loader->support_triggers('test_instance_created'));
        $this->assertTrue($loader->support_triggers('test_overdue_reminder'));
        $this->assertTrue($loader->support_triggers('test_due_date_reminder'));
        $this->assertFalse($loader->support_triggers('kia_ora_koutou_katoa'));
        try {
            $loader->support_triggers('he_who_must_not_be_named');
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->debuginfo);
        }
    }
}
