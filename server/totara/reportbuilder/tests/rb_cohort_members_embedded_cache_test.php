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
 * @subpackage reportbuilder
 *
 * Unit/functional tests to check Audience members reports caching
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
global $CFG;
require_once($CFG->dirroot . '/totara/reportbuilder/tests/reportcache_advanced_testcase.php');
require_once($CFG->dirroot . '/totara/cohort/lib.php');
require_once($CFG->dirroot . '/totara/cohort/rules/lib.php');

/**
 * @group totara_reportbuilder
 */
class totara_reportbuilder_rb_cohort_members_embedded_cache_testcase extends reportcache_advanced_testcase {
    // Testcase data
    protected $report_builder_data = array('id' => 4, 'fullname' => 'Audience members', 'shortname' => 'cohort_members',
                                           'source' => 'cohort_members', 'hidden' => 1, 'embedded' => 1);

    protected $report_builder_columns_data = array(
                        array('id' => 18, 'reportid' => 4, 'type' => 'user', 'value' => 'namelink',
                              'heading' => 'A', 'sortorder' => 1),
                        array('id' => 19, 'reportid' => 4, 'type' => 'user', 'value' => 'position',
                              'heading' => 'B', 'sortorder' => 2),
                        array('id' => 20, 'reportid' => 4, 'type' => 'user', 'value' => 'organisation',
                              'heading' => 'C', 'sortorder' => 3));

    protected $report_builder_filters_data = array(
                        array('id' => 9, 'reportid' => 4, 'type' => 'user', 'value' => 'fullname',
                              'sortorder' => 1, 'advanced' => 0));

    // Work data
    protected $users = array();
    protected $cohort1 = null;
    protected $cohort2 = null;
    protected $cohort3 = null;

    protected function tearDown(): void {
        $this->report_builder_data = null;
        $this->report_builder_columns_data = null;
        $this->report_builder_filters_data = null;
        $this->users = null;
        $this->cohort1 = null;
        $this->cohort2 = null;
        $this->cohort3 = null;
        parent::tearDown();
    }

    /**
     * Prepare mock data for testing
     *
     * Common part of all test cases:
     * - Add 8 users(0-7)
     * - Add 1 set cohort
     * - Add 2 dynamic cohort
     * - Add users1,4 to cohort1
     * - Add Users2,3,4 to cohort2 using their firstname
     * - Users6 has the same firstname as users2
     * - Cohort three without members
     *
     */
    protected function setUp(): void {
        parent::setup();
        $this->setAdminUser();
        $this->getDataGenerator()->reset();
        // Common parts of test cases:
        // Create report record in database
        $this->loadDataSet($this->createArrayDataSet(array('report_builder' => array($this->report_builder_data),
                                                           'report_builder_columns' => $this->report_builder_columns_data,
                                                           'report_builder_filters' => $this->report_builder_filters_data)));
        for ($a = 0; $a <= 7; $a++) {
            $data = array('firstname' => 'User'.$a);
            if ($a == 6) {
                $data['firstname'] = 'User2';
            }
            $this->users[] = $this->getDataGenerator()->create_user($data);
        }

        $this->cohort1 = $this->getDataGenerator()->create_cohort(array('cohorttype' => cohort::TYPE_STATIC));
        $this->cohort2 = $this->getDataGenerator()->create_cohort(array('cohorttype' => cohort::TYPE_DYNAMIC));
        $this->cohort3 = $this->getDataGenerator()->create_cohort(array('cohorttype' => cohort::TYPE_DYNAMIC));

        cohort_add_member($this->cohort1->id, $this->users[1]->id);
        cohort_add_member($this->cohort1->id, $this->users[4]->id);


        // create collection
        $rulesetid = cohort_rule_create_ruleset($this->cohort2->draftcollectionid);
        $ruleid = cohort_rule_create_rule($rulesetid, 'user', 'firstname');
        $values = array($this->users[2]->firstname,
                      $this->users[3]->firstname,
                      $this->users[4]->firstname);
        $this->getDataGenerator()->create_cohort_rule_params($ruleid, array('equal' => COHORT_RULES_OP_IN_ISEQUALTO), $values);
        cohort_rules_approve_changes($this->cohort2);
    }

    /**
     * Test courses report
     * Test case:
     * - Common part (@see: self::setUp() )
     * - Check that set group has added members
     * - Check that dynamic group has all members
     *
     * @param int $usecache Use cache or not (1/0)
     * @dataProvider provider_use_cache
     */
    public function test_cohort_members($usecache) {
        $this->resetAfterTest();
        if ($usecache) {
            $this->enable_caching($this->report_builder_data['id']);
        }
        $useridalias = reportbuilder_get_extrafield_alias('user', 'namelink', 'id');
        $result = $this->get_report_result($this->report_builder_data['shortname'],  array('cohortid' => $this->cohort1->id), $usecache);
        $this->assertCount(2, $result);
        $was = array();
        foreach($result as $r) {
            $this->assertContains($r->$useridalias, array($this->users[1]->id, $this->users[4]->id));
            $this->assertNotContains($r->$useridalias, $was);
            $was[] = $r->$useridalias;
        }

        $result = $this->get_report_result($this->report_builder_data['shortname'],  array('cohortid' => $this->cohort2->id), $usecache);
        $this->assertCount(4, $result);
        $was = array();
        $cohort2ids =  array($this->users[2]->id, $this->users[3]->id, $this->users[4]->id, $this->users[6]->id);
        foreach($result as $r) {
            $this->assertContains($r->$useridalias, $cohort2ids);
            $this->assertNotContains($r->$useridalias, $was);
            $was[] = $r->$useridalias;
        }

        $result = $this->get_report_result($this->report_builder_data['shortname'],  array('cohortid' => $this->cohort3->id), $usecache);
        $this->assertCount(0, $result);
    }

    public function test_is_capable() {
        global $DB;
        $this->resetAfterTest();

        // Set up report and embedded object for is_capable checks.
        $syscontext = context_system::instance();
        $shortname = $this->report_builder_data['shortname'];
        $report = reportbuilder::create_embedded($shortname);
        $embeddedobject = $report->embedobj;
        $roleuser = $DB->get_record('role', array('shortname'=>'user'));
        $userid = $this->users[1]->id;

        // Test admin can access report.
        $this->assertTrue($embeddedobject->is_capable(2, $report),
                'admin cannot access report');

        // Test user cannot access report.
        $this->assertFalse($embeddedobject->is_capable($userid, $report),
                'user should not be able to access report');

        // Test user with view capability can access report.
        assign_capability('moodle/cohort:view', CAP_ALLOW, $roleuser->id, $syscontext);
        $this->assertTrue($embeddedobject->is_capable($userid, $report),
                'user with capability moodle/cohort:view cannot access report');
        assign_capability('moodle/cohort:view', CAP_INHERIT, $roleuser->id, $syscontext);
    }
}
