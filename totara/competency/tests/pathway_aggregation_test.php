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
 * @package totara_competency
 */

class totara_competency_pathway_aggregation_testcase extends advanced_testcase {

    public function test_set_users_with_valid_values_once() {
        $user1_id = 101;
        $user2_id = 102;

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation1->set_user_ids([$user1_id, $user2_id]);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation2
         */
        $aggregation2 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation2->set_user_ids([]);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation3
         */
        $aggregation3 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation3->set_user_ids([$user1_id]);
    }

    public function test_set_users_cannot_be_set_more_than_once() {
        $user1_id = 101;
        $user2_id = 102;

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation1->set_user_ids([$user1_id, $user2_id]);

        $user3_id = 103;

        // It could be the same set of users and we'd still get the exception,
        // but we'll change the array slightly to illustrate what we are actually trying to prevent.
        $this->expectException(coding_exception::class);
        $aggregation1->set_user_ids([$user1_id, $user3_id]);
    }

    public function test_set_pathways_can_be_set_once_with_valid_values() {
        $pathway1 = $this->getMockForAbstractClass(\totara_competency\pathway::class);
        $pathway2 = $this->getMockForAbstractClass(\totara_competency\pathway::class);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation1->set_pathways([$pathway1, $pathway2]);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation2
         */
        $aggregation2 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation2->set_pathways([]);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation3
         */
        $aggregation3 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation3->set_pathways([$pathway1]);
    }

    public function test_set_pathways_cannot_be_set_more_than_once() {
        $pathway1 = $this->getMockForAbstractClass(\totara_competency\pathway::class);
        $pathway2 = $this->getMockForAbstractClass(\totara_competency\pathway::class);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation1->set_pathways([$pathway1, $pathway2]);

        $pathway3 = $this->getMockForAbstractClass(\totara_competency\pathway::class);

        // It could be the same set of pathwayss and we'd still get the exception,
        // but we'll change the array slightly to illustrate what we are actually trying to prevent.
        $this->expectException(coding_exception::class);
        $aggregation1->set_pathways([$pathway1, $pathway3]);
    }

    public function test_aggregate_initialises_to_empty_values_for_users() {
        $user1_id = 101;
        $user2_id = 102;

        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);
        $aggregation1->set_user_ids([$user1_id, $user2_id]);
        $aggregation1->set_pathways([]);

        // Internally, this will call do_aggregate. The mock should have stubbed this method with one that returns null.
        $aggregation1->aggregate();

        $this->assertNull($aggregation1->get_achieved_value_id($user1_id));
        $this->assertNull($aggregation1->get_achieved_value_id($user2_id));
        $this->assertEquals([], $aggregation1->get_achieved_via($user1_id));
        $this->assertEquals([], $aggregation1->get_achieved_via($user2_id));
    }

    public function test_aggregate_requires_users_was_set() {
        /**
         * @var PHPUnit_Framework_MockObject_MockObject|\totara_competency\pathway_aggregation $aggregation1
         */
        $aggregation1 = $this->getMockForAbstractClass(\totara_competency\pathway_aggregation::class);

        $this->expectException(coding_exception::class);
        $aggregation1->aggregate();
    }
}