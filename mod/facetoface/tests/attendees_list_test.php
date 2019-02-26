<?php
/*
 * This file is part of Totara LMS
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

use mod_facetoface\{seminar_event, signup, seminar_session};
use mod_facetoface\signup\state\{booked, attendance_state, not_set};

class mod_facetoface_attendees_list_testcase extends advanced_testcase {
    /**
     * @return seminar_event
     */
    private function create_seminar_event(): seminar_event {
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);

        $e = new seminar_event();
        $e->set_facetoface($f2f->id);
        $e->save();

        return $e;
    }

    /**
     * A test suite that assuring the deleted users should not be included in embedded report builder for event's attendees.
     * And within this test, the actor that viewing the embedded report is an editing trainer, and this user does not have the
     * permission to view the deleted user.
     *
     * @return void
     */
    public function test_attendee_list_should_not_include_deleted_user(): void {
        global $DB, $PAGE;
        $PAGE->set_url('/');

        $this->resetAfterTest();
        $this->setAdminUser();

        $e = $this->create_seminar_event();
        $s = new seminar_session();
        $s->set_sessionid($e->get_id());
        $s->set_timestart(time() - 7200);
        $s->set_timefinish(time() - 3600);
        $s->save();

        $gen = $this->getDataGenerator();
        $current = null;
        for ($i = 0; $i < 5; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $e->get_seminar()->get_course());

            $signup = signup::create($user->id, $e);
            $signup->save();

            $signup->switch_state(booked::class);

            if (null == $current) {
                // We just need one user here, so that we can start deleting this user later on within this test suite.
                $current = $user;
            }
        }

        delete_user($current);

        $trainer = $gen->create_user();
        $gen->enrol_user($trainer->id, $e->get_seminar()->get_course(), 'editingteacher');
        $this->setUser($trainer);

        $cfg = new rb_config();
        $cfg->set_reportfor($trainer->id);
        $cfg->set_embeddata(
            [
                'sessionid' => $e->get_id(),
                'status' => attendance_state::get_all_attendance_code_with([booked::class, not_set::class]),
            ]
        );

        $record = $DB->get_record('report_builder', ['shortname' => 'facetoface_sessions']);
        $cfg->set_global_restriction_set(is_object($record) ? $record : null);
        $report = reportbuilder::create_embedded('facetoface_sessions', $cfg);

        /** @var totara_reportbuilder_renderer $output */
        $output = $PAGE->get_renderer('totara_reportbuilder');
        [$reporthtml, $debughtml] = $output->report_html($report, 0);

        // As this $current user had been deleted earlier, therefore, we are expecting this user to not be included in the
        // embedded report builder.
        $name = fullname($current);
        $this->assertNotContains($name, $reporthtml);
    }

    /**
     *
     * @return void
     */
    public function test_attendee_list_to_include_deleted_user(): void {
        global $DB, $PAGE, $USER;
        $PAGE->set_url('/');

        $this->resetAfterTest();
        $this->setAdminUser();

        $e = $this->create_seminar_event();
        $s = new seminar_session();
        $s->set_sessionid($e->get_id());
        $s->set_timestart(time() - 7200);
        $s->set_timefinish(time() - 3600);
        $s->save();

        $gen = $this->getDataGenerator();

        // The user's record to be deleted here
        $current = null;

        for ($i = 0; $i < 5; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $e->get_seminar()->get_course());

            $signup = signup::create($user->id, $e);
            $signup->save();

            $signup->switch_state(booked::class);

            if (null == $current) {
                $current = $user;
            }
        }

        // Start preparing the new permission for the role editingteacher. So that our trainer is able to see the deleted users
        // within this test here.
        $role = $DB->get_record('role', ['shortname' => 'editingteacher']);

        $cm = get_coursemodule_from_instance('facetoface', $e->get_facetoface(), $e->get_seminar()->get_course());
        $ctx = context_module::instance($cm->id);
        $PAGE->set_context($ctx);

        $cap = new stdClass();
        $cap->contextid = $ctx->id;
        $cap->roleid = $role->id;
        $cap->capability = 'totara/core:seedeletedusers';
        $cap->permission = 1;
        $cap->timemodified = time();
        $cap->modifierid = $USER->id;

        $DB->insert_record('role_capabilities', $cap);

        delete_user($current);
        $trainer = $gen->create_user();
        $gen->enrol_user($trainer->id, $e->get_seminar()->get_course(), 'editingteacher');
        $this->setUser($trainer);

        $cfg = new rb_config();
        $cfg->set_reportfor($trainer->id);
        $cfg->set_embeddata(
            [
                'sessionid' => $e->get_id(),
                'status' => attendance_state::get_all_attendance_code_with([booked::class, not_set::class]),
            ]
        );

        $record = $DB->get_record('report_builder', ['shortname' => 'facetoface_sessions']);
        $cfg->set_global_restriction_set(is_object($record) ? $record : null);
        $report = reportbuilder::create_embedded('facetoface_sessions', $cfg);

        /** @var totara_reportbuilder_renderer $output */
        $output = $PAGE->get_renderer('totara_reportbuilder');
        [$reporthtml, $debughtml] = $output->report_html($report, 0);

        $name = fullname($current);
        $this->assertContains($name, $reporthtml);
    }
}