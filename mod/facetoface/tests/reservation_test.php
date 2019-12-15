<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

use mod_facetoface\{seminar_event, signup};
use mod_facetoface\signup\state\{booked, requested, requestedadmin, requestedrole, waitlisted};
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests reservation functions
 */
class mod_facetoface_reservation_testcase extends advanced_testcase {
    /**
     * Check that users deallocated correctly
     */
    public function test_facetoface_remove_allocations() {
        $this->resetAfterTest(true);

        $manager = $this->getDataGenerator()->create_user();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $facetoface = $facetofacegenerator->create_instance(array(
            'course' => $course->id,
            'multiplesessions' => 1,
            'managerreserve' => 1,
            'maxmanagerreserves' => 2
        ));
        // Create session.
        $sessiondate = new stdClass();
        $sessiondate->timestart = time() + DAYSECS;
        $sessiondate->timefinish = time() + DAYSECS + 60;
        $sessiondate->sessiontimezone = 'Pacific/Auckland';

        $sessiondata = array(
            'facetoface' => $facetoface->id,
            'capacity' => 5,
            'allowoverbook' => 1,
            'sessiondates' => array($sessiondate),
            'mincapacity' => '1',
        );
        $sessionid = $facetofacegenerator->add_session($sessiondata);
        $sessiondata['datetimeknown'] = '1';
        $seminarevent = new \mod_facetoface\seminar_event($sessionid);
        $seminar = $seminarevent->get_seminar();

        // Allocate to session by manager.
        $this->setUser($manager);
        \mod_facetoface\signup_helper::signup(\mod_facetoface\signup::create($user1->id, $seminarevent));
        \mod_facetoface\signup_helper::signup(\mod_facetoface\signup::create($user2->id, $seminarevent));

        $this->execute_adhoc_tasks();
        $sink = $this->redirectMessages();
        \mod_facetoface\reservations::remove_allocations($seminarevent, $seminar, array($user1->id), true, $manager->id);
        $this->execute_adhoc_tasks();
        $this->assertSame(1, $sink->count());
        $messages = $sink->get_messages();
        $sink->clear();

        $this->assertContains('BOOKING CANCELLED', $messages[0]->fullmessage);
        $this->assertEquals($user1->id, $messages[0]->useridto);

        $sink = $this->redirectMessages();
        \mod_facetoface\reservations::remove_allocations($seminarevent, $seminar, array($user2->id), false, $manager->id);
        $this->execute_adhoc_tasks();
        $this->assertSame(1, $sink->count());
        $messages = $sink->get_messages();
        $sink->clear();
        $this->assertContains('BOOKING CANCELLED', $messages[0]->fullmessage);
        $this->assertEquals($user2->id, $messages[0]->useridto);
    }

    /**
     * @return array
     */
    public function data_closed_registrationtimeset() {
        $now = time();
        $monthsecs = 30 * DAYSECS;
        return [
            [$now + $monthsecs, 0],
            [0, $now - $monthsecs * 3],
            [$now - $monthsecs * 5, $now - $monthsecs * 4],
            [$now + $monthsecs * 6, $now + $monthsecs * 7]
        ];
    }

    /**
     * @param integer $registrationtimestart
     * @param integer $registrationtimefinish
     * @dataProvider data_closed_registrationtimeset
     */
    public function test_make_reservation_when_registration_is_closed($registrationtimestart, $registrationtimefinish) {
        $gen = $this->getDataGenerator();
        $manager = $gen->create_user();
        $staff = $gen->create_user();
        $student1 = $gen->create_user();
        $student2 = $gen->create_user();
        $student3 = $gen->create_user();
        $course = $gen->create_course();

        $managerja = job_assignment::create_default($manager->id);
        job_assignment::create_default($student1->id, ['managerjaid' => $managerja->id]);
        job_assignment::create_default($student2->id, ['managerjaid' => $managerja->id]);
        $staffja = job_assignment::create_default($staff->id);
        job_assignment::create_default($student1->id, ['managerjaid' => $staffja->id]);
        job_assignment::create_default($student2->id, ['managerjaid' => $staffja->id]);

        $gen->enrol_user($student1->id, $course->id);
        $gen->enrol_user($student3->id, $course->id);
        $gen->enrol_user($manager->id, $course->id, 'manager');
        $gen->enrol_user($staff->id, $course->id, 'staffmanager');

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance([
            'course' => $course->id,
            'managerreserve' => 1,
            'maxmanagerreserves' => 2
        ]);
        $sessionid = $f2fgen->add_session([
            'facetoface' => $f2f->id,
            'sessiondates' => [time() + YEARSECS],
            'registrationtimestart' => $registrationtimestart,
            'registrationtimefinish' => $registrationtimefinish
        ]);

        $seminarevent = new seminar_event($sessionid);
        $signup1by1 = signup::create($student1->id, $seminarevent)->set_actorid($student1->id);
        $signup2by2 = signup::create($student2->id, $seminarevent)->set_actorid($student2->id);
        $signup3by3 = signup::create($student3->id, $seminarevent)->set_actorid($student3->id);
        foreach ([booked::class, waitlisted::class, requested::class, requestedrole::class, requestedadmin::class] as $stateclass) {
            $this->setUser($student1);
            $this->assertFalse($signup1by1->can_switch($stateclass), '#1 should not be able to switch to '.$stateclass);
            $this->setUser($student2);
            $this->assertFalse($signup2by2->can_switch($stateclass), '#2 should not be able to switch to '.$stateclass);
            $this->setUser($student3);
            $this->assertFalse($signup3by3->can_switch($stateclass), '#3 should not be able to switch to '.$stateclass);
        }

        $this->setUser($manager);
        $reservebymanager = signup::create(0, $seminarevent)->set_bookedby($manager->id);
        $this->assertTrue($reservebymanager->can_switch(booked::class), 'manager should be able to switch to '.booked::class);
        $this->assertFalse($reservebymanager->can_switch(waitlisted::class), 'manager should not be able to switch to '.waitlisted::class);

        $this->setUser($staff);
        $reservebystaff = signup::create(0, $seminarevent)->set_bookedby($staff->id);
        foreach ([booked::class, waitlisted::class] as $stateclass) {
            $this->assertFalse($reservebystaff->can_switch($stateclass), 'staff should not be able to switch to '.$stateclass);
        }
    }
}
