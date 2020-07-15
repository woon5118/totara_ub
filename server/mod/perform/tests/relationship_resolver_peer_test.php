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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Marco Song <marco.song@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\relationship\resolvers\peer;

require_once(__DIR__ . '/perform_relationship_resolver_test.php');

/**
 * @group perform
 * @covers peer
 */
class relationship_resolver_peer_testcase extends perform_relationship_resolver_test {
    /**
     * @var string
     */
    const ID_NUMBER = 'perform_peer';

    public function test_get_users_by_subject_instance_id() {
        [$user1, $subject_instance] = $this->create_relationship_resolver_data(self::ID_NUMBER);

        $peer = $this->get_resolver(self::ID_NUMBER);
        $user_ids = $peer->get_users(['subject_instance_id' => $subject_instance->id]);

        $this->assertEquals([$user1->id], $user_ids);
    }

    public function test_get_users_by_subject_instance_id_and_user_id() {
        [$user1, $subject_instance] = $this->create_relationship_resolver_data(self::ID_NUMBER);

        $peer = $this->get_resolver(self::ID_NUMBER);
        $user_ids = $peer->get_users(['subject_instance_id' => $subject_instance->id, 'user_id' => $user1->id]);

        $this->assertEquals([$user1->id], $user_ids);
    }
}