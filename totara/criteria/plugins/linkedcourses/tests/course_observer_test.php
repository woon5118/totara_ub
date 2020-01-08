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
 * @package criteria_linkedcourses
 */

use core\event\course_completed;
use core\event\course_deleted;
use core\event\course_restored;
use totara_competency\entities\course as course_entity;
use totara_competency\linked_courses;
use totara_completionimport\event\bulk_course_completionimport;
use criteria_linkedcourses\observer\course as course_observer;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\hook\criteria_achievement_changed;
use totara_criteria\hook\criteria_validity_changed;

class criteria_linkedcourses_course_observer_testcase extends advanced_testcase {

    const NUM_USERS = 5;
    const NUM_COURSES = 5;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        global $CFG;
        require_once($CFG->dirroot . '/completion/completion_completion.php');
    }

    private function setup_data(int $num_criteria = 0) {
        global $CFG;
        $data = new class() {
            /** @var competency_entity $competency */
            public $competency;
            /** @var criterion[] $criterion */
            public $criteria = [];
            /** @var array int[] $criteria_ids */
            public $criteria_ids = [];
            public $courses =  [];
            public $users = [];
        };

        $CFG->enablecompletion = true;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $data->competency = $competency_generator->create_competency('Comp A');

        for ($user_idx = 1; $user_idx <= self::NUM_USERS; $user_idx++) {
            $data->users[$user_idx] = $this->getDataGenerator()->create_user();
        }

        for ($course_idx = 1; $course_idx <= self::NUM_COURSES; $course_idx++) {
            $data->courses[$course_idx] = $this->getDataGenerator()->create_course(['enablecompletion' => true]);

            for ($user_idx = 1; $user_idx <= self::NUM_USERS; $user_idx++) {
                $this->getDataGenerator()->enrol_user($data->users[$user_idx]->id, $data->courses[$course_idx]->id);
            }
        }

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        for ($i = 1; $i <= $num_criteria; $i++) {
            $data->criteria[$i] = $criteria_generator->create_linkedcourses(['competency' => $data->competency->id]);
            $data->criteria_ids[$i] = $data->criteria[$i]->get_id();
        }

        return $data;
    }

    public function test_course_completed_no_criterion() {
        $data = $this->setup_data(0);

        /** @var phpunit_event_sink $event_sink */
        $event_sink = $this->redirectEvents();
        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $event_sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));
        $event_sink->clear();

        course_observer::course_completion_changed($cc_event);
        $events = $event_sink->get_events();
        $this->assertEmpty($events);

        $hook_sink->close();
        $event_sink->close();
    }

    public function test_course_completed_single_item() {
        $data = $this->setup_data(1);

        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        /** @var phpunit_event_sink $event_sink */
        $event_sink = $this->redirectEvents();
        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $event_sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));

        course_observer::course_completion_changed($cc_event);
        $this->verify_achievement_changed_hook($hook_sink, [$data->users[1]->id => [$data->criteria[1]->get_id()]]);

        $event_sink->close();
        $hook_sink->close();
    }

    public function test_course_completed_multiple_items() {
        $data = $this->setup_data(2);

        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        /** @var phpunit_event_sink $event_sink */
        $event_sink = $this->redirectEvents();
        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        $events = $event_sink->get_events();
        // Expecting the course_completed
        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_completed::class, get_class($cc_event));

        course_observer::course_completion_changed($cc_event);
        $this->verify_achievement_changed_hook($hook_sink,
            [$data->users[1]->id => [$data->criteria[1]->get_id(), $data->criteria[2]->get_id()]]);

        $event_sink->close();
        $hook_sink->close();
    }

    public function test_bulk_course_completions_imported() {
        $data = $this->setup_data(1);

        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

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

        course_observer::bulk_course_completions_imported($import_event);
        $this->verify_achievement_changed_hook($hook_sink, [
            $data->users[1]->id => [$data->criteria[1]->get_id()],
            $data->users[2]->id => [$data->criteria[1]->get_id()],
        ]);

        $hook_sink->close();
    }


    public function test_course_deleted_not_used() {
        $data = $this->setup_data(1);
        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(1, $nvalid);

        /** @var phpunit_event_sink $event_sink */
        $event_sink = $this->redirectEvents();
        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        delete_course($data->courses[4], false);

        $events = $event_sink->get_events();
        // Expecting the course_deleted event
        $events = array_filter($events, function ($event) {
            return $event instanceof course_deleted;
        });

        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_deleted::class, get_class($cc_event));

        course_observer::course_deleted($cc_event);
        $this->assertEmpty($hook_sink->get_hooks());

        // Verify that the status didn't changed on disk
        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(1, $nvalid);

        $event_sink->close();
        $hook_sink->close();
    }

    public function test_course_deleted_linked() {
        $data = $this->setup_data(2);

        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(2, $nvalid);

        /** @var phpunit_event_sink $event_sink */
        $event_sink = $this->redirectEvents();
        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        delete_course($data->courses[1], false);

        $events = $event_sink->get_events();
        // Expecting the course_deleted event
        $events = array_filter($events, function ($event) {
            return $event instanceof course_deleted;
        });

        $this->assertEquals(1, count($events));
        $cc_event = reset($events);
        $this->assertEquals(course_deleted::class, get_class($cc_event));

        course_observer::course_deleted($cc_event);
        $this->verify_validity_changed_hook($hook_sink, $data->criteria_ids);

        // Verify that the status changed on disk
        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(0, $nvalid);

        $ninvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 0)
            ->count();
        $this->assertEquals(2, $ninvalid);

        $event_sink->close();
        $hook_sink->close();
    }

    public function test_course_restored() {
        global $CFG;

        require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
        require_once($CFG->dirroot . '/backup/backup.class.php');

        $data = $this->setup_data(1);

        // Delete the course first to ensure the criteria's valid attribute is set correctly
        $deleted_course_id = $data->courses[3]->id;
        $deleted_course = new course_entity($deleted_course_id);
        $deleted_course->delete();

        linked_courses::set_linked_courses($data->competency->id, [
            ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ['id' => $data->courses[3]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
        ]);

        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(0, $nvalid);

        $ninvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 0)
            ->count();
        $this->assertEquals(1, $ninvalid);

        $restored_course = $this->getDataGenerator()->create_course(['enablecompletion' => true]);

        /** @var phpunit_hook_sink $hook_sink */
        $hook_sink = $this->redirectHooks();

        $cc_event = course_restored::create([
            'objectid' => $restored_course->id,
            'context' => context_course::instance($restored_course->id),
            'other' => [
                'type' => backup::TYPE_1COURSE,
                'target' => backup::TARGET_NEW_COURSE,
                'mode' => backup::MODE_GENERAL,
                'operation' => backup::OPERATION_RESTORE,
                'samesite' => true,
                'originalcourseid' => $deleted_course_id,
            ]
        ]);

        course_observer::course_restored($cc_event);
        $this->verify_validity_changed_hook($hook_sink, $data->criteria_ids);

        // Verify that the status changed on disk
        $nvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 1)
            ->count();
        $this->assertEquals(1, $nvalid);

        $ninvalid = criterion_entity::repository()
            ->where('id', $data->criteria_ids)
            ->where('valid', 0)
            ->count();
        $this->assertEquals(0, $ninvalid);

        $hook_sink->close();
    }


    /**
     * @param phpunit_hook_sink $sink
     * @param array $expected_user_criteria_ids
     */
    private function verify_achievement_changed_hook(phpunit_hook_sink $sink, array $expected_user_criteria_ids) {
        $hooks = $sink->get_hooks();
        $this->assertEquals(1, count($hooks));
        /** @var criteria_achievement_changed $hook */
        $hook = reset($hooks);
        $this->assertEquals(criteria_achievement_changed::class, get_class($hook));

        $this->assertEqualsCanonicalizing($expected_user_criteria_ids, $hook->get_user_criteria_ids());
        $sink->clear();
    }

    /**
     * @param phpunit_hook_sink $sink
     * @param array $expected_criteria_ids
     */
    private function verify_validity_changed_hook(phpunit_hook_sink $sink, array $expected_criteria_ids) {
        $hooks = $sink->get_hooks();
        $this->assertEquals(1, count($hooks));
        /** @var criteria_validity_changed $hook */
        $hook = reset($hooks);
        $this->assertEquals(criteria_validity_changed::class, get_class($hook));

        $this->assertEqualsCanonicalizing($expected_criteria_ids, $hook->get_criteria_ids());
        $sink->clear();
    }
}
