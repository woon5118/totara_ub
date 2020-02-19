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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */
class mod_perform_activity_model_testcase extends advanced_testcase {

    public function test_can_manage() {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();
        $user3 = $data_generator->create_user();

        $this->setUser($user1);
        $activity_user1 = $perform_generator->create_activity_in_container(['activity_name' => 'User1 One']);

        $this->setUser($user2);
        $activity_user2 = $perform_generator->create_activity_in_container(['activity_name' => 'User2 One']);

        $this->setAdminUser();

        $this->assertTrue($activity_user1->can_manage($user1->id));
        $this->assertFalse($activity_user1->can_manage($user2->id));
        $this->assertFalse($activity_user1->can_manage($user3->id));

        $this->assertFalse($activity_user2->can_manage($user1->id));
        $this->assertTrue($activity_user2->can_manage($user2->id));
        $this->assertFalse($activity_user2->can_manage($user3->id));

        $this->setUser($user1);
        $this->assertTrue($activity_user1->can_manage());
        $this->assertFalse($activity_user2->can_manage());
    }
}