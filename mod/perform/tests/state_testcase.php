<?php

use mod_perform\state\state_helper;

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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

abstract class state_testcase extends advanced_testcase {

    abstract protected static function get_object_type(): string;

    /**
     * Make sure no duplicates exist for state codes, internal names and translated names.
     * Also makes sure no lang strings were forgotten.
     */
    public function test_duplicates(): void {
        $this->assert_no_duplicates('get_code');
        $this->assert_no_duplicates('get_name');
        $this->assert_no_duplicates('get_display_name');
    }

    private function assert_no_duplicates(string $method_name) {
        $all_states = state_helper::get_all(static::get_object_type());
        $unique_results = array_unique(array_map(function (string $state_class) use ($method_name) {
            return call_user_func([$state_class, $method_name]);
        }, $all_states));
        $this->assertCount(count($all_states), $unique_results);
    }
}