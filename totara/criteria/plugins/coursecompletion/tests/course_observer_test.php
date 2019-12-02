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
use totara_completionimport\event\bulk_course_completionimport;
use totara_criteria\hook\criteria_achievement_changed;

class criteria_coursecompletion_course_observer_testcase extends advanced_testcase {

    const NUM_USERS = 5;
    const NUM_COURSES = 5;

    private function setup_data() {
        $data = new class() {
            public $courses =  [];
            public $users = [];
        };

        for ($user_idx = 1; $user_idx <= self::NUM_USERS; $user_idx++) {
            $data->users[$user_idx] = $this->getDataGenerator()->create_user();
        }

        for ($course_idx = 1; $course_idx <= self::NUM_COURSES; $course_idx++) {
            $data->courses[$course_idx] = $this->getDataGenerator()->create_course();

            for ($user_idx = 1; $user_idx <= self::NUM_USERS; $user_idx++) {
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

        course_observer::course_completion_changed($cc_event);
        $events = $sink->get_events();
        $this->assertEmpty($events);
        $sink->close();

        // We now generate a coursecompletion criterion but not for this course
        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]);

        $sink = $this->redirectEvents();
        $completion = new completion_completion(['course' => $data->courses[2]->id, 'userid' => $data->users[2]->id]);
        $completion->mark_complete();

        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $sink->clear();

        $sink = $this->redirectHooks();
        course_observer::course_completion_changed($cc_event);
        $hooks = $sink->get_hooks();
        $this->assertEmpty($hooks);
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
        $sink->close();

        $sink = $this->redirectHooks();
        course_observer::course_completion_changed($cc_event);
        $this->verify_hook($sink, [$data->users[1]->id => [$criterion->get_id()]]);
        $sink->close();
    }

    public function test_course_completed_multiple_items() {
        $data = $this->setup_data();
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $criteria = [
            1 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]),
            2 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id], $data->courses[2]->id]),
            3 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[2]->id], $data->courses[3]->id])
        ];

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
        $sink->close();

        $sink = $this->redirectHooks();
        course_observer::course_completion_changed($cc_event);
        $this->verify_hook($sink, [$data->users[1]->id => [$criteria[1]->get_id(), $criteria[2]->get_id()]]);
        $sink->close();
    }

    public function test_bulk_course_completions_imported() {
        $data = $this->setup_data();
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $criteria = [
            1 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id]]),
            2 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[1]->id, $data->courses[2]->id]]),
            3 => $criteria_generator->create_coursecompletion(['courseids' => [$data->courses[3]->id]]),
        ];

        /**
         * Create event
         * user1 - completes course1
         * user2 - completes courses 1 and 2
         * user3 - completes course 3
         * user4 - completes courses 3 and 4
         * user5 - completes course 4
         */
        $course_completions = [
            ['userid' => $data->users[1]->id, 'courseid' => $data->courses[1]->id],
            ['userid' => $data->users[2]->id, 'courseid' => $data->courses[1]->id],
            ['userid' => $data->users[2]->id, 'courseid' => $data->courses[2]->id],
            ['userid' => $data->users[3]->id, 'courseid' => $data->courses[3]->id],
            ['userid' => $data->users[4]->id, 'courseid' => $data->courses[3]->id],
            ['userid' => $data->users[4]->id, 'courseid' => $data->courses[4]->id],
            ['userid' => $data->users[5]->id, 'courseid' => $data->courses[4]->id],
        ];

        $import_event = bulk_course_completionimport::create_from_list($course_completions);

        $sink = $this->redirectHooks();

        course_observer::bulk_course_completions_imported($import_event);
        $this->verify_hook($sink, [
            $data->users[1]->id => [$criteria[1]->get_id(), $criteria[2]->get_id()],
            $data->users[2]->id => [$criteria[1]->get_id(), $criteria[2]->get_id()],
            $data->users[3]->id => [$criteria[3]->get_id()],
            $data->users[4]->id => [$criteria[3]->get_id()],
        ]);
        $sink->close();
    }



    /**
     * @param phpunit_hook_sink $sink
     * @param array $expected_user_criteria_ids
     */
    private function verify_hook(phpunit_hook_sink $sink, array $expected_user_criteria_ids) {
        $hooks = $sink->get_hooks();
        $this->assertEquals(1, count($hooks));
        /** @var criteria_achievement_changed $hook */
        $hook = reset($hooks);
        $this->assertEquals(criteria_achievement_changed::class, get_class($hook));

        $this->assertEqualsCanonicalizing($expected_user_criteria_ids, $hook->get_user_criteria_ids());
        $sink->clear();
    }
}
