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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/facetoface/db/upgradelib.php');

/**
 * Test facetoface upgradelib related functions
 */
class mod_facetoface_upgradelib_testcase extends advanced_testcase {
    /**
     * Test facetoface_upgradelib_managerprefix_clarification()
     */
    public function test_facetoface_upgradelib_managerprefix_clarification() {
        global $DB;
        $this->resetAfterTest();

        // Prepate data.
        $tpl_cancellation = $DB->get_record('facetoface_notification_tpl', array('reference' => 'cancellation'));
        $tpl_cancellation->managerprefix = text_to_html(get_string('setting:defaultcancellationinstrmngrdefault', 'facetoface') . "test");
        $DB->update_record('facetoface_notification_tpl', $tpl_cancellation);

        $tpl_reminder = $DB->get_record('facetoface_notification_tpl', array('reference' => 'reminder'));
        $tpl_reminder->managerprefix = text_to_html(get_string('setting:defaultreminderinstrmngrdefault', 'facetoface'));
        $DB->update_record('facetoface_notification_tpl', $tpl_reminder);

        $tpl_request = new stdClass();
        $tpl_request->status = 1;
        $tpl_request->title =  "Test title 3";
        $tpl_request->type =  4;
        $tpl_request->courseid =  1;
        $tpl_request->facetofaceid =  1;
        $tpl_request->courseid =  1;
        $tpl_request->templateid =  1;
        $tpl_request->body = text_to_html(get_string('setting:defaultrequestmessagedefault_v9', 'facetoface'));
        $tpl_request->managerprefix = text_to_html(get_string('setting:defaultrequestinstrmngrdefault', 'facetoface'));
        $DB->insert_record('facetoface_notification', $tpl_request);

        $tpl_rolerequest = new stdClass();
        $tpl_rolerequest->status = 1;
        $tpl_rolerequest->title =  "Test title 4";
        $tpl_rolerequest->type =  4;
        $tpl_rolerequest->courseid =  1;
        $tpl_rolerequest->facetofaceid =  1;
        $tpl_rolerequest->courseid =  1;
        $tpl_rolerequest->templateid =  1;
        $tpl_rolerequest->body = text_to_html(get_string('setting:defaultrolerequestmessagedefault_v9', 'facetoface'));
        $tpl_rolerequest->managerprefix = text_to_html("test".get_string('setting:defaultrolerequestinstrmngrdefault', 'facetoface'));
        $DB->insert_record('facetoface_notification', $tpl_rolerequest);

        // Do upgrade.
        facetoface_upgradelib_managerprefix_clarification();

        // Check that changed strings are not updated.
        $cancellation = $DB->get_field('facetoface_notification_tpl', 'managerprefix', array('reference' => 'cancellation'));
        $cancellationexp = text_to_html(get_string('setting:defaultcancellationinstrmngrdefault', 'facetoface') . "test");
        $this->assertEquals($cancellationexp, $cancellation);

        $rolerequest = $DB->get_field('facetoface_notification', 'managerprefix', array('title' => 'Test title 4'));
        $rolerequestexp = text_to_html("test" . get_string('setting:defaultrolerequestinstrmngrdefault', 'facetoface'));
        $this->assertEquals($rolerequestexp, $rolerequest);

        // Check that not changed string are updated.
        $reminder = $DB->get_field('facetoface_notification_tpl', 'managerprefix', array('reference' => 'reminder'));
        $reminderexp = text_to_html(get_string('setting:defaultreminderinstrmngrdefault_v92', 'facetoface'));
        $this->assertEquals($reminderexp, $reminder);

        $request = $DB->get_field('facetoface_notification', 'managerprefix', array('title' => 'Test title 3'));
        $requestexp = text_to_html(get_string('setting:defaultrequestinstrmngrdefault_v92', 'facetoface'));
        $this->assertEquals($requestexp, $request);
    }
}
