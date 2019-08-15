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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 * @subpackage test
 */

use core\orm\query\builder;
use core\webapi\execution_context;
use pathway_manual\manual;
use pathway_manual\models\role_rating;
use pathway_manual\webapi\resolver\query\role_ratings;
use tassign_competency\expand_task;
use tassign_competency\models\assignment;
use totara_competency\entities\competency;
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the query to fetch all roles and their latest ratings for a given user and competency
 */
class totara_competency_webapi_resolver_query_role_ratings_testcase extends advanced_testcase {

    private $scalevalue1;

    private $scalevalue2;

    private $manual;

    private $competency;

    private $user1_assignment;

    private $user1;

    private $user2;

    protected function setUp() {
        global $DB;

        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        /** @var tassign_competency_generator $tassign_competency_generator */
        $tassign_competency_generator = $this->getDataGenerator()->get_plugin_generator('tassign_competency');

        $compfw = $totara_hierarchy_generator->create_comp_frame([]);
        $comp = $totara_hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $this->competency = new competency($comp);

        $scale = $this->competency->scale;
        $this->scalevalue1 = $scale->scale_values->first();
        $scale->scale_values->next();
        $this->scalevalue2 = $scale->scale_values->current();

        $this->manual = new manual();
        $this->manual->set_competency($this->competency);
        $this->manual->set_roles([manual::ROLE_SELF, manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);
        $this->manual->save();

        $this->user1 = $this->getDataGenerator()->create_user();
        $this->user2 = $this->getDataGenerator()->create_user();

        $this->user1_assignment = $tassign_competency_generator->create_user_assignment($this->competency->id, $this->user1->id);

        $expand_task = new expand_task($DB);
        $expand_task->expand_all();

        $role = builder::table('role')->where('shortname', 'user')->value('id');
        $this->setUser($this->user2);
        assign_capability('totara/competency:view_other_profile', CAP_ALLOW, $role, context_user::instance($this->user1->id));
        $this->setUser($this->user1);
        assign_capability('totara/competency:view_own_profile', CAP_ALLOW, $role, context_user::instance($this->user1->id));

        $this->setAdminUser();
    }

    protected function tearDown() {
        $this->scalevalue1 = null;
        $this->scalevalue2 = null;
        $this->manual = null;
        $this->user1 = null;
        $this->user2 = null;
        $this->competency = null;
        $this->user1_assignment = null;
    }

    /**
     * @return role_rating[]
     */
    private function resolve(): array {
        return role_ratings::resolve(
            ['user_id' => $this->user1->id, 'assignment_id' => $this->user1_assignment->id],
            execution_context::create('dev', null)
        );
    }

    /**
     * Make sure the user has the right permissions to view their ratings
     */
    public function test_self_capability() {
        $role = builder::table('role')->where('shortname', 'user')->value('id');

        $this->setUser($this->user1);
        $this->assertNotNull($this->resolve());

        unassign_capability('totara/competency:view_own_profile', $role);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('View own competency profile');

        $this->resolve();
    }

    /**
     * Make sure the query cannot be accessed by another user who isn't the users manager
     */
    public function test_manager_capability() {
        $role = builder::table('role')->where('shortname', 'user')->value('id');

        $this->setUser($this->user2);
        $this->assertNotNull($this->resolve());

        unassign_capability('totara/competency:view_other_profile', $role);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('View profile of other users');

        $this->resolve();
    }

    /**
     * Make sure that the latest rating is always returned
     */
    public function test_latest_rating_is_returned() {
        $this->manual->set_manual_value(
            $this->user1->id,
            $this->user1->id,
            manual::ROLE_SELF,
            $this->scalevalue1->id,
            'Rating One'
        );

        $this->setUser($this->user1);

        /** @var \pathway_manual\entities\rating $rating */
        $rating = $this->resolve()[0]->get_latest_rating();
        $this->assertEquals('Rating One', $rating->comment);
        $this->assertEquals($this->scalevalue1->id, $rating->scale_value_id);

        $this->waitForSecond();
        $this->waitForSecond();

        $this->manual->set_manual_value(
            $this->user1->id,
            $this->user1->id,
            manual::ROLE_SELF,
            $this->scalevalue2->id,
            'Rating Two'
        );

        $rating = $this->resolve()[0]->get_latest_rating();
        $this->assertEquals('Rating Two', $rating->comment);
        $this->assertEquals($this->scalevalue2->id, $rating->scale_value_id);
    }

    /**
     * Make sure it can tell if the current user has the role returned
     */
    public function test_user_has_role() {
        $managerja = job_assignment::create_default($this->user2->id);
        job_assignment::create_default(
            $this->user1->id,
            ['managerjaid' => $managerja->id]
        );

        $appraiser = $this->getDataGenerator()->create_user();
        job_assignment::create_default(
            $this->user1->id,
            ['appraiserid' => $appraiser->id]
        );

        $this->setUser($this->user1);
        $query = $this->resolve();

        $this->assertTrue($query[0]->current_user_has_role()); // self
        $this->assertFalse($query[1]->current_user_has_role()); // manager
        $this->assertFalse($query[2]->current_user_has_role()); // appraiser

        $this->setUser($this->user2);
        $query = $this->resolve();
        $this->assertFalse($query[0]->current_user_has_role()); // self
        $this->assertTrue($query[1]->current_user_has_role()); // manager
        $this->assertFalse($query[2]->current_user_has_role()); // appraiser

        $this->setUser($appraiser);
        $query = $this->resolve();
        $this->assertFalse($query[0]->current_user_has_role()); // self
        $this->assertFalse($query[1]->current_user_has_role()); // manager
        $this->assertTrue($query[2]->current_user_has_role()); // appraiser
    }

    /**
     * Make sure that the display order of the roles are fixed, even when there are multiple pathways in different orders
     */
    public function test_role_order() {
        $this->manual->delete();

        $appraiser_pathway = new manual();
        $appraiser_pathway->set_competency($this->competency);
        $appraiser_pathway->set_roles([manual::ROLE_APPRAISER]);
        $appraiser_pathway->save();

        $query = $this->resolve();
        $this->assertCount(1, $query);
        $this->assertEquals(manual::ROLE_APPRAISER, $query[0]->get_role());

        $manager_pathway = new manual();
        $manager_pathway->set_competency($this->competency);
        $manager_pathway->set_roles([manual::ROLE_MANAGER]);
        $manager_pathway->save();

        $query = $this->resolve();
        $this->assertCount(2, $query);
        $this->assertEquals(manual::ROLE_MANAGER, $query[0]->get_role());
        $this->assertEquals(manual::ROLE_APPRAISER, $query[1]->get_role());

        $all_pathways = new manual();
        $all_pathways->set_competency($this->competency);
        $all_pathways->set_roles([manual::ROLE_MANAGER, manual::ROLE_APPRAISER, manual::ROLE_SELF]);
        $all_pathways->save();

        $query = $this->resolve();
        $this->assertCount(3, $query);
        $this->assertEquals(manual::ROLE_SELF, $query[0]->get_role());
        $this->assertEquals(manual::ROLE_MANAGER, $query[1]->get_role());
        $this->assertEquals(manual::ROLE_APPRAISER, $query[2]->get_role());
    }

    /**
     * Make sure the display names are correct, and are conditional for the self role
     */
    public function test_role_display_names() {
        $query = $this->resolve();
        $this->assertEquals(fullname($this->user1), $query[0]->get_role_display_name());
        $this->assertEquals('Manager', $query[1]->get_role_display_name());
        $this->assertEquals('Appraiser', $query[2]->get_role_display_name());

        $this->setUser($this->user1);
        $query = $this->resolve();
        $this->assertEquals('Your rating', $query[0]->get_role_display_name());
    }

    /**
     * Make sure we can get the ratings even if the assignment isn't active
     */
    public function test_load_from_inactive_assignment() {
        global $DB;

        $this->assertCount(3, $this->resolve()); // load it normally

        $assignment = assignment::load_by_id($this->user1_assignment->id);
        $assignment->archive();
        $expand_task = new expand_task($DB);
        $expand_task->expand_all();

        $this->assertCount(3, $this->resolve()); // load after archiving
    }

}
