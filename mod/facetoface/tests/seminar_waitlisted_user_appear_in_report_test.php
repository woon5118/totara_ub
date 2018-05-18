<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
global $CFG;
require_once ($CFG->dirroot . "/mod/facetoface/lib.php");

/**
 * Unit test of assuring the users that are waitlisted
 * in an event of a seminar would still appear in
 * the report querying
 *
 * Class seminar_signup_user_test
 */
class seminar_waitlisted_user_appear_in_report_test extends advanced_testcase
{
    private $source = "facetoface_sessions";

    /**
     * The array of value that required
     * for column options
     * @var array
     */
    private $columnsrequired = array(
        'sessiondate',
        'namelink',
        'courselink',
        'statuscode'
    );

    /**
     * @param stdClass $user
     * @return reportbuilder
     */
    private function create_facetoface_session_report(stdClass $user): reportbuilder {
        global $DB;

        $data = [
            'fullname' => "Seminar Sign-ups test",
            'shortname' => "short",
            'source' => $this->source,
            'hidden' => 0,
            'cache' => 0,
            'accessmode' => 1,
            'contentmode' => 0,
            'description' => 'This is the report',
            'globalrestriction' => 0,
            'timemodified' => time(),
        ];

        $id = $DB->insert_record("report_builder", (object)$data, true);

        $data['id'] = $id;
        $reportdata = (object)$data;
        $this->set_up_columns((object)$reportdata);

        $config = new rb_config();
        $config->set_reportfor($user->id);
        return reportbuilder::create($id, $config);
    }

    /**
     * @param stdClass $report
     */
    private function set_up_columns(stdClass $report): void {
        global $DB;

        /** @var rb_source_facetoface_sessions $source */
        $source = reportbuilder::get_source_object($report->source);
        $columnoptions = $source->columnoptions;
        $sortorder = 1;

        /** @var rb_column_option $columnoption */
        foreach ($columnoptions as $columnoption) {
            if (in_array($columnoption->value, $this->columnsrequired, false)) {
                $DB->insert_record("report_builder_columns", (object)[
                    'reportid' => $report->id,
                    'type' => $columnoption->type,
                    'value' => $columnoption->value,
                    'sortorder' => $sortorder,
                    'hidden' => 0,
                    'customheading' => 0
                ]);

                $sortorder += 1;
            }
        }
    }

    /**
     * Create user (1)
     * Create user (2)
     * Create course
     * Create Seminar
     * Create Seminar's event (facetoface_session)
     * Add user (1 and 2) to seminar's event
     *
     * @param stdClass $user
     */
    private function generate_data(stdClass $user): void {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user([
            'firstname' => "kian",
            'lastname' => "nguyen"
        ]);

        $user2 = $this->getDataGenerator()->create_user([
            'firstname' => "james",
            'lastname' => "lebron"
        ]);

        $course = $this->getDataGenerator()->create_course();
        $time = time();

        $seminarid = $DB->insert_record("facetoface", (object)[
            'course' => $course->id,
            'name' => "Seminar_name",
            'timecreated' => $time,
            'timemodified' => $time
        ]);

        $sessionid = $DB->insert_record("facetoface_sessions", (object)[
            'facetoface' => $seminarid,
            'capacity' => 10,
            'timecreated' => $time,
            'timemodified' => $time,
            'usermodified' => $user->id,
        ]);

        $users = array($user1, $user2);
        foreach ($users as $normaluser) {
            $DB->insert_record("facetoface_signups", (object)[
                'sessionid' => $sessionid,
                'userid' => $normaluser->id,
                'notificationtype' => MDL_F2F_BOTH,
                'bookedby' => $user->id
            ]);
        }
    }

    /**
     * @param reportbuilder $reportbuilder
     * @return counted_recordset
     */
    private function query_records(reportbuilder $reportbuilder): counted_recordset {
        list ($sql, $params, $cache) = $reportbuilder->build_query(false, true);

        $refClass = new ReflectionClass($reportbuilder);
        $method = $refClass->getMethod("get_counted_recordset_sql");
        $method->setAccessible(true);
        $recordset = $method->invokeArgs($reportbuilder, [$sql, $params, 0, 100, true]);

        return $recordset;
    }

    /**
     * Test suite of report builder assuring that the number of
     * wait-listed users appearing in the counted_recordset instance
     */
    public function test_number_of_waitlist_user(): void {
        global $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->generate_data($USER);
        $reportbuilder = $this->create_facetoface_session_report($USER);

        $recordset = $this->query_records($reportbuilder);
        $this->assertEquals(2, $recordset->get_count_without_limits());
    }

    /**
     * The test suite to assure that the record set
     * includes the users that are wait-listed in an
     * seminar event.
     */
    public function test_waitlist_user_in_records(): void {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->generate_data($USER);
        $reportbuilder = $this->create_facetoface_session_report($USER);

        $recordset = $this->query_records($reportbuilder);
        $expected = array("kian nguyen", "james lebron");

        foreach ($recordset as $record) {
            $this->assertContains((string) @$record->user_namelink, $expected);
        }

    }
}