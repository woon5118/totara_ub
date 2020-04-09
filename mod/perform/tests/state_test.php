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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\state\condition;
use mod_perform\state\invalid_state_switch_exception;
use mod_perform\state\state as base_state;
use mod_perform\state\state_helper;
use mod_perform\state\transition;

/**
 * Test some basic functionality of state and transition classes.
 *
 * @group perform
 */
class mod_perform_state_testcase extends advanced_testcase {

    public function test_transition_possible() {
        $test_object = new stdClass();

        $transition = transition::to(new test_state($test_object))->with_conditions([
            test_condition_passing::class
        ]);
        $this->assertTrue($transition->is_possible());

        $transition = transition::to(new test_state($test_object))->with_conditions([
            test_condition_not_passing::class
        ]);
        $this->assertFalse($transition->is_possible());

        $transition = transition::to(new test_state($test_object))->with_conditions([
            test_condition_passing::class,
            test_condition_not_passing::class,
        ]);
        $this->assertFalse($transition->is_possible());
    }

    public function test_state() {
        $test_object = new stdClass();
        $state = new test_state($test_object);

        $this->assertTrue($state->can_switch(test_state_2::class));
        $this->assertFalse($state->can_switch(test_state_3::class));

        $this->assertInstanceOf(test_state_2::class, $state->transition_to(test_state_2::class));
        $this->expectException(invalid_state_switch_exception::class);
        $state->transition_to(test_state_3::class);
    }
}

class test_condition_passing extends condition {
    public function pass(): bool {
        return true;
    }
}

class test_condition_not_passing extends condition {
    public function pass(): bool {
        return false;
    }
}

class test_state extends base_state {
    public function get_transitions(): array {
        return [
            transition::to(new test_state_2($this->object))->with_conditions([
                test_condition_passing::class,
            ]),
            transition::to(new test_state_3($this->object))->with_conditions([
                test_condition_passing::class,
                test_condition_not_passing::class,
            ]),
        ];
    }

    public static function get_code(): int {
        return 123;
    }

    public static function get_name(): string {
        return 'TEST_STATE';
    }

    public static function get_display_name(): string {
        return 'test state';
    }
}

class test_state_2 extends base_state {
    public function get_transitions(): array {
        return [];
    }

    public static function get_code(): int {
        return 200;
    }

    public static function get_name(): string {
        return 'TEST_STATE_2';
    }

    public static function get_display_name(): string {
        return 'test state 2';
    }
}

class test_state_3 extends base_state {
    public function get_transitions(): array {
        return [];
    }

    public static function get_code(): int {
        return 300;
    }

    public static function get_name(): string {
        return 'TEST_STATE_3';
    }

    public static function get_display_name(): string {
        return 'test state 3';
    }
}
