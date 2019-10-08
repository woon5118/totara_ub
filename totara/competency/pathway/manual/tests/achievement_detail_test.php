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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

use pathway_manual\achievement_detail;
use pathway_manual\manual;
use pathway_manual\userdata\manual_rating_other;
use totara_competency\entities\pathway_achievement;
use totara_job\job_assignment;
use totara_userdata\userdata\target_user;

class pathway_manual_achievement_detail_testcase extends advanced_testcase {

    private $scalevalue1;

    private $scalevalue2;

    private $manual;

    private $user1;

    private $user2;

    protected function setUp() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $totara_hierarchy_generator->create_comp_frame([]);
        $comp = $totara_hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new \totara_competency\entities\competency($comp);

        $scale = $competency->scale;
        $this->scalevalue1 = $scale->scale_values->first();
        $scale->scale_values->next();
        $this->scalevalue2 = $scale->scale_values->current();

        $this->manual = new manual();
        $this->manual->set_competency($competency);
        $this->manual->set_roles([manual::ROLE_SELF, manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);
        $this->manual->save();

        $this->user1 = $this->getDataGenerator()->create_user();
        $this->user2 = $this->getDataGenerator()->create_user();
    }

    protected function tearDown() {
        $this->scalevalue1 = null;
        $this->scalevalue2 = null;
        $this->manual = null;
        $this->user1 = null;
        $this->user2 = null;
    }

    public function test_get_achieved_via_strings_empty() {
        $detail = new achievement_detail();
        $this->assertSame([], $detail->get_achieved_via_strings());
    }

    /**
     * Return the correct string for a user rating themselves
     */
    public function test_get_achieved_via_string_self() {
        $this->manual->set_manual_value($this->user1->id, $this->user1->id, manual::ROLE_SELF, $this->scalevalue1->id, '');

        $this->assert_achieved_via_string($this->user1->id, 'rating by ' . fullname($this->user1) . ' (Self)');
    }

    /**
     * Return the correct string for an manager rating a user
     */
    public function test_get_achieved_via_string_manager() {
        $managerja = job_assignment::create_default($this->user2->id);
        job_assignment::create([
            'userid' => $this->user1->id,
            'idnumber' => 'a',
            'managerjaid' => $managerja->id
        ]);
        $this->manual->set_manual_value($this->user1->id, $this->user2->id, manual::ROLE_MANAGER, $this->scalevalue2->id, '');

        $this->assert_achieved_via_string($this->user1->id, 'rating by ' . fullname($this->user2) . ' (Manager)');
    }

    /**
     * Return the correct string for an appraiser rating a user
     */
    public function test_get_achieved_via_string_appraiser() {
        job_assignment::create([
            'userid' => $this->user1->id,
            'idnumber' => 'a',
            'appraiserid' => $this->user2->id,
        ]);
        $this->manual->set_manual_value($this->user1->id, $this->user2->id, manual::ROLE_APPRAISER, $this->scalevalue2->id, '');

        $this->assert_achieved_via_string($this->user1->id, 'rating by ' . fullname($this->user2) . ' (Appraiser)');
    }

    /**
     * Return the correct string for when rater details have been purged
     */
    public function test_get_achieved_via_string_rater_purged() {
        job_assignment::create([
            'userid' => $this->user1->id,
            'idnumber' => 'a',
            'appraiserid' => $this->user2->id,
        ]);
        $this->manual->set_manual_value($this->user1->id, $this->user2->id, manual::ROLE_APPRAISER, $this->scalevalue2->id, '');
        manual_rating_other::execute_purge(new target_user($this->user2), context_system::instance());

        $expected_string = get_string('rating_by_removed', 'pathway_manual', 'an appraiser');
        $this->assert_achieved_via_string($this->user1->id, $expected_string);
    }

    /**
     * Return the correct string for when the rater user has been deleted
     */
    public function test_get_achieved_via_string_rater_deleted() {
        $managerja = job_assignment::create_default($this->user2->id);
        job_assignment::create([
            'userid' => $this->user1->id,
            'idnumber' => 'a',
            'managerjaid' => $managerja->id
        ]);
        $this->manual->set_manual_value($this->user1->id, $this->user2->id, manual::ROLE_MANAGER, $this->scalevalue2->id, '');
        delete_user($this->user2);

        $expected_string = get_string('rating_by_removed', 'pathway_manual', 'a manager');
        $this->assert_achieved_via_string($this->user1->id, $expected_string);
    }

    private function assert_achieved_via_string(int $user, string $expected_string) {
        $achievement = pathway_achievement::get_current($this->manual, $user);
        $detail = new achievement_detail();
        $detail->set_related_info(json_decode($achievement->related_info, true));
        $this->assertSame([$expected_string], $detail->get_achieved_via_strings());
    }
}
