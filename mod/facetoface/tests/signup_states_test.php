<?php

/*
 * This file is part of Totara LMS
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

use mod_facetoface\{ seminar, seminar_event, signup };
use mod_facetoface\signup\condition\condition;
use mod_facetoface\signup\restriction\restriction;
use mod_facetoface\signup\state\state;
use mod_facetoface\signup\transaction;

defined('MOODLE_INTERNAL') || die();

/**
 * A unit test to make sure that all states are the right classes.
 * 
 * Class mod_facetoface_signup_states_testcase
 */
class mod_facetoface_signup_states_testcase extends advanced_testcase {
    /**
     * Walk through the transitions of all states and assert their conditions or restrictions.
     *
     * @param string $prop_name the property name of the transaction class
     * @param string $parent_class
     * @return void
     */
    private function assert_state_transitions(string $prop_name, string $parent_class): void {
        $user = $this->getDataGenerator()->create_user();
        $seminar = new seminar();
        $seminar->save();
        $seminarevent = new seminar_event();
        $seminarevent->set_facetoface($seminar->get_id());
        $seminarevent->save();
        $signup = signup::create($user->id, $seminarevent);

        $done = [];

        foreach (state::get_all_states() as $stateclass) {
            $state = new $stateclass($signup);

            foreach ($state->get_map() as $transition) {
                $reflect = new ReflectionClass($transition);
                $prop = $reflect->getProperty($prop_name);
                $prop->setAccessible(true);

                $classes = $prop->getValue($transition);
                foreach ($classes as $class) {
                    // do not test a class that is already tested
                    if (array_key_exists($class, $done)) {
                        continue;
                    }
                    $done[$class] = true;
                    $obj = new $class($signup);
                    $this->assertInstanceOf($parent_class, $obj);
                }
            }
        }
    }

    /**
     * @return void
     */
    public function test_all_given_conditions_are_classes_of_condition(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // test only conditions
        $this->assert_state_transitions('conditions', condition::class);
    }

    /**
     * @return void
     */
    public function test_all_given_restrictions_are_classes_of_restriction(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // test only restrictions
        $this->assert_state_transitions('restrictions', restriction::class);
    }
}
