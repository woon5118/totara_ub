<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_job
 */

use totara_core\relationship\relationship;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\manager;

/**
 * @group totara_core_relationship
 * @covers \totara_job\relationship\resolvers\manager
 */
class totara_job_totara_core_relationship_resolvers_manager_testcase extends \advanced_testcase {

    private function create_data(): array {
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $user2ja = job_assignment::create_default($user2->id);
        $user3ja = job_assignment::create_default($user3->id);

        $user1ja1 = job_assignment::create_default($user1->id, ['managerjaid' => $user2ja->id]);
        $user1ja2 = job_assignment::create_default($user1->id, ['managerjaid' => $user3ja->id]);

        $relationship = relationship::load_by_idnumber('manager');
        $manager_resolver = new manager($relationship);

        return [$user1, $user2, $user3, $user1ja1, $user1ja2, $user2ja, $user3ja, $manager_resolver];
    }

    public function test_get_users_from_job_assignment_id(): void {
        [$user1, $user2, $user3, $user1ja1, $user1ja2, $user2ja, $user3ja, $manager_resolver] = $this->create_data();

        // user2 is the manager of user1 in ja1
        $this->assertEquals(
            [$user2->id],
            $manager_resolver->get_users(['job_assignment_id' => $user1ja1->id])
        );

        // user3 is the manager of user1 in ja2
        $this->assertEquals(
            [$user3->id],
            $manager_resolver->get_users(['job_assignment_id' => $user1ja2->id])
        );

        // user2 is not managed by anyone
        $this->assertEquals(
            [],
            $manager_resolver->get_users(['job_assignment_id' => $user2ja->id])
        );

        // user3 is not managed by anyone
        $this->assertEquals(
            [],
            $manager_resolver->get_users(['job_assignment_id' => $user3ja->id])
        );
    }

    public function test_get_users_from_user_id(): void {
        [$user1, $user2, $user3, $user1ja1, $user1ja2, $user2ja, $user3ja, $manager_resolver] = $this->create_data();

        // user2 and user3 are the managers of user1
        $this->assertEqualsCanonicalizing(
            [$user2->id, $user3->id],
            $manager_resolver->get_users(['user_id' => $user1->id])
        );

        // user2 is not managed by anyone
        $this->assertEquals(
            [],
            $manager_resolver->get_users(['user_id' => $user2->id])
        );

        // user3 is not managed by anyone
        $this->assertEquals(
            [],
            $manager_resolver->get_users(['user_id' => $user2->id])
        );
    }

    public function test_get_users_with_incorrect_attributes(): void {
        [$user1, $user2, $user3, $user1ja1, $user1ja2, $user2ja, $user3ja, $manager_resolver] = $this->create_data();

        $manager_resolver->get_users(['job_assignment_id' => -1]);
        $manager_resolver->get_users(['user_id' => -1]);
        $manager_resolver->get_users(['job_assignment_id' => -1, 'user_id' => -1]);
        $manager_resolver->get_users(['job_assignment_id' => -1, 'incorrect attribute' => -1]);
        $manager_resolver->get_users(['user_id' => -1, 'incorrect attribute' => -1]);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('The fields inputted into the ' . manager::class . ' relationship resolver are invalid');

        $manager_resolver->get_users(['incorrect attribute' => -1]);
    }

    public function test_get_users_with_no_attributes(): void {
        [$user1, $user2, $user3, $user1ja1, $user1ja2, $user2ja, $user3ja, $manager_resolver] = $this->create_data();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('The fields inputted into the ' . manager::class . ' relationship resolver are invalid');

        $manager_resolver->get_users([]);
    }

}
