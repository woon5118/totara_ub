<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara
 * @subpackage feedback360
 */

global $CFG;
require_once($CFG->dirroot.'/totara/feedback360/tests/feedback360_testcase.php');

class totara_feedback360_feedback360_testcase extends feedback360_testcase {

    public function test_create() {
        $this->resetAfterTest();
        $fdbck = new feedback360();
        $data = new stdClass();
        $data->name = 'Name';
        $data->description = 'Description';
        $fdbck->set($data);
        $fdbck->save();
        $fdbckid = $fdbck->id;
        unset($fdbck);
        $fdbcktst = new feedback360($fdbckid);
        $datatest = $fdbcktst->get();
        $this->assertEquals('Name', $datatest->name);
        $this->assertEquals('Description', $datatest->description);
    }

    public function test_edit() {
        $this->resetAfterTest();
        $fdbck = new feedback360();
        $data = new stdClass();
        $data->name = 'Name';
        $data->description = 'Description';
        $fdbck->set($data);
        $fdbck->save();
        $fdbckid = $fdbck->id;
        unset($fdbck);
        $fdbckedit = new feedback360($fdbckid);
        $dataedit = new stdClass();
        $dataedit->name = 'Edit';
        $dataedit->description = 'New Description';
        $fdbckedit->set($dataedit);
        $fdbckedit->save();
        unset($fdbckedit);
        $fdbcktst = new feedback360($fdbckid);
        $datatest = $fdbcktst->get();
        $this->assertEquals('Edit', $datatest->name);
        $this->assertEquals('New Description', $datatest->description);
    }

    public function test_delete() {
        $this->resetAfterTest();
        $this->setAdminUser();
        list($fdbck) = $this->prepare_feedback_with_users();
        list($fdbck2) = $this->prepare_feedback_with_users();

        // Check deleting of new feeedback.
        $fdbck->delete();
        $list1 = feedback360::get_manage_list();
        $this->assertCount(1, $list1);
        $this->assertArrayHasKey($fdbck2->id, $list1);
        unset($list1);

        // Check deleting of activated feedback.
        $fdbck2->validate();
        $fdbck2->activate();
        try {
            $fdbck2->delete();
            $this->fail('Exception expected if feedback active');
        } catch (feedback360_exception $e) {
            $this->assertSame('Cannot delete active feedback', $e->getMessage());
        }
        $list2 = feedback360::get_manage_list();
        $this->assertCount(1, $list2);
        $this->assertArrayHasKey($fdbck2->id, $list2);
        unset($list2);

        // Check deleting of de-activated feedback.
        $fdbck2->set_status(feedback360::STATUS_CLOSED);
        $fdbck2->delete();
        $list3 = feedback360::get_manage_list();
        $this->assertCount(0, $list3);
    }

