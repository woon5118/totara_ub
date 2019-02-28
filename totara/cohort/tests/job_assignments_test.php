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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package totara_cohort
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/cohort/lib.php');
require_once($CFG->libdir . '/testing/generator/lib.php');
require_once($CFG->dirroot . '/totara/cohort/tests/position_rules_test.php');

class totara_cohort_job_assignments_testcase extends totara_cohort_position_rules_testcase {

    /**
     * Data provider for the reports to rule.
     */
    public function data_reportsto() {
        $data = array(
            array(array('equal' => 0), array(0), 23), // equal = COHORT_RULES_OP_NONE
            array(array('equal' => 1), array(1), 2),  // equal = COHORT_RULES_OP_MIN
            array(array('equal' => 20), array(4), 2), // equal = COHORT_RULES_OP_MAX
            array(array('equal' => 30), array(3), 2), // equal = COHORT_RULES_OP_EXACT
            array(array('equal' => 30), array(2), 0), // equal = COHORT_RULES_OP_EXACT
        );
        return $data;
    }

    /**
     * Evaluates if the user is a manager.
     * Has direct reports = is_manager.
     *
     * manager1
     *    |-------> user1, user3, user5.
     *
     * manager2
     *    |-------> user2, user4.
     *
     * @dataProvider data_reportsto
     */
    public function test_has_direct_reports($params, $listofvalues, $usercount) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create some manager accounts.
        $manager1 = $this->getDataGenerator()->create_user(array('username' => 'manager1'));
        $manager2 = $this->getDataGenerator()->create_user(array('username' => 'manager2'));

        // Assign managers to users.
        $manager1ja = \totara_job\job_assignment::create_default($manager1->id);
        $manager2ja = \totara_job\job_assignment::create_default($manager2->id);
        \totara_job\job_assignment::create([
            'userid' => $this->user1->id,
            'fullname' => 'user1',
            'shortname' => 'user1',
            'idnumber' => 'id1',
            'managerjaid' => $manager1ja->id,
        ]);
        \totara_job\job_assignment::create([
            'userid' => $this->user1->id,
            'fullname' => 'user1',
            'shortname' => 'user1',
            'idnumber' => 'id2',
            'managerjaid' => $manager2ja->id,
        ]);
        \totara_job\job_assignment::get_first($this->user2->id)->update(array('managerjaid' => $manager2ja->id));
        \totara_job\job_assignment::get_first($this->user3->id)->update(array('managerjaid' => $manager1ja->id));
        \totara_job\job_assignment::get_first($this->user4->id)->update(array('managerjaid' => $manager2ja->id));
        \totara_job\job_assignment::get_first($this->user5->id)->update(array('managerjaid' => $manager1ja->id));
        \totara_job\job_assignment::create([
            'userid' => $this->user6->id,
            'fullname' => 'user6',
            'shortname' => 'user6',
            'idnumber' => 'id6',
        ]);

        // Exclude admin user from this cohort.
        $this->cohort_generator->create_cohort_rule_params($this->ruleset, 'user', 'username', array('equal' => COHORT_RULES_OP_IN_NOTEQUALTO), array('admin'));

        // Create a rule.
        $this->cohort_generator->create_cohort_rule_params($this->ruleset, 'alljobassign', 'hasdirectreports', $params, $listofvalues);
        cohort_rules_approve_changes($this->cohort);

        // It should match:
        // 1. data2: 23 users that no are managers.
        // 2. data1: 2 users that are assigned as managers and have "greater than or equal to" 1 learner/s
        // 3. data3: 2 users that are assigned as managers and have "less than or equal to" 4 learner/s
        // 4. data4: 2 users that are assigned as managers and have "equal to" 3 learner/s
        // 5. data5: 0 users that are assigned as managers and have "equal to" 2 learner/s
        $this->assertEquals($usercount, $DB->count_records('cohort_members', array('cohortid' => $this->cohort->id)));
    }

    /**
     * Evaluates if the user is a tempmanager.
     * Has temporary reports = is_manager.
     *
     * tempmanager1
     *    |-------> user1, user3, user5.
     *
     * tempmanager2
     *    |-------> user2, user4.
     *
     * @dataProvider data_reportsto
     */
    public function test_has_temporary_reports($params, $listofvalues, $usercount) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create some manager accounts.
        $manager1 = $this->getDataGenerator()->create_user(array('username' => 'manager1'));
        $manager2 = $this->getDataGenerator()->create_user(array('username' => 'manager2'));

        // Assign managers to users.
        $expirydate = time() + WEEKSECS;
        $manager1ja = \totara_job\job_assignment::create_default($manager1->id);
        $manager2ja = \totara_job\job_assignment::create_default($manager2->id);
        \totara_job\job_assignment::create([
            'userid' => $this->user1->id,
            'fullname' => 'user1',
            'shortname' => 'user1',
            'idnumber' => 'id1',
            'tempmanagerjaid' => $manager1ja->id,
            'tempmanagerexpirydate' => $expirydate
        ]);
        \totara_job\job_assignment::create([
            'userid' => $this->user1->id,
            'fullname' => 'user1',
            'shortname' => 'user1',
            'idnumber' => 'id2',
            'tempmanagerjaid' => $manager2ja->id,
            'tempmanagerexpirydate' => $expirydate
        ]);
        \totara_job\job_assignment::get_first($this->user2->id)->update(array('tempmanagerjaid' => $manager2ja->id, 'tempmanagerexpirydate' => $expirydate));
        \totara_job\job_assignment::get_first($this->user3->id)->update(array('tempmanagerjaid' => $manager1ja->id, 'tempmanagerexpirydate' => $expirydate));
        \totara_job\job_assignment::get_first($this->user4->id)->update(array('tempmanagerjaid' => $manager2ja->id, 'tempmanagerexpirydate' => $expirydate));
        \totara_job\job_assignment::get_first($this->user5->id)->update(array('tempmanagerjaid' => $manager1ja->id, 'tempmanagerexpirydate' => $expirydate));
        \totara_job\job_assignment::create([
            'userid' => $this->user6->id,
            'fullname' => 'user6',
            'shortname' => 'user6',
            'idnumber' => 'id6',
        ]);

        // Exclude admin user from this cohort.
        $this->cohort_generator->create_cohort_rule_params($this->ruleset, 'user', 'username', array('equal' => COHORT_RULES_OP_IN_NOTEQUALTO), array('admin'));

        // Create a rule.
        $this->cohort_generator->create_cohort_rule_params($this->ruleset, 'alljobassign', 'hastemporaryreports', $params, $listofvalues);
        cohort_rules_approve_changes($this->cohort);

        // It should match:
        // 1. data2: 23 users that no are temporary managers.
        // 2. data1: 2 users that are assigned as managers and have "greater than or equal to" 1 learner/s
        // 3. data3: 2 users that are assigned as managers and have "less than or equal to" 4 learner/s
        // 4. data4: 2 users that are assigned as managers and have "equal to" 3 learner/s
        // 5. data5: 0 users that are assigned as managers and have "equal to" 2 learner/s
        $this->assertEquals($usercount, $DB->count_records('cohort_members', array('cohortid' => $this->cohort->id)));
    }
}