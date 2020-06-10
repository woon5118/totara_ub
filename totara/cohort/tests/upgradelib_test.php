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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_cohort
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/reportbuilder/tests/reportcache_advanced_testcase.php');
require_once($CFG->dirroot . '/totara/cohort/lib.php');
require_once($CFG->dirroot . '/totara/cohort/db/upgradelib.php');
require_once($CFG->libdir . '/testing/generator/lib.php');

/**
 * Test position rules.
 *
 * To test, run this from the command line from the $CFG->dirroot
 * vendor/bin/phpunit ./totara/cohort/tests/upgradelib_test.php
 *
 */
class totara_cohort_upgradelib_testcase extends advanced_testcase {

    private $generator;
    private $cohort;
    private $ruleset;
    private $program;
    private $course;

    protected function tearDown(): void {
        $this->generator = null;
        $this->cohort = null;
        $this->ruleset = null;
        $this->program = null;
        $this->course = null;
        parent::tearDown();
    }

    public function setUp(): void {

        $this->generator = $this->getDataGenerator()->get_plugin_generator('totara_cohort');
        // Creating dynamic cohort.
        $cohortdata = array('name' => 'Test Cohort', 'cohorttype' => cohort::TYPE_DYNAMIC);
        $this->cohort = $this->generator->create_cohort($cohortdata);
        $this->ruleset = cohort_rule_create_ruleset($this->cohort->draftcollectionid);

        $coursedata = array('fullname' => 'Test Course');
        $this->getDataGenerator()->create_course($coursedata);

        $progdata = array('fullname' => 'Test Program');
        $this->getDataGenerator()->get_plugin_generator('totara_program')->create_program($progdata);
    }

    /**
     * Test the migration of some rules.
     */
    public function test_cohort_rule_migration() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create an old style rule.
        $this->generator->create_cohort_rule_params($this->ruleset, 'learning', 'programcompletionduration',
                array('equal' => COHORT_RULE_COMPLETION_OP_BEFORE_PAST_DURATION, 'date' => 1), array($this->program, false));

        // Create a control rule.
        $this->generator->create_cohort_rule_params($this->ruleset, 'learning', 'coursecompletionduration',
                array('equal' => COHORT_RULE_COMPLETION_OP_AFTER_FUTURE_DURATION, 'date' => 1), array($this->course, false));

        // Approve the rule changes and check them.
        cohort_rules_approve_changes($this->cohort);
        $this->assertEquals(4, $DB->count_records('cohort_rules', array())); // Twice as many due to draft rules.

        // Migrate one of the rules to a new name.
        totara_cohort_migrate_rules('learning', 'programcompletionduration', 'learning', 'programcompletiondurationassigned');

        $this->assertEquals(4, $DB->count_records('cohort_rules', array()));
        $this->assertEquals(2, $DB->count_records('cohort_rules', array('name' => 'programcompletiondurationassigned')));
        $this->assertEquals(4, $DB->count_records('cohort_rules', array('ruletype' => 'learning')));

        // Now migrate the rule type.
        totara_cohort_migrate_rules('learning', 'programcompletiondurationassigned', 'teaching', 'programcompletiondurationassigned');

