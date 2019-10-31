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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_manual
 */

use pathway_manual\manual;
use totara_competency\entities\competency;
use totara_competency\pathway_aggregator;
use totara_job\job_assignment;
use totara_competency\entities\pathway_achievement;
use pathway_manual\entities\rating;

class pathway_manual_rating_testcase extends advanced_testcase {

    /**
     * @return competency
     */
    private function create_competency(): competency {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        return new competency($comp);
    }

    public function test_get_value_when_none_set() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_SELF]);
        $manual->save();

        $user_id = 201;
        $this->assertNull(pathway_achievement::get_current($manual, $user_id)->scale_value_id);
    }

    public function test_set_manual_null_value_as_self() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_SELF]);
        $manual->save();

        $this->setCurrentTimeStart();
        $user_id = 201;
        $manual->set_manual_value($user_id, $user_id, manual::ROLE_SELF, null, 'Go me');

        $this->assertNull(pathway_achievement::get_current($manual, $user_id)->scale_value_id);

        $ratings = rating::repository()->get();
        $this->assertEquals(1, $ratings->count());
        /** @var rating $rating */
        $rating = $ratings->first();
        $this->assertEquals($competency->id, $rating->comp_id);
        $this->assertEquals($user_id, $rating->user_id);
        $this->assertEquals($user_id, $rating->assigned_by);
        $this->assertEquals(manual::ROLE_SELF, $rating->assigned_by_role);
        $this->assertNull($rating->scale_value_id);
        $this->assertTimeCurrent($rating->date_assigned);
        $this->assertEquals('Go me', $rating->comment);
    }

    public function test_set_manual_scale_value_as_self() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_SELF]);
        $manual->save();

        $user_id = 201;

        $this->setCurrentTimeStart();
        $scale_value = $competency->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();
        $manual->set_manual_value($user_id, $user_id, manual::ROLE_SELF, $scale_value->id, 'Great jerb');

        $this->assertEquals(
            $scale_value->id,
            $pathway_achievement2 = pathway_achievement::get_current($manual, $user_id)->scale_value_id
        );

        $ratings = rating::repository()->get();
        $this->assertEquals(1, $ratings->count());
        /** @var rating $rating */
        $rating = $ratings->first();
        $this->assertEquals($competency->id, $rating->comp_id);
        $this->assertEquals($user_id, $rating->user_id);
        $this->assertEquals($user_id, $rating->assigned_by);
        $this->assertEquals(manual::ROLE_SELF, $rating->assigned_by_role);
        $this->assertEquals($scale_value->id, $rating->scale_value_id);
        $this->assertTimeCurrent($rating->date_assigned);
        $this->assertEquals('Great jerb', $rating->comment);
    }

    public function test_set_manual_scale_value_as_manager() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_MANAGER]);
        $manual->save();

        $manager = $this->getDataGenerator()->create_user();
        $managerja = job_assignment::create_default($manager->id);
        $user = $this->getDataGenerator()->create_user();
        job_assignment::create_default($user->id, ['managerjaid' => $managerja->id]);

        $this->setCurrentTimeStart();
        $scale_value = $competency->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();
        $manual->set_manual_value($user->id, $manager->id, manual::ROLE_MANAGER, $scale_value->id);

        $this->assertEquals(
            $scale_value->id,
            $pathway_achievement2 = pathway_achievement::get_current($manual, $user->id)->scale_value_id
        );

        $ratings = rating::repository()->get();
        $this->assertEquals(1, $ratings->count());
        /** @var rating $rating */
        $rating = $ratings->first();
        $this->assertEquals($competency->id, $rating->comp_id);
        $this->assertEquals($user->id, $rating->user_id);
        $this->assertEquals($manager->id, $rating->assigned_by);
        $this->assertEquals(manual::ROLE_MANAGER, $rating->assigned_by_role);
        $this->assertEquals($scale_value->id, $rating->scale_value_id);
        $this->assertTimeCurrent($rating->date_assigned);
        $this->assertEquals('', $rating->comment);
    }

    public function test_exception_when_no_role_applies() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_MANAGER]);
        $manual->save();

        $manager = $this->getDataGenerator()->create_user();
        job_assignment::create_default($manager->id);
        $user = $this->getDataGenerator()->create_user();
        // We are not setting the user to have any manager here.
        job_assignment::create_default($user->id);

        $scale_value = $competency->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();

        $this->expectException(coding_exception::class);

        $manual->set_manual_value($user->id, $manager->id, manual::ROLE_MANAGER, $scale_value->id, '');
    }

    public function test_set_value_when_multiple_roles_apply() {
        $competency = $this->create_competency();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);
        $manual->save();

        $manager = $this->getDataGenerator()->create_user();
        $managerja = job_assignment::create_default($manager->id);
        $user = $this->getDataGenerator()->create_user();
        job_assignment::create_default(
            $user->id,
            ['managerjaid' => $managerja->id, 'appraiserid' => $manager->id]
        );

        $scale_value = $competency->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();
        $manual->set_manual_value($user->id, $manager->id, manual::ROLE_APPRAISER, $scale_value->id, '');

        $this->assertEquals(
            $scale_value->id,
            $pathway_achievement2 = pathway_achievement::get_current($manual, $user->id)->scale_value_id
        );

        // And now make sure that the value was set as appraiser and not as manager.
        $ratings = rating::repository()->get();
        $this->assertEquals(1, $ratings->count());
        $rating = $ratings->first();
        $this->assertEquals(manual::ROLE_APPRAISER, $rating->assigned_by_role);
    }

    public function test_roles_resolve() {
        $user = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $appraiser = $this->getDataGenerator()->create_user();
        $mgrmgr = $this->getDataGenerator()->create_user();
        $mgr_appraiser = $this->getDataGenerator()->create_user();

        $mgrmgrja = job_assignment::create_default($mgrmgr->id);
        $managerja = job_assignment::create_default($manager->id, ['managerjaid' => $mgrmgrja->id]);
        $mgr_appraiserja = job_assignment::create_default($mgr_appraiser->id);

        // The user will have 2 job assignments.
        job_assignment::create_default(
            $user->id,
            ['managerjaid' => $managerja->id, 'appraiserid' => $appraiser->id]
        );

        job_assignment::create(
            [
                'userid' => $user->id,
                'idnumber' => 2,
                'managerjaid' => $mgr_appraiserja->id,
                'appraiserid' => $mgr_appraiser->id
            ]
        );

        $manual = new manual();
        $manual->set_roles([manual::ROLE_SELF]);
        $this->assertEquals([manual::ROLE_SELF => manual::ROLE_SELF], $manual->get_roles_that_apply_to_user($user->id, $user->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $manager->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $appraiser->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgrmgr->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgr_appraiser->id));

        $manual->set_roles([manual::ROLE_MANAGER]);
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $user->id));
        $this->assertEquals([manual::ROLE_MANAGER => manual::ROLE_MANAGER], $manual->get_roles_that_apply_to_user($user->id, $manager->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $appraiser->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgrmgr->id));
        $this->assertEquals([manual::ROLE_MANAGER => manual::ROLE_MANAGER], $manual->get_roles_that_apply_to_user($user->id, $mgr_appraiser->id));

        $manual->set_roles([manual::ROLE_APPRAISER]);
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $user->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $manager->id));
        $this->assertEquals([manual::ROLE_APPRAISER => manual::ROLE_APPRAISER], $manual->get_roles_that_apply_to_user($user->id, $appraiser->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgrmgr->id));
        $this->assertEquals([manual::ROLE_APPRAISER => manual::ROLE_APPRAISER], $manual->get_roles_that_apply_to_user($user->id, $mgr_appraiser->id));

        $manual->set_roles([manual::ROLE_SELF, manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);
        $this->assertEquals([manual::ROLE_SELF => manual::ROLE_SELF], $manual->get_roles_that_apply_to_user($user->id, $user->id));
        $this->assertEquals([manual::ROLE_MANAGER => manual::ROLE_MANAGER], $manual->get_roles_that_apply_to_user($user->id, $manager->id));
        $this->assertEquals([manual::ROLE_APPRAISER => manual::ROLE_APPRAISER], $manual->get_roles_that_apply_to_user($user->id, $appraiser->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgrmgr->id));
        $roles_for_mgr_appraiser = $manual->get_roles_that_apply_to_user($user->id, $mgr_appraiser->id);
        $this->assertCount(2, $roles_for_mgr_appraiser);
        $this->assertEquals(manual::ROLE_MANAGER, $roles_for_mgr_appraiser[manual::ROLE_MANAGER]);
        $this->assertEquals(manual::ROLE_APPRAISER, $roles_for_mgr_appraiser[manual::ROLE_APPRAISER]);

        $manual->set_roles([]);
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $user->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $manager->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $appraiser->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgrmgr->id));
        $this->assertEmpty($manual->get_roles_that_apply_to_user($user->id, $mgr_appraiser->id));
    }

    public function test_set_value_when_multiple_pathways() {
        $competency = $this->create_competency();

        $manual1 = new manual();
        $manual1->set_competency($competency);
        $manual1->set_roles([manual::ROLE_MANAGER]);
        $manual1->save();

        $manual2 = new manual();
        $manual2->set_competency($competency);
        $manual2->set_roles([manual::ROLE_SELF]);
        $manual2->save();

        $scale_values = $competency->scale->values()
            ->order_by('sortorder', 'asc')
            ->get()
            ->all();
        $scale_value1 = array_pop($scale_values);
        $scale_value2 = array_pop($scale_values);

        $manager = $this->getDataGenerator()->create_user();
        $managerja = job_assignment::create_default($manager->id);
        $user = $this->getDataGenerator()->create_user();
        job_assignment::create_default(
            $user->id,
            ['managerjaid' => $managerja->id, 'appraiserid' => $manager->id]
        );

        // Set different values for the two different manual pathways. Assert that afterwards, those values correctly
        // apply to their respective pathways.

        $manual1->set_manual_value($user->id, $manager->id, manual::ROLE_MANAGER, $scale_value1->id, '');
        $manual2->set_manual_value($user->id, $user->id, manual::ROLE_SELF, $scale_value2->id, '');

        $pathway_achievement1 = pathway_achievement::get_current($manual1, $user->id);
        $pathway_achievement2 = pathway_achievement::get_current($manual2, $user->id);

        $this->assertEquals($pathway_achievement1->scale_value_id, $scale_value1->id);
        $this->assertEquals($pathway_achievement2->scale_value_id, $scale_value2->id);

        // This happens internally when setting a manual value. We do this now independent of a manual
        // value being set to ensure that one still does not pick up the other's value.
        (new pathway_aggregator($manual1))->aggregate([$user->id]);
        (new pathway_aggregator($manual2))->aggregate([$user->id]);

        $pathway_achievement1 = pathway_achievement::get_current($manual1, $user->id);
        $pathway_achievement2 = pathway_achievement::get_current($manual2, $user->id);

        $this->assertEquals($pathway_achievement1->scale_value_id, $scale_value1->id);
        $this->assertEquals($pathway_achievement2->scale_value_id, $scale_value2->id);
    }
}
