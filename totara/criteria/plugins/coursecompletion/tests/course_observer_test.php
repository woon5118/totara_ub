<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use core\event\course_completed;
use criteria_coursecompletion\observer\course as course_observer;
use totara_criteria\event\criteria_achievement_changed;

class criteria_coursecompletion_course_observer_testcase extends \advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $courses =  [];
            public $users = [];
        };

        for ($course_idx = 1; $course_idx <= 2; $course_idx++) {
            $data->courses[$course_idx] = $this->getDataGenerator()->create_course();

            for ($user_idx = 1; $user_idx <= 2; $user_idx++) {
                $data->users[$user_idx] = $this->getDataGenerator()->create_user();
                $this->getDataGenerator()->enrol_user($data->users[$user_idx]->id, $data->courses[$course_idx]->id);
            }
        }

        return $data;
    }

    public function test_course_completed_no_item() {
        $data = $this->setup_data();

        // No other way to stop event propagation without sinking the course_completed event and manually pass it to the function
        $sink = $this->redirectEvents();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $sink->clear();

        course_observer::course_completed($cc_event);
        $events = $sink->get_events();
        $this->assertEmpty($events);
        $sink->close();

        // We now generate a coursecompletion criterion but not for this course
        /** @var \totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $criterion = $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]);

        $sink = $this->redirectEvents();
        $completion = new completion_completion(['course' => $data->courses[2]->id, 'userid' => $data->users[2]->id]);
        $completion->mark_complete();

        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $sink->clear();

        $sink = $this->redirectEvents();
        course_observer::course_completed($cc_event);
        $events = $sink->get_events();
        $this->assertEmpty($events);
        $sink->close();
    }

    public function test_course_completed_single_item() {
        $data = $this->setup_data();
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $criterion = $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]);

        // No other way to stop event propagation without sinking the course_completed event and manually pass it to the function
        $sink = $this->redirectEvents();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $sink->clear();

        course_observer::course_completed($cc_event);
        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $event = reset($events);
        $this->assertEquals(criteria_achievement_changed::class, get_class($event));

        $this->assertEqualsCanonicalizing([$criterion->get_id()], $event->other['criteria_ids']);
        $sink->close();
    }

    public function test_course_completed_multiple_items() {
        $data = $this->setup_data();
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $criterion1 = $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]);
        $criterion2 = $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]);

        // No other way to stop event propagation without sinking the course_completed event and manually pass it to the function
        $sink = $this->redirectEvents();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $sink->clear();

        course_observer::course_completed($cc_event);
        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $event = reset($events);
        $this->assertEquals(criteria_achievement_changed::class, get_class($event));

        $this->assertEqualsCanonicalizing([$criterion1->get_id(), $criterion2->get_id()], $event->other['criteria_ids']);
        $sink->close();
    }

}
