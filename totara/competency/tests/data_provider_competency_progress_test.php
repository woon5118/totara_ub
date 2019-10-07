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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

global $CFG;

use core\orm\query\builder;
use totara_assignment\entities\position;
use totara_assignment\entities\user;
use totara_assignment\user_groups;
use totara_competency\data_providers\assignments;
use totara_competency\entities\assignment;

require_once($CFG->dirroot . '/totara/competency/tests/totara_competency_testcase.php');

class totara_competency_data_provider_competency_progress_testcase extends totara_competency_testcase {

    /**
     * This integration test requires more or less large amount of data created, thus it's performing
     * multiple assertions within one test to avoid unnecessary extra resets
     */
    public function test_it_fetches_filters_and_orders_assignments() {
        $data = $this->create_a_lot_of_data();

        $user = $data['users']->item(19);

        $models = \totara_competency\data_providers\competency_progress::for($user)
            ->set_order('recently-assigned')
            ->fetch()
            ->get(); // <-- This will return a collection of competency_progress models.

        $ass = $models->pluck('assignments');

        foreach ($ass as &$a) {
            $a = $a->pluck('assignment_user');
            foreach ($a as &$i) {
                if ($i) {
                    $i = $i->to_array();
                }
            }
        }

        var_dump($ass);
        //var_dump(array_column($models->pluck('assignments'), 'assignment_user'));

        $m2 = \totara_competency\data_providers\competency_progress::for($user)
            ->set_order('alphabetical')
            ->fetch()
            ->get(); // <-- This will return a collection of competency_progress models.

        $this->assertEquals(array_column($models->pluck('competency'), 'fullname'), array_column($m2->pluck('competency'), 'fullname'));


        // Check filtering

        // Check ordering

        // Check data integrity -> will go to the model test class

    }

    protected function create_testing_data() {
        // Create 2 users
        $users = $this->create_n_users(2);

        // Create competencies
        $competencies = $this->create_some_competencies();

        $users_to_add = [$users->item(0), $users->item(1), $users->item(3)];

        // Create position
        $position = $this->generator()->create_position_and_add_members($users_to_add);

        // Create organisation
        $organisation = $this->generator()->create_organisation_and_add_members($users_to_add);

        // Create audience
        $audience = $this->generator()->create_cohort_and_add_members($users_to_add);

        // Create
    }

}