    public function test_activate() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck) = $this->prepare_feedback_with_users();
        $fdbck->validate();
        $this->assertTrue(feedback360::is_draft($fdbck));
        $fdbck->activate();
        $fdbckid = $fdbck->id;
        unset($fdbck);
        $fdbcktest = new feedback360($fdbckid);
        $dbman = $DB->get_manager();

        $this->assertEquals(feedback360::STATUS_ACTIVE, $fdbcktest->status);
        $this->assertFalse(feedback360::is_draft($fdbcktest));

        $this->assertTrue($dbman->table_exists('feedback360_quest_data_'.$fdbcktest->id));
        $assign = new totara_assign_feedback360('feedback360', $fdbcktest);
        $this->assertTrue($assign->assignments_are_stored());
        // The function get_current_users() returns a recordset so need to loop through to count.
        $users = $assign->get_current_users();
        $count = 0;
        foreach ($users as $user) {
            $count++;
        }
        $this->assertEquals(1, $count);
    }

    public function test_validate() {
        $this->resetAfterTest();
        // No questions.
        list($fdbckquest) = $this->prepare_feedback_with_users(array(), 0);

        // No users.
        list($fdbcklrnr) = $this->prepare_feedback_with_users(0);

        // No recipient rights.
        list($fdbckrcpt) = $this->prepare_feedback_with_users();
        $fdbckrcpt->recipients = 0;
        $fdbckrcpt->save();

        // Valid.
        list($fdbckno) = $this->prepare_feedback_with_users();

        $errquest = $fdbckquest->validate();
        $errlrnr = $fdbcklrnr->validate();
        $errrcpt = $fdbckrcpt->validate();
        $errno = $fdbckno->validate();

        // Check questions.
        $this->assertCount(1, $errquest);
        $this->assertArrayHasKey('questions', $errquest);
        unset($errquest);

        // Check learners.
        $this->assertCount(1, $errlrnr);
        $this->assertArrayHasKey('learners', $errlrnr);
        unset($errlrnr);

        // Check learners.
        $this->assertCount(1, $errrcpt);
        $this->assertArrayHasKey('recipients', $errrcpt);
        unset($errrcpt);

        // Check no errors.
        $this->assertEmpty($errno);
    }

    public function test_cancel_requests() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users(2);
        $fdbck->activate();
        $respuser = $this->getDataGenerator()->create_user();
        $userass = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));
        // Create 1 resp assignment.
        feedback360_responder::update_system_assignments(array($respuser->id), array(), current($userass)->id, time());
        // Check that there 2 user assignments and 1 resp assignments.
        $respass = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => current($userass)->id));
        $this->assertCount(2, $userass);
        $this->assertCount(1, $respass);

        $fdbck->close();
        // Check that there no user/resp assignments.
        $respass2 = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => current($userass)->id));
        $userass2 = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        $this->assertNotEmpty($userass2);
        $this->assertNotEmpty($respass2);
    }

    public function test_cancel_resp_assignment() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users();
        $fdbck->activate();
        $respuser = $this->getDataGenerator()->create_user();
        $respuser2 = $this->getDataGenerator()->create_user();
        $respuser3 = $this->getDataGenerator()->create_user();
        $userass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        // Create 3 resp assignments.
        feedback360_responder::update_system_assignments(array($respuser->id, $respuser2->id, $respuser3->id), array(),
                $userass->id, time());
        // Check that there 3 resp assignments.
        $userrespass = $DB->get_record('feedback360_resp_assignment', array('feedback360userassignmentid' => $userass->id,
            'userid' => $respuser->id));
        $user2respass = $DB->get_record('feedback360_resp_assignment', array('feedback360userassignmentid' => $userass->id,
            'userid' => $respuser2->id));
        $user3respass = $DB->get_record('feedback360_resp_assignment', array('feedback360userassignmentid' => $userass->id,
            'userid' => $respuser3->id));

        // Delete response assignment by record.
        $fdbck->cancel_resp_assignment($user2respass);
        $respass2 = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $userass->id));
        $this->assertCount(2, $respass2);
        $this->assertArrayHasKey($user3respass->id, $respass2);
        $this->assertArrayHasKey($userrespass->id, $respass2);
        unset($respass2);

        // Delete response assignment by number.
        $fdbck->cancel_resp_assignment((int)$userrespass->id);
        $respass3 = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $userass->id));
        $this->assertCount(1, $respass3);
        $this->assertArrayHasKey($user3respass->id, $respass3);
    }

    public function test_cancel_user_assignment() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users(2);
        $fdbck->activate();
        $user1 = current($users);
        $user2 = next($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user1->id));
        $user2ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user2->id));
        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(),
                $user1ass->id, time());
        // Check that there two user assignments and one resp assignment.
        $respass = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $user1ass->id));
        $userass = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        $this->assertCount(2, $userass);
        $this->assertCount(1, $respass);

        feedback360::cancel_user_assignment($user1ass->id);

        // Check that there one user assignments and no resp assignments.
        $respass2 = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $user1ass->id));
        $userass2 = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        // Function cancel_user_assignments removes only all response assignments of this user.
        $this->assertCount(2, $userass2);
        $this->assertEmpty($respass2);
    }

    public function test_anon_cancel_user_assignment() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users(2, 1, true);
        $fdbck->activate();
        $user1 = current($users);
        $user2 = next($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user1->id));
        $user2ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user2->id));
        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(),
                $user1ass->id, time());
        // Check that there two user assignments and one resp assignment.
        $respass = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $user1ass->id));
        $userass = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        $this->assertCount(2, $userass);
        $this->assertCount(1, $respass);

        feedback360::cancel_user_assignment($user1ass->id);

        // Check that there one user assignments and no resp assignments.
        $respass2 = $DB->get_records('feedback360_resp_assignment', array('feedback360userassignmentid' => $user1ass->id));
        $userass2 = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        // Function cancel_user_assignments removes only all response assignments of this user.
        $this->assertCount(2, $userass2);
        $this->assertCount(1, $respass2);
    }

    public function test_duplicate() {
        $this->resetAfterTest();
        $this->setAdminUser();
        list($fdbck, $users) = $this->prepare_feedback_with_users();
        $fdbck->activate();
        $newfdbckid = feedback360::duplicate($fdbck->id);
        $newfdbck = new feedback360($newfdbckid);
        // Check name, description, status, assignments.
        $this->assertEquals(feedback360::STATUS_DRAFT, $newfdbck->status);
        $this->assertGreaterThan($fdbck->id, $newfdbck->id);
        $this->assertEquals('Feedback', $newfdbck->name);
        $this->assertEquals('Description', $newfdbck->description);
        $assign = new totara_assign_feedback360('feedback360', $newfdbck);
        $users2 = $assign->get_current_users();
        $assignedusers = array();
        foreach ($users2 as $user2) {
            $assignedusers[$user2->id] = $user2;
        }
        foreach ($users as $user) {
            $this->assertArrayHasKey($user->id, $assignedusers);
        }
    }

    public function test_has_user_assignment() {
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users();
        list($fdbck2) = $this->prepare_feedback_with_users();
        $justuser = $this->getDataGenerator()->create_user();
        $fdbck->activate();
        $fdbck2->activate();
        $this->assertTrue(feedback360::has_user_assignment(current($users)->id, $fdbck->id));
        $this->assertFalse(feedback360::has_user_assignment(current($users)->id, $fdbck2->id));
        $this->assertFalse(feedback360::has_user_assignment($justuser->id, $fdbck->id));
    }

    public function test_get_manage_list() {
        $this->resetAfterTest();
        list($fdbck1) = $this->prepare_feedback_with_users();
        $fdbck1->userid = 2;
        $fdbck1->save();
        list($fdbck2) = $this->prepare_feedback_with_users();
        $fdbck2->userid = 2;
        $fdbck2->save();
        list($fdbck3) = $this->prepare_feedback_with_users();
        // Check getting manage list as not admin.
        $justuser = $this->getDataGenerator()->create_user();
        $this->setUser($justuser);
        try {
            $list1 = feedback360::get_manage_list();
            $this->fail('permission exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }

        // Get list of all feedbacks.
        $this->setAdminUser();
        $list2 = feedback360::get_manage_list();
        $this->assertCount(3, $list2);

        // Get list of one user.
        $list3 = feedback360::get_manage_list(2);
        $this->assertCount(2, $list3);
        $this->assertArrayHasKey($fdbck1->id, $list3);
        $this->assertArrayHasKey($fdbck2->id, $list3);
    }

    public function test_fetch_questions() {
        $this->resetAfterTest();
        list($fdbck1) = $this->prepare_feedback_with_users(array(), 3);
        list($fdbck2) = $this->prepare_feedback_with_users(array(), 0);
        $this->assertCount(3, $fdbck1->fetch_questions());
        $this->assertEmpty($fdbck2->fetch_questions());
    }

    public function test_postupdate_answers() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users, $quests) = $this->prepare_feedback_with_users(1, 2);
        $fdbck->activate();
        $user = current($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user->id));
        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(),
                $user1ass->id, time());
        $response = feedback360_responder::by_user($respuser->id, $fdbck->id, $user->id);

        $formdata = new stdClass();
        $formdata->{'data_'.$quests['Text1']->id.'_'.$response->id} = 'Test1';
        $formdata->{'data_'.$quests['Text2']->id.'_'.$response->id} = 'Test2';

        $dbdata = $fdbck->postupdate_answers($formdata, $response);

        $attr1 = 'data_'.$quests['Text1']->id;
        $attr2 = 'data_'.$quests['Text2']->id;
        $this->assertObjectHasAttribute($attr1, $dbdata);
        $this->assertObjectHasAttribute($attr2, $dbdata);
        $this->assertEquals('Test1', $dbdata->$attr1);
        $this->assertEquals('Test2', $dbdata->$attr2);
    }

    public function test_prepare_answers() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users, $quests) = $this->prepare_feedback_with_users(1, 2);
        $fdbck->activate();
        $user = current($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user->id));
        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(),
                $user1ass->id, time());
        $response = feedback360_responder::by_user($respuser->id, $fdbck->id, $user->id);

        $dbdata = new stdClass();
        $dbdata->{'data_'.$quests['Text1']->id} = 'Test1';
        $dbdata->{'data_'.$quests['Text2']->id} = 'Test2';

        $formdata = $fdbck->prepare_answers($dbdata, $response);

        $attr1 = 'data_'.$quests['Text1']->id.'_'.$response->id;
        $attr2 = 'data_'.$quests['Text2']->id.'_'.$response->id;

        $this->assertObjectHasAttribute($attr1, $formdata);
        $this->assertObjectHasAttribute($attr2, $formdata);
        $this->assertEquals('Test1', $formdata->$attr1);
        $this->assertEquals('Test2', $formdata->$attr2);
    }

    public function test_save_answers() {
        global $DB;
        $this->resetAfterTest();
        list($fdbck, $users, $quests) = $this->prepare_feedback_with_users(1, 2);
        $fdbck->activate();
        $user = current($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user->id));
        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(),
                $user1ass->id, time());
        $response = feedback360_responder::by_user($respuser->id, $fdbck->id, $user->id);

        $field1 = 'data_'.$quests['Text1']->id.'_'.$response->id;
        $field2 = 'data_'.$quests['Text2']->id.'_'.$response->id;
        $formdata = new stdClass();
        $formdata->$field1 = 'Test1';
        $formdata->$field2 = 'Test2';

        $saved = $fdbck->save_answers($formdata, $response);
        $this->assertTrue($saved);

        $fromdb = $fdbck->get_answers($response);
        $this->assertInstanceOf('stdClass', $fromdb);
        $this->assertObjectHasAttribute($field1, $fromdb);
        $this->assertObjectHasAttribute($field1, $fromdb);
        $this->assertEquals('Test1', $fromdb->$field1);
        $this->assertEquals('Test2', $fromdb->$field2);
    }

    public function test_count_completed_answers() {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        list($fdbck, $users, $quests) = $this->prepare_feedback_with_users(1, 2);
        $fdbck->activate();
        $user = current($users);
        $user1ass = $DB->get_record('feedback360_user_assignment', array('feedback360id' => $fdbck->id, 'userid' => $user->id));

        $respuser = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser->id), array(), $user1ass->id, time());
        $response = feedback360_responder::by_user($respuser->id, $fdbck->id, $user->id);

        $respuser2 = $this->getDataGenerator()->create_user();
        feedback360_responder::update_system_assignments(array($respuser2->id), array(), $user1ass->id, time());
        $response2 = feedback360_responder::by_user($respuser2->id, $fdbck->id, $user->id);

        $field1 = 'data_'.$quests['Text1']->id.'_'.$response->id;
        $field2 = 'data_'.$quests['Text2']->id.'_'.$response->id;
        $formdata = new stdClass();
        $formdata->$field1 = 'Test1';
        $formdata->$field2 = 'Test2';

        $field1_2 = 'data_'.$quests['Text1']->id.'_'.$response2->id;
        $field2_2 = 'data_'.$quests['Text2']->id.'_'.$response2->id;
        $formdata_2 = new stdClass();
        $formdata_2->$field1_2 = 'Test1';
        $formdata_2->$field2_2 = 'Test2';

        $saved = $fdbck->save_answers($formdata, $response);
        $this->assertTrue($saved);
        $saved = $fdbck->save_answers($formdata_2, $response2);
        $this->assertTrue($saved);

        $this->assertEquals(0, $fdbck->count_completed_answers());
        $response->complete(time());
        $this->assertEquals(1, $fdbck->count_completed_answers());
        $response2->complete(time());
        $this->assertEquals(2, $fdbck->count_completed_answers());
    }


    public function test_can_view() {
        $this->resetAfterTest();
        list($fdbck, $users) = $this->prepare_feedback_with_users();
        list($fdbck2) = $this->prepare_feedback_with_users();
        $fdbck->activate();
        $fdbck2->activate();

        $user = current($users);
        $user2 = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        // Check that assigned user can view.
        $this->setUser($user);
        $canuser = feedback360::can_view_feedback360s();
        $this->assertTrue($canuser);

        $managerja = \totara_job\job_assignment::create_default($manager->id);
        \totara_job\job_assignment::create_default($user->id, array('managerjaid' => $managerja->id));

        // Check that manager can view responses on staff feedback.
        $this->setAdminUser();
        $canmngr = feedback360::can_view_feedback360s($user->id);
        $this->assertTrue($canmngr);

        // Check that not assigned user cannot view.
        $canother = feedback360::can_view_feedback360s($user2->id);
        $this->assertFalse($canother);
    }

    public function test_cleanup_task() {
        global $DB;
        $this->resetAfterTest();

        // Create feedback360 and activate it.
        list($fdbck, $users) = $this->prepare_feedback_with_users(5);
        $fdbck->activate();

        // Get assignment records.
        $userassignments = $DB->get_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id));

        $sql = "SELECT fra.id
                  FROM {feedback360_resp_assignment} fra
            INNER JOIN {feedback360_user_assignment} fua
                    ON fua.id = fra.feedback360userassignmentid
                 WHERE fua.feedback360id = ? ";

        // Create records for feedback360_resp_assignment and mark users as deleted.
        $deletedusers = array();
        foreach ($userassignments as $userassignment) {
            $sink = $this->redirectMessages();
            feedback360_responder::update_system_assignments(array($userassignment->userid), array(), $userassignment->id, time());
            $sink->close();
            if ($userassignment->userid % 2) {
                $DB->set_field('user', 'deleted', 1, array('id' => $userassignment->userid));
                $deletedusers[$userassignment->userid] = $userassignment->id;
            }
        }

        // Check we have 5 users assigned to the feedback360.
        $this->assertEquals(count($users), count($userassignments));
        $this->assertEquals(count($users), count($DB->get_records_sql($sql, array($fdbck->id))));

        // Call the clean up task.
        $task = new \totara_feedback360\task\cleanup_task();
        $task->execute();

        // Check the clean up task has done its work and deleted users have been removed from the assignments table.
        $currentusers = count($users) - count($deletedusers);
        $this->assertEquals($currentusers, $DB->count_records('feedback360_user_assignment', array('feedback360id' => $fdbck->id)));
        $this->assertEquals($currentusers, count($DB->get_records_sql($sql, array($fdbck->id))));

        foreach ($deletedusers as $userid => $value) {
            $this->assertFalse(feedback360::has_user_assignment($userid, $fdbck->id));
            $params = array('feedback360userassignmentid' => $value);
            $this->assertFalse($DB->record_exists('feedback360_resp_assignment', $params));
        }
    }

    /**
     * Tests feedback360::validate_user_to_assignment_id().
     */
    public function test_validate_user_to_assignment_id() {
        $this->resetAfterTest(true);
        global $DB;

        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();

        $feedback1users = array($user1, $user2);
        $feedback2users = array($user2);
        $feedback3users = array($user3);

        /** @var feedback360 $feedback1*/
        list($feedback1) = $this->prepare_feedback_with_users($feedback1users);
        /** @var feedback360 $feedback2*/
        list($feedback2) = $this->prepare_feedback_with_users($feedback2users);
        /** @var feedback360 $feedback3*/
        list($feedback3) = $this->prepare_feedback_with_users($feedback3users);

        $feedback1->activate();
        $feedback2->activate();
        // $feedback3 will not be activated. Check that hasn't already happened.
        $this->assertEquals(feedback360::STATUS_DRAFT, $DB->get_field('feedback360', 'status', array('id' => $feedback3->id)));

        // Fetching the user assignment ids.
        $user1feedback1 = $DB->get_field('feedback360_user_assignment', 'id',
            array('feedback360id' => $feedback1->id, 'userid' => $user1->id));
        $user2feedback1 = $DB->get_field('feedback360_user_assignment', 'id',
            array('feedback360id' => $feedback1->id, 'userid' => $user2->id));
        $user2feedback2 = $DB->get_field('feedback360_user_assignment', 'id',
            array('feedback360id' => $feedback2->id, 'userid' => $user2->id));
        $user3feedback3 = $DB->get_field('feedback360_user_assignment', 'id',
            array('feedback360id' => $feedback3->id, 'userid' => $user3->id));

        // Checking feedback1 assigments.
        $this->assertEquals(true, feedback360::validate_user_to_assignment_id($user1->id, $user1feedback1));
        $this->assertEquals(true, feedback360::validate_user_to_assignment_id($user2->id, $user2feedback1));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user2->id, $user1feedback1));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user4->id, $user1feedback1));

        // Checking feedback2 assignments.
        $this->assertEquals(true, feedback360::validate_user_to_assignment_id($user2->id, $user2feedback2));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user1->id, $user2feedback2));

        // Checking feedback 3 assignments. Even user3's assignment should return false since it's not active.
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user3->id, $user3feedback3));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user1->id, $user3feedback3));

        $feedback1->close();

        // Running feedback1 checks again. All should now be false since it's not active.
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user1->id, $user1feedback1));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user2->id, $user2feedback1));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user2->id, $user1feedback1));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user4->id, $user1feedback1));

        // But feedback2 should not have been affected.
        $this->assertEquals(true, feedback360::validate_user_to_assignment_id($user2->id, $user2feedback2));
        $this->assertEquals(false, feedback360::validate_user_to_assignment_id($user1->id, $user2feedback2));
    }
}
