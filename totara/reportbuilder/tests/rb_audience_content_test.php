<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package totara_reportbuilder
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @group totara_reportbuilder
 */
class totara_rb_audience_restrictions_testcase extends advanced_testcase {

    use totara_reportbuilder\phpunit\report_testing;

    private $report   = null;
    private $users    = null;
    private $audience = null;
    private $reportid = null;

    protected function setUp() {
        parent::setup();
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->users = array();
        $this->audience = array();
        $this->report = null;
        $this->reportid = null;
    }

    protected function tearDown() {
        $this->report = null;
        $this->users = null;
        $this->audience = null;
        $this->reportid = null;
        parent::tearDown();
    }

    public function test_report_without_audience_restriction() {
        global $DB;
        $this->setup_report_data();

        $this->report = new reportbuilder($this->reportid, null, false, null, null, true);
        $this->add_column($this->report, 'user', 'id', null, null, null, 0);
        $this->add_column($this->report, 'user', 'firstname', null, null, null, 0);

        list($sql, $params,) = $this->report->build_query();
        $records = $DB->get_records_sql($sql, $params);
        foreach ($this->users as $user) {
            $this->assertArrayHasKey($user->id, $records);
        }
    }

    public function test_report_with_audience_restriction() {
        global $DB;
        $this->setup_report_data();

        // update settings to audience 001
        reportbuilder::update_setting($this->reportid, 'audience_content', 'enable', 1);
        reportbuilder::update_setting($this->reportid, 'audience_content', 'audience', $this->audience[0]->id);
        $this->report->contentmode = REPORT_BUILDER_CONTENT_MODE_ALL;

        list($sql, $params,) = $this->report->build_query();
        $records = $DB->get_records_sql($sql, $params);

        $this->assertArrayHasKey($this->users[0]->id, $records);
        $this->assertArrayHasKey($this->users[1]->id, $records);
        $this->assertArrayNotHasKey($this->users[2]->id, $records);
        $this->assertArrayNotHasKey($this->users[3]->id, $records);
        $this->assertArrayNotHasKey($this->users[4]->id, $records);

        // update settings to audience 002
        reportbuilder::update_setting($this->reportid, 'audience_content', 'audience', $this->audience[1]->id);
        list($sql, $params,) = $this->report->build_query();
        $records = $DB->get_records_sql($sql, $params);

        $this->assertArrayNotHasKey($this->users[0]->id, $records);
        $this->assertArrayNotHasKey($this->users[1]->id, $records);
        $this->assertArrayHasKey($this->users[2]->id, $records);
        $this->assertArrayHasKey($this->users[3]->id, $records);
        $this->assertArrayNotHasKey($this->users[4]->id, $records);
    }

    public function test_global_audience_setting_when_creating_user_report() {
        global $DB;
        $this->setup_report_data();
        set_config('userrestrictaudience', $this->audience[1]->id, 'totara_reportbuilder');

        // Create the report and trigger the event
        $this->reportid = $this->create_report('user', 'Test User Restriction Report');
        \totara_reportbuilder\event\report_created::create_from_report(new reportbuilder($this->reportid), false)->trigger();

        // Check default audience restrictions
        $settings = reportbuilder::get_all_settings($this->reportid, 'audience_content');
        $this->assertSame($this->audience[1]->id, $settings['audience']);
        $this->assertSame(1, (int)$settings['enable']);

        // Check report data
        $report = new reportbuilder($this->reportid); // Init report with new settings
        list($sql, $params,) = $report->build_query();
        $records = $DB->get_records_sql($sql, $params);

        $this->assertArrayNotHasKey($this->users[0]->id, $records);
        $this->assertArrayNotHasKey($this->users[1]->id, $records);
        $this->assertArrayHasKey($this->users[2]->id, $records);
        $this->assertArrayHasKey($this->users[3]->id, $records);
        $this->assertArrayNotHasKey($this->users[4]->id, $records);
    }

    private function setup_report_data() {
        $this->users[] = $this->getDataGenerator()->create_user(['firstname' => 'Cherise', 'lastname' => 'Staten']);
        $this->users[] = $this->getDataGenerator()->create_user(['firstname' => 'Sidney', 'lastname' => 'Goguen']);
        $this->users[] = $this->getDataGenerator()->create_user(['firstname' => 'Darci', 'lastname' => 'Kocsis']);
        $this->users[] = $this->getDataGenerator()->create_user(['firstname' => 'Crissy', 'lastname' => 'Bertolino']);
        $this->users[] = $this->getDataGenerator()->create_user(['firstname' => 'Seth', 'lastname' => 'Rabon']);
        $this->audience[] = $this->getDataGenerator()->create_cohort(['name' => 'aud001']);
        $this->audience[] = $this->getDataGenerator()->create_cohort(['name' => 'aud002']);
        cohort_add_member($this->audience[0]->id, $this->users[0]->id);
        cohort_add_member($this->audience[0]->id, $this->users[1]->id);
        cohort_add_member($this->audience[1]->id, $this->users[2]->id);
        cohort_add_member($this->audience[1]->id, $this->users[3]->id);

        $this->reportid = $this->create_report('user', 'Test User Report');
        $this->report = new reportbuilder($this->reportid, null, false, null, null, true);
        $this->add_column($this->report, 'user', 'id', null, null, null, 0);
        $this->add_column($this->report, 'user', 'firstname', null, null, null, 0);
    }
}
