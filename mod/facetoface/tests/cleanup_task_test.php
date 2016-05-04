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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package mod_facetoface
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

global $CFG;
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

class mod_facetoface_cleanup_task_testcase extends advanced_testcase {

    /**
     * Tests the Cleanup Task for Face-to-face.
     *
     * This task does two things, it cancels any user sessions for suspended, deleted users.
     * It also cleans up any unused custom rooms older than a set period.
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function test_cleanup_task() {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/lib.php');

        $this->resetAfterTest(true);

        $time = time();
        $day = 86400;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id, $studentrole->id);

        /** @var totara_hierarchy_generator $hierarchygenerator */
        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $hierarchygenerator->assign_primary_position($user2->id, $user1->id, null, null);
        $hierarchygenerator->assign_primary_position($user3->id, $user1->id, null, null);
        $hierarchygenerator->assign_primary_position($user4->id, $user1->id, null, null);

        /** @var mod_facetoface_generator $facetofacegenerator */
        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');

        $facetoface = $facetofacegenerator->create_instance(['course' => $course1->id, 'usercalentry' => false]);
        $room1 = $facetofacegenerator->add_custom_room(['timecreated' => $time - ($day * 1.1)]);
        $room2 = $facetofacegenerator->add_custom_room(['timecreated' => $time - ($day * 1.1)]);
        $sessionid = $facetofacegenerator->add_session([
            'facetoface' => $facetoface->id,
            'sessiondates' => [
                (object)[
                    'timestart' => $time + ($day / 24) * 36,
                    'timefinish' => $time + ($day / 24) * 38,
                    'sessiontimezone' => 'Pacific/Auckland',
                    'roomid' => $room2->id
                ]
            ]
        ]);
        $session = facetoface_get_session($sessionid);

        $sink = $this->redirectMessages();
        facetoface_user_signup($session, $facetoface, $course1, 'discountcode1', MDL_F2F_TEXT, MDL_F2F_STATUS_BOOKED, $user2->id, false, $user1);
        facetoface_user_signup($session, $facetoface, $course1, 'discountcode1', MDL_F2F_TEXT, MDL_F2F_STATUS_BOOKED, $user3->id, false, $user1);
        facetoface_user_signup($session, $facetoface, $course1, 'discountcode1', MDL_F2F_TEXT, MDL_F2F_STATUS_BOOKED, $user4->id, false, $user1);
        $this->assertSame(3, $sink->count());
        $sink->clear();

        $this->assertCount(3, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_BOOKED));
        $this->assertCount(0, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_USER_CANCELLED));

        // Suspend user 3.
        $user3 = $DB->get_record('user', array('id'=>$user3->id, 'deleted'=>0), '*', MUST_EXIST);
        $user3->suspended = 1;
        user_update_user($user3, false);

        // Delete user 4.
        delete_user($user4);
        // Why you may ask do we get this?
        // Because there are two events triggered here that lead to the user being cancelled.
        // 1. The user unenrolled event triggers cancellation.
        //    delete_user does this mid-way through.
        // 2. The user deleted event triggers cancellation.
        //    delete_user triggers this at the end of the function.
        $this->assertDebuggingCalled('User status already changed to cancelled.');

        // Check that both rooms still exist.
        $rooms = $DB->get_records('facetoface_room');
        $this->assertCount(2, $rooms);
        $this->assertArrayHasKey($room1->id, $rooms);
        $this->assertArrayHasKey($room2->id, $rooms);

        // The deleted user will be automatically updated but the suspended user won't.
        $this->assertCount(2, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_BOOKED));
        $this->assertCount(1, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_USER_CANCELLED));

        // Run the cleanup task.
        $task = new \mod_facetoface\task\cleanup_task();
        $task->execute();

        // Oh what they hell it happens again? why this time?
        // @todo TL-8983 Bug!
        // This one is a real bug, the cleanup task attempts to cancel all suspended/deleted users every time.
        // even if they have previously been cancelled.
        // We will test this again at the end.
        $this->assertDebuggingCalled('User status already changed to cancelled.');


        // We should not have updated statuses.
        $this->assertCount(1, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_BOOKED));
        $this->assertCount(2, facetoface_get_users_by_status($session->id, MDL_F2F_STATUS_USER_CANCELLED));

        // Check that room1 has been deleted.
        $rooms = $DB->get_records('facetoface_room');
        $this->assertCount(1, $rooms);
        $this->assertArrayNotHasKey($room1->id, $rooms);
        $this->assertArrayHasKey($room2->id, $rooms);

        $sink->close();

        // Run the cleanup task.
        $task = new \mod_facetoface\task\cleanup_task();
        $task->execute();

        // @todo TL-8983: Yip this bug is still present, hoorah.
        $messages = $this->getDebuggingMessages();
        $this->resetDebugging();
        $this->assertCount(2, $messages);
        foreach ($messages as $message) {
            $this->assertSame('User status already changed to cancelled.', $message->message);
        }
    }

}