        $this->assertEquals(4, $DB->count_records('cohort_rules', array()));
        $this->assertEquals(2, $DB->count_records('cohort_rules', array('ruletype' => 'learning')));
        $this->assertEquals(2, $DB->count_records('cohort_rules', array('ruletype' => 'teaching')));
    }

    /**
     * Test upgrade from Old Has direct reports rule to a new one.
     */
    public function test_cohort_update_has_direct_reports_rule() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        for ($i = 1; $i <= 23; $i++) {
            $this->{'user'.$i} = $this->getDataGenerator()->create_user();
        }
        // Check the users were created. It should match $this->countmembers + 2 users(admin + guest).
        $this->assertEquals(23 + 2, $DB->count_records('user'));

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
            'userid' => $this->user2->id,
            'fullname' => 'user2',
            'shortname' => 'user2',
            'idnumber' => 'id2',
            'managerjaid' => $manager2ja->id,
        ]);

        $this->generator = $this->getDataGenerator()->get_plugin_generator('totara_cohort');

        // Creating 'Does not have direct report' dynamic cohort.
        $cohortdata = array('name' => 'Does not have direct report', 'cohorttype' => cohort::TYPE_DYNAMIC);
        $this->cohort1 = $this->generator->create_cohort($cohortdata);
        // Create cohort_rulesets.
        $rulesetid1 = cohort_rule_create_ruleset($this->cohort1->draftcollectionid);
        // Create cohort_rule_params records.
        $this->generator->create_cohort_rule_params($rulesetid1, 'alljobassign', 'hasdirectreports', array('equal' => 1), array(0));
        // Approve rule and generate cohort members.
        // We use our function to skip a new sqlhandler.
        self::approve_old_hasdirectreports_rule($this->cohort1, true, false);
        // Lets test how many users has old rule.
        $this->assertEquals(24, $DB->count_records('cohort_members', array('cohortid' => $this->cohort1->id)));

        // Creating 'Has direct report' dynamic cohort.
        $cohortdata = array('name' => 'Has direct report', 'cohorttype' => cohort::TYPE_DYNAMIC);
        $this->cohort2 = $this->generator->create_cohort($cohortdata);
        // Create cohort_rulesets.
        $rulesetid2 = cohort_rule_create_ruleset($this->cohort2->draftcollectionid);
        // Create cohort_rule_params records.
        $this->generator->create_cohort_rule_params($rulesetid2, 'alljobassign', 'hasdirectreports', array('equal' => 1), array(1));
        // Approve rule and generate cohort members.
        // We use our function to skip a new sqlhandler.
        self::approve_old_hasdirectreports_rule($this->cohort2, true, true);
        // Lets test how many users has rule.
        $this->assertEquals(2, $DB->count_records('cohort_members', array('cohortid' => $this->cohort2->id)));

        // Upgrade cohort_rule_params records to reflect new conditions for 'Does not have direct report' dynamic cohort.
        totara_cohort_update_has_direct_reports_rule();
        // Re-run the approvement and re-generate cohort members.
        cohort_rules_approve_changes($this->cohort1);
        // Should be same members for 'Does not have direct report' dynamic cohort.
        $this->assertEquals(24, $DB->count_records('cohort_members', array('cohortid' => $this->cohort1->id)));
        // Should be same members for 'Has direct report' dynamic cohort.
        $this->assertEquals(2, $DB->count_records('cohort_members', array('cohortid' => $this->cohort2->id)));
    }

    /**
     * Copy/paste from cohort_rules_approve_changes() function except calling self::totara_cohort_update_dynamic_cohort_members()
     *
     * @param $cohort
     * @param bool $syncmembers
     * @param bool $hasreports
     */
    private static function approve_old_hasdirectreports_rule($cohort, $syncmembers = true, $hasreports) {
        global $DB, $USER;

        $now = time();

        $transaction = $DB->start_delegated_transaction();

        // Mark current active cohort collection as obsolete.
        $todb = new stdClass;
        $todb->id = $cohort->activecollectionid;
        $todb->status = COHORT_COL_STATUS_OBSOLETE;
        $todb->timemodified = $now;
        $todb->modifierid = $USER->id;
        $DB->update_record('cohort_rule_collections', $todb);

        // Copy current draft cohort collection.
        $dcollid = cohort_rules_clone_collection($cohort->draftcollectionid, COHORT_COL_STATUS_DRAFT_UNCHANGED, false);

        // Mark current draft cohort collection as active.
        $todb = new stdClass;
        $todb->id = $cohort->draftcollectionid;
        $todb->status = COHORT_COL_STATUS_ACTIVE;
        $todb->timemodified = $now;
        $todb->modifierid = $USER->id;
        $DB->update_record('cohort_rule_collections', $todb);

        // Update cohort.
        $todb = new stdClass;
        $todb->id = $cohort->id;
        $todb->activecollectionid = $cohort->draftcollectionid;
        $todb->draftcollectionid = $dcollid;
        $todb->timemodified = $now;
        $todb->modifierid = $USER->id;
        $DB->update_record('cohort', $todb);
        // Delete the now-obsolete previous collection.
        cohort_rules_delete_collection($cohort->activecollectionid);

        $transaction->allow_commit();

        // Trigger draft saved event.
        \totara_cohort\event\draftcollection_saved::create_from_instance($cohort)->trigger();

        if ($syncmembers) {
            self::update_hasdirectreports_cohort_members($cohort->id, 0, true, true, $hasreports);
        }

        return true;
    }

    /**
     * Copy/paste from totara_cohort_update_dynamic_cohort_members() function except calling self::totara_cohort_get_dynamic_cohort_whereclause() function
     *
     * @param $cohortid
     * @param int $userid
     * @param bool $delaymessages
     * @param bool $updatenested
     * @param bool $hasreports
     * @return array
     */
    private static function update_hasdirectreports_cohort_members($cohortid, $userid = 0, $delaymessages = false, $updatenested = true, $hasreports) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/totara/cohort/rules/lib.php');

        /// update necessary nested cohorts first (if any)
        if ($updatenested) {
            $nestedcohorts = totara_cohort_get_nested_dynamic_cohorts($cohortid, array(), true);
            foreach ($nestedcohorts as $ncohortid) {
                self::update_hasdirectreports_cohort_members($ncohortid, $userid, $delaymessages, false, $hasreports);
            }
        }

        /// find members who should be added and deleted
        $sql = "
           SELECT userid AS id, MAX(inrules) AS addme, MAX(inmembers) AS deleteme
             FROM (
               SELECT u.id as userid, 1 AS inrules, 0 AS inmembers
                 FROM {user} u
                WHERE u.username <> 'guest'
                  and u.deleted = 0
                  and u.confirmed = 1
        ";
        $sqlparams = array();

        if ($userid) {
            $sql .= " AND u.id = :userid ";
            $sqlparams['userid'] = $userid;
        }

        $whereclause = self::get_hasdirectreports_whereclause($hasreports);
        if (empty($whereclause)) {
            // no whereclause, no members!
            return false;
        }
        $sql .= " AND ({$whereclause->sql})";

        $sql .= " UNION ALL
               SELECT cm.userid AS userid, 0 AS inrules, 1 AS inmembers
               FROM {cohort_members} cm
               WHERE cm.cohortid = :cohortid ";
        $sqlparams['cohortid'] = $cohortid;

        if ($userid) {
            $sql .= " AND cm.userid = :userid2 ";
            $sqlparams['userid2'] = $userid;
        }

        $sql .= " ) q
           GROUP BY userid
           HAVING MAX(inrules) <> MAX(inmembers)
        ";

        $changedmembers = $DB->get_recordset_sql($sql, array_merge($sqlparams, $whereclause->params));
        if (!$changedmembers) {
            $changedmembers = array();
        }

        // Get the membersip options so we know whether to add and / remove users.
        $sql = "SELECT crc.id, crc.addnewmembers, crc.removeoldmembers
                  FROM {cohort} c
            INNER JOIN {cohort_rule_collections} crc ON c.draftcollectionid = crc.id
                 WHERE c.id = ?";
        $cohort = $DB->get_record_sql($sql, array($cohortid));

        // Update memberships in batches.
        $newmembers = array();
        $delmembers = array();
        $cmcount = 0;
        $numadd = 0;
        $numdel = 0;
        $currentcohortroles = totara_get_cohort_roles($cohortid); // Current roles assigned to this cohort.
        foreach ($changedmembers as $mem) {
            $cmcount++;
            if ($mem->addme && $cohort->addnewmembers) {
                $newmembers[$mem->id] = $mem;
            }
            if ($mem->deleteme && $cohort->removeoldmembers) {
                $delmembers[$mem->id] = $mem;
            }
            if ($cmcount < 2000) {
                // continue to add records to current batches
                continue;
            }

            if (!empty($newmembers)) {
                $now = time();
                foreach ($newmembers as $i => $rec) {
                    var_dump($rec->id);
                    $newmembers[$i] = (object)array('cohortid' => $cohortid, 'userid' => $rec->id, 'timeadded' => $now);
                }
                $DB->insert_records_via_batch('cohort_members', $newmembers);
                totara_set_role_assignments_cohort($currentcohortroles, $cohortid, array_keys($newmembers));
                totara_cohort_notify_add_users($cohortid, array_keys($newmembers), $delaymessages);
                $numadd += count($newmembers);
                unset($newmembers);
            }

            if (!empty($delmembers)) {
                $delids = array_keys($delmembers);
                unset($delmembers);
                list($sqlin, $params) = $DB->get_in_or_equal($delids, SQL_PARAMS_NAMED);
                $params['cohortid'] = $cohortid;
                $DB->delete_records_select(
                    'cohort_members',
                    "cohortid = :cohortid AND userid ".$sqlin, $params
                );
                totara_unset_role_assignments_cohort($currentcohortroles, $cohortid, $delids);
                totara_cohort_notify_del_users($cohortid, $delids, $delaymessages);
                $numdel += count($delids);
                unset($delids);
            }

            // reset stuff for next batch
            $newmembers = array();
            $delmembers = array();
            $cmcount = 0;
        }
        $changedmembers->close();
        unset($changedmembers);

        /// process leftover batches (if any)
        if (!empty($newmembers)) {
            $now = time();
            foreach ($newmembers as $i => $rec) {
                $newmembers[$i] = (object)array('cohortid' => $cohortid, 'userid' => $rec->id, 'timeadded' => $now);
            }
            $DB->insert_records_via_batch('cohort_members', $newmembers);
            totara_set_role_assignments_cohort($currentcohortroles, $cohortid, array_keys($newmembers));
            totara_cohort_notify_add_users($cohortid, array_keys($newmembers), $delaymessages);
            $numadd += count($newmembers);
            unset($newmembers);
        }

        if (!empty($delmembers)) {
            $delids = array_keys($delmembers);
            unset($delmembers);
            list($sqlin, $params) = $DB->get_in_or_equal($delids, SQL_PARAMS_NAMED);
            $params['cohortid'] = $cohortid;
            $DB->delete_records_select(
                'cohort_members',
                "cohortid = :cohortid AND userid ".$sqlin, $params
            );
            totara_unset_role_assignments_cohort($currentcohortroles, $cohortid, $delids);
            totara_cohort_notify_del_users($cohortid, $delids, $delaymessages);
            $numdel += count($delids);
            unset($delids);
        }

        if ($numadd > 0 || $numdel > 0) {
            $event = \totara_cohort\event\members_updated::create(
                array(
                    'objectid' => $cohortid,
                )
            );
            $event->trigger();
        }

        return array('add' => $numadd, 'del' => $numdel);
    }

    /**
     * This function return old sqlhandler for "Has direct reports" or "Does not have direct reports" rules.
     *
     * @param bool $hasreports
     * return object $sqlhandler
     */
    private static function get_hasdirectreports_whereclause($hasreports) {

        $sqlhandler = new stdClass();
        $sqlhandler->params = [];
        $sqlhandler->sql = '1=1  
            AND ( 1=1 
                AND ';
        $sqlhandler->sql .= ($hasreports ? '' : 'NOT ') . 'EXISTS (
                        SELECT 1
                          FROM {job_assignment} staffja
                          JOIN {job_assignment} managerja
                            ON staffja.managerjaid = managerja.id
                         WHERE managerja.userid = u.id
                    )  
            ) 
        ';
        return $sqlhandler;
    }
}
