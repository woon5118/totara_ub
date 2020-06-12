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
use mod_perform\notification\loader;
use totara_core\relationship\relationship_provider;

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
                'kia_ora' => ['name' => 'kia ora']
            ], 'class is missing for kia_ora'],
            'missing name' => [[
                'kia_ora' => ['class' => 'kia\\ora']
            ], 'name is missing for kia_ora'],
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
                'name' => ['notification_instance_created', 'mod_perform'],
                'has_triggers' => false,
            ],
            'test_overdue' => [
                'class' => overdue::class,
                'name' => ['screenshot', 'moodle'],
                'has_triggers' => true,
            ],
            'kia_ora_koutou_katoa' => [
                'class' => mod_perform_notification_loader_testcase::class,
                'name' => ['ok'],
            ],
        ];
        return loader::create($notifications);
    }

    public function test_get_classes_custom() {
        $loader = $this->create_loader();
        $brokers = $loader->get_classes();
        $this->assertCount(3, $brokers);
        $keys = array_keys($brokers);
        $this->assertEquals('test_instance_created', $keys[0]);
        $this->assertEquals('test_overdue', $keys[1]);
        $this->assertEquals('kia_ora_koutou_katoa', $keys[2]);
        $values = array_values($brokers);
        $this->assertEquals(instance_created::class, $values[0]);
        $this->assertEquals(overdue::class, $values[1]);
        $this->assertEquals(mod_perform_notification_loader_testcase::class, $values[2]);
    }

    public function test_get_class_of() {
        $loader = $this->create_loader();
        $this->assertEquals(instance_created::class, $loader->get_class_of('test_instance_created'));
        $this->assertEquals(overdue::class, $loader->get_class_of('test_overdue'));
        $this->assertEquals(mod_perform_notification_loader_testcase::class, $loader->get_class_of('kia_ora_koutou_katoa'));
        try {
            $loader->get_class_of('he_who_must_not_be_named');
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->a);
        }
    }

    public function test_get_name_of() {
        $loader = $this->create_loader();
        $this->assertEquals(get_string('notification_instance_created', 'mod_perform'), $loader->get_name_of('test_instance_created'));
        $this->assertEquals(get_string('screenshot', 'moodle'), $loader->get_name_of('test_overdue'));
        $this->assertEquals(get_string('ok'), $loader->get_name_of('kia_ora_koutou_katoa'));
        try {
            $loader->get_name_of('he_who_must_not_be_named');
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->a);
        }
    }

    public function test_has_triggers() {
        $loader = $this->create_loader();
        $this->assertFalse($loader->has_triggers('test_instance_created'));
        $this->assertTrue($loader->has_triggers('test_overdue'));
        $this->assertFalse($loader->has_triggers('kia_ora_koutou_katoa'));
        try {
            $loader->has_triggers('he_who_must_not_be_named');
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertStringContainsString('notification he_who_must_not_be_named is not registered', $ex->a);
        }
    }
}
