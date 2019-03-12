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
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package totara_cohort
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/cohort/tests/generator/totara_cohort_generator.class.php');
require_once($CFG->dirroot . '/totara/cohort/lib.php');

// Make constants available.
require_once($CFG->dirroot . '/totara/cohort/classes/rules/ui/none_min_max_exactly.php');
use \totara_cohort\rules\ui\none_min_max_exactly as ui;


class totara_cohort_indirect_reports_testcase extends advanced_testcase {

    private $users = [];
    private $cohort = null;
    private $ruleset = 0;
    private $cohort_generator = null;

    const TEST_COUNT_USERS = 20;

    protected function tearDown() {
        $this->users = null;
        $this->cohort = null;
        $this->ruleset = null;
        $this->cohort_generator = null;
        parent::tearDown();
    }

    public function setUp() {
        parent::setup();
        $this->resetAfterTest(true);

        // Set totara_cohort generator.
        $this->cohort_generator = $this->getDataGenerator()->get_plugin_generator('totara_cohort');
    }


    /**
    Test data: Job assignment hierarchies:
    ------------------------------------

     * Tree.
    Usr3 (manager: user id 3 == root)
     |
     |
     |---Usr4
     |    |
     |    |---Usr6
     |    |
     |    |---Usr7
     |         |__________________
     |         |        |        |
     |       Usr8      Usr9     Usr10
     |
     |
     |---Usr5
          |
          |---Usr11
               |
               |
               |
              Usr12
               |
               |
           ____|____
           |       |
          Usr13   Usr14


     * Sapling.
    Usr11
     |_____
          |
         Usr15
          |______
                |
               Usr16

    */

    /**
     * Evaluates if the user has indirect reports according to various filtering options.
     *
     * @dataProvider data_indirect_reports
     */
    public function test_indirect_reports_rules($params, $listofvalues, $cohortmembers) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create users.
        for ($i = 3; $i < self::TEST_COUNT_USERS; $i++) {
            $this->users[$i] = $this->getDataGenerator()->create_user();
        }
        $this->assertEquals((self::TEST_COUNT_USERS - 3), count($this->users));

        // Create job assignments and assign managers to users in the tree formation given above (user id points to manager user id).
        $trees = [];
        $trees[] = [
            // Root.
            $this->users[3]->id => 0,
            // Branch heads.
            $this->users[4]->id => $this->users[3]->id,
            $this->users[5]->id => $this->users[3]->id,
            // Left branch.
            $this->users[6]->id => $this->users[4]->id,
            $this->users[7]->id => $this->users[4]->id,
            $this->users[8]->id => $this->users[7]->id,
            $this->users[9]->id => $this->users[7]->id,
            $this->users[10]->id => $this->users[7]->id,
            // Right branch.
            $this->users[11]->id => $this->users[5]->id,
            $this->users[12]->id => $this->users[11]->id,
            $this->users[13]->id => $this->users[12]->id,
            $this->users[14]->id => $this->users[12]->id,
        ];

        // Additional straight-line job assignment hierarchy.
        $trees[] = [
            $this->users[11]->id => 0,
            $this->users[15]->id => $this->users[11]->id,
            $this->users[16]->id => $this->users[15]->id,
        ];

        // Construct the hierarchies in job assignments table.
        $jaids = [];
        foreach ($trees as $index => $tree) {
            $jaids[$index] = [];
            foreach ($tree as $userid => $managerid) {
                // Create job assignment for user.
                $suffix = (string) $index . (string) $userid;
                $userja = \totara_job\job_assignment::create([
                    'userid' => $userid,
                    'fullname' => 'user' . $suffix,
                    'shortname' => 'user' . $suffix,
                    'idnumber' => 'id' . $suffix,
                    'managerjaid' => null,
                ]);

                // Store job assignment id for easy retrieval.
                $jaids[$index][$userid] = (int) $userja->id;

                // Set users manager as needed.
                if ($managerid > 0) {
                    \totara_job\job_assignment::get_with_id($jaids[$index][$userid])->update(['managerjaid' => $jaids[$index][$managerid]]);
                }
            }
        }
        $jaids = null;

        // Create and apply indirect reports rule using test data parameters.
        $this->cohort = $this->cohort_generator->create_cohort(['cohorttype' => cohort::TYPE_DYNAMIC]);
        $this->ruleset = cohort_rule_create_ruleset($this->cohort->draftcollectionid);

        // Create dynamic cohort.
        $this->cohort_generator->create_cohort_rule_params(
            $this->ruleset,
            'alljobassign',
            'hasindirectreports',
            $params,
            $listofvalues,
            'listofvalues'
        );

        // Calculate cohort membership.
        cohort_rules_approve_changes($this->cohort);

        // Test the results..
        $this->assertEquals($cohortmembers, $DB->count_records('cohort_members', ['cohortid' => $this->cohort->id]));
    }

    /**
     * Data provider for the indirect reports rule.
     */
    public function data_indirect_reports() {
        $data = [
            [['equal' => ui::COHORT_RULES_OP_MIN], [9], 1],   // Minimum 9 indirect reports --> expecting 1.
            [['equal' => ui::COHORT_RULES_OP_MIN], [20], 0],  // Minimum 20 indirect reports --> expecting 0.
            [['equal' => ui::COHORT_RULES_OP_MAX], [3], 3],   // Maximum 3 indirect reports --> expecting 3.
            [['equal' => ui::COHORT_RULES_OP_MAX], [9], 4],   // Maximum 9 indirect reports --> expecting 4.
            [['equal' => ui::COHORT_RULES_OP_EXACT], [3], 3], // Exactly 3 indirect reports --> expecting 3.
            [['equal' => ui::COHORT_RULES_OP_EXACT], [2], 0], // Exactly 2 indirect reports --> expecting 0.
            [['equal' => ui::COHORT_RULES_OP_NONE], [0], 7],  // No indirect reports --> expecting 7.
        ];
        return $data;
    }
}
