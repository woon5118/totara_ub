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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

use mod_facetoface\{seminar, seminar_event, seminar_session};

class mod_facetoface_seminar_session_list_testcase extends advanced_testcase {
    /**
     * Create seminar_event with default setting of seminar
     * @return seminar_event
     */
    private function create_seminar_event(): seminar_event {
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $f2f = new seminar();
        $f2f->set_course($course->id);
        $f2f->save();

        $event = new seminar_event();
        $event->set_facetoface($f2f->get_id());
        $event->save();
        return $event;
    }

    /**
     * Provide test data for session's list calculation.
     * @return array
     */
    public function provide_session_data(): array {
        $time = time();
        return [
            [
                [
                    ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                    ['start' => $time + (3600 * 4), 'finish' => $time + (3600 * 5)],
                    ['start' => $time + (3600 * 7), 'finish' => $time + (3600 * 8)],
                ],
                $time,
                [
                    'over' => 0,
                    'upcoming' => 3
                ]
            ],
            [
                [
                    ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                    ['start' => $time + (3600 * 4), 'finish' => $time + (3600 * 5)],
                    ['start' => $time + (3600 * 7), 'finish' => $time + (3600 * 8)],
                ],
                $time + (3600 * 3),
                [
                    'over' => 1,
                    'upcoming' => 2
                ]
            ],
            [
                [
                    ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                    ['start' => $time + (3600 * 4), 'finish' => $time + (3600 * 5)],
                    ['start' => $time + (3600 * 7), 'finish' => $time + (3600 * 8)],
                ],
                $time + (3600 * 10),
                [
                    'over' => 3,
                    'upcoming' => 0
                ]
            ],
            [
                // Waitlisted event
                [],
                $time,
                ['over' => 0, 'upcoming' => 0]
            ]
        ];
    }

    /**
     * Test suite of calculating the number of sessions that either upcoming or over.
     * @dataProvider provide_session_data
     * @param array $times
     * @param int $currenttime
     * @param array $result
     */
    public function test_calculate_session(array $times, int $currenttime, array $result): void {
        $this->resetAfterTest();

        $event = $this->create_seminar_event();
        foreach ($times as $time) {
            $session = new seminar_session();
            $session->set_sessionid($event->get_id());
            $session->set_timestart($time['start']);
            $session->set_timefinish($time['finish']);
            $session->save();
        }

        $sessions = $event->get_sessions();

        // Reload the sessions here, so that we can stub our test data with the custom time.
        $sessions->reload();

        $this->assertEquals($result['over'], $sessions->count_over($currenttime));
        $this->assertEquals($result['upcoming'], $sessions->count_upcoming($currenttime));
    }
}