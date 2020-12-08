<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package container_workspace
 */

defined('MOODLE_INTERNAL') || die();

use container_workspace\workspace;
use container_workspace\loader\member\audience_loader;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group container_workspace
 */
class container_workspace_audience_loader_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        global $CFG;
        require_once($CFG->dirroot.'/cohort/lib.php');
    }

    /**
     * @return container_workspace_generator
     */
    private function get_workspace_generator(): container_workspace_generator {
        $generator = self::getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        return $workspace_generator;
    }

    public function test_get_users_to_add(): void {
        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $user6 = $generator->create_user();
        $user7 = $generator->create_user();

        $cohort1 = $generator->create_cohort();
        $cohort2 = $generator->create_cohort();
        // Have an empty cohort
        $cohort3 = $generator->create_cohort();

        cohort_add_member($cohort1->id, $user2->id);
        cohort_add_member($cohort1->id, $user3->id);

        cohort_add_member($cohort2->id, $user4->id);
        cohort_add_member($cohort2->id, $user5->id);

        $this->setUser($user1);

        $workspace_generator = $this->get_workspace_generator();
        $workspace1 = $workspace_generator->create_workspace();
        $workspace2 = $workspace_generator->create_workspace();

        $workspace_generator->add_member($workspace2, $user2->id);
        $workspace_generator->add_member($workspace2, $user3->id);

        $workspace_generator->add_member($workspace1, $user4->id);
        $workspace_generator->add_member($workspace1, $user6->id);

        $cohort_ids = [$cohort1->id, $cohort2->id];
        $expected = [$user2->id, $user3->id, $user5->id];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        $workspace_generator->add_member($workspace1, $user2->id);
        $expected = [$user3->id, $user5->id];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        $workspace_generator->add_member($workspace1, $user3->id);
        $expected = [$user5->id];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        $workspace_generator->add_member($workspace1, $user5->id);
        $expected = [];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        // Now try with an empty cohort
        $cohort_ids = [$cohort3->id];
        $expected = [];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        cohort_add_member($cohort3->id, $user4->id);
        cohort_add_member($cohort3->id, $user6->id);
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);

        // Now add one more and try again

        cohort_add_member($cohort3->id, $user7->id);
        $expected = [$user7->id];
        $this->assert_cohort_members_to_add_to_workspace($workspace1, $cohort_ids, $expected);
    }

    /**
     * Verifies the cohort members that can added as members to a workspace.
     *
     * @param workspace $workspace the workspace to check.
     * @param $cohort_id cohorts whose members are to be added to the workspace.
     * @param int[] $potential_members cohort members that can be added to the
     *        workspace.
     */
    private function assert_cohort_members_to_add_to_workspace(
        workspace $workspace,
        array $cohort_ids,
        array $potential_members
    ): void {
        $count = audience_loader::get_bulk_members_to_add_count($workspace, $cohort_ids);
        $this->assertCount($count, $potential_members);

        $to_add_members = audience_loader::get_bulk_members_to_add($workspace, $cohort_ids);
        $this->assertEqualsCanonicalizing($to_add_members, $potential_members);
    }
}