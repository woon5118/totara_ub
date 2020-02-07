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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */
use  mod_perform\data_providers\activity\activity;

class mod_perform_activity_data_provider_testcase extends advanced_testcase {

    public function test_fetch() {
        $this->create_test_data();
        $data_provider = new activity();
        $performs = $data_provider->fetch()->get();

        $this->assertCount(2, $performs);
        $this->assertEqualsCanonicalizing(
            ['Mid year performance', 'End year performance'],
            [$performs[0]->get_entity()->name, $performs[1]->get_entity()->name]
        );
    }

    private function create_test_data() {
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $perform_generator->create_activity(['name' => 'Mid year performance']);
        $perform_generator->create_activity(['name' => 'End year performance']);
    }
}