<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package core_course
 * @category totara_catalog
 */

namespace core_course\totara_catalog\course;

use core_course\totara_catalog\course as course_provider;
use \core\event\course_created;

/**
 * @group totara_catalog
 */
class catalog_course_object_update_observer extends \advanced_testcase {

    /**
     * @var \stdClass
     */
    private $course = null;

    protected function setUp() {
        parent::setup();
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
    }

    protected function tearDown() {
        $this->course = null;
        parent::tearDown();
    }

    public function test_get_observer_events() {
        foreach ($this->get_update_observers() as $observer) {
            $applicable_events = $observer->get_observer_events();
            $this->assertNotEmpty($applicable_events);
        }
    }

    public function test_update_object() {
        $updateobjects = $this->get_update_observers()['course']->get_update_objects();

        $this->assertSame($this->course->id, $updateobjects[0]->objectid);
        $this->assertSame(course_provider::get_object_type(), $updateobjects[0]->objecttype);
    }

    public function test_delete_object() {
        $deleteobjects = $this->get_update_observers()['course_delete']->get_delete_object_ids();
        $this->assertTrue(in_array($this->course->id, $deleteobjects));
    }

    public function test_process() {
        global $DB;
        $this->get_update_observers()['course']->process();

        $count = $DB->count_records('catalog', ['objecttype' => course_provider::get_object_type()]);
        $this->assertSame(1, $count);
    }

    private function get_update_observers() {
        $observers = [];

        // Get observer classes.
        $classes = \core_component::get_namespace_classes(
            'totara_catalog\course\observer',
            'totara_catalog\observer\object_update_observer'
        );

        // Create course created event.
        $event = course_created::create(
            [
                'objectid' => $this->course->id,
                'context'  => \context_course::instance($this->course->id),
                'other'    => [
                    'shortname' => $this->course->shortname,
                    'fullname'  => $this->course->fullname,
                ],
            ]
        );

        foreach ($classes as $class) {
            $observer = new $class(
                course_provider::get_object_type(),
                $event
            );
            $shortclassname = str_replace('core_course\\totara_catalog\\course\\observer\\', '', get_class($observer));
            $observers[$shortclassname] = $observer;
        }

        return $observers;
    }
}
