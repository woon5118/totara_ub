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

use core\collection as core_collection;
use core\orm\collection;
use tassign_competency\expand_task;
use totara_assignment\entities\user;
use totara_assignment\user_groups;
use totara_competency\entities\assignment;
use totara_job\job_assignment;

abstract class totara_competency_testcase extends advanced_testcase {

    /**
     * @var int
     */
    protected $time;

    protected function setUp() {
        parent::setUp();

        $this->warpTime();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * Get time based on time space continuum fluctuations
     *
     * @param bool $warp Warp time to the next milestone flag
     * @return int
     */
    protected function getTime(bool $warp = true) {
        $time = $this->time;

        if ($warp) {
            $this->warpTime($time + 86400); // 86400 is one star-day
        }

        return $time;
    }

    /**
     * Warp time to a given value
     *
     * @param int|null $time
     * @return $this
     */
    protected function warpTime(int $time = null) {
        if (is_null($time)) {
            $time = time();
        }

        $this->time = $time;

        return $this;
    }

    /**
     * Create data for the competency to test
     */
    public function create_a_lot_of_data() {

        // Define assignments
        $assignments = new collection([
            'pos' => new collection(),
            'org' => new collection(),
            'audience' => new collection(),
            'user' => new collection(),
        ]);

        // Create competency (5)
        $competencies = $this->create_some_competencies();

        // Create user(s)
        $users = $this->create_n_users();

        // Create user group(s)

        {
            $pos_users = array_merge(
                [$users->item(19)],
                array_slice($users->all(),0, 5)
            );

            $org_users = array_merge(
                [$users->item(19)],
                array_slice($users->all(),5, 5)
            );

            $audience_users = array_merge(
                [$users->item(19)],
                array_slice($users->all(),10, 5)
            );

            // Create position
            $position = $this->generator()->create_position_and_add_members($pos_users);

            // Create organisation
            $organisation = $this->generator()->create_organisation_and_add_members($org_users);

            // Create audience
            $audience = $this->generator()->create_cohort_and_add_members($audience_users);

            // Create assignments for a position and organisation
            for($i = 1; $i <= 3; $i++) {
                $assignments->item('pos')->append(
                    $this->generator()->create_position_assignment(
                        $competencies->item($i)->id,
                        $position->id,
                        $this->attributes_with_ts()
                    )
                );

                $assignments->item('org')->append(
                    $this->generator()->create_organisation_assignment(
                        $competencies->item($i)->id,
                        $organisation->id,
                        $this->attributes_with_ts()
                    )
                );

                $assignments->item('audience')->append(
                    $this->generator()->create_cohort_assignment(
                        $competencies->item($i)->id,
                        $audience->id,
                        $this->attributes_with_ts()
                    )
                );

                $assignments->item('user')->append(
                    $this->generator()->create_user_assignment(
                        $competencies->item($i)->id,
                        $users->item(18)->id,
                        $this->attributes_with_ts()
                    )
                );

                $assignments->item('user')->append(
                    $this->generator()->create_user_assignment(
                        $competencies->item($i)->id,
                        $users->item(19)->id,
                        $this->attributes_with_ts()
                    )
                );
            }

            // Let's create self assignments as well
            $assignments->item('user')->append(
                $this->generator()->create_self_assignment($competencies->item(4)->id, $users->item(19)->id)
            );
        }

        $task = new expand_task($GLOBALS['DB']);
        $task->expand_all();

        // Then we'd have to remove users from org/pos using job assignments

        $users_to_remove = $users->all();
        $users_to_remove = [
            $users_to_remove[4]->id,
            $users_to_remove[8]->id,
            $users_to_remove[9]->id,
        ];

        foreach ($users_to_remove as $user) {
            $ass = job_assignment::get_first($user);
            job_assignment::delete($ass);
        }

        // Let's remove users from a cohort
        cohort_remove_member($audience->id, $users->all()[14]->id);

        // Re-evaluating
        $task->expand_all();;

        // Now let's archive a few assignments and re-evaluate :)
        $assignments->item('user')->filter(function($assignment) use ($users) {
            $assignment = new assignment($assignment);

            if ($assignment->user_group_type === user_groups::USER &&
                $assignment->user_group_id === $users->item(19)->id) {
                $this->generator()->archive_assignment($assignment, false);
            }
        });

        // Re-evaluating
        $task->expand_all();;

        return [
            'assignments' => $assignments,
            'users' => $users,
            'pos' => $position,
            'org' => $organisation,
            'comps' => $competencies,
            'audience' => $audience,
        ];
    }

    /**
     * Create 5 predictable competencies in 2 frameworks with children.
     *
     * @return core_collection
     */
    protected function create_some_competencies(): ?core_collection {
        $competencies = new core_collection();

        $competencies->append($this->generator()->create_competency());

        $competencies->append(
            $this->generator()
                ->create_competency(
                    ['fullname' => 'Something. This is a predefined key phrase for searching a competency. Another thing'], $competencies->first()->frameworkid
                )
        );

        $competencies->append(
            $this->generator()
                ->create_competency(
                    ['description' => 'The description. This is a predefined key phrase for searching a competency'], $competencies->first()->frameworkid
                )
        )
        ;
        $competencies->append($comp = $this->generator()->create_competency());
        $competencies->append($this->generator()->create_competency([], $comp->frameworkid));

        return $competencies;
    }

    /**
     * Append attributes array with time-stamps (created_at, updated_at)
     *
     * @param array $attributes
     * @return array
     */
    protected function attributes_with_ts(array $attributes = []) {
        $time = $this->getTime();

        return array_merge($attributes, [
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }

    /**
     * Create n users
     *
     * @param int $n How many users to create
     * @param array $attributes Attributes to be shared among all users
     * @return collection
     */
    protected function create_n_users(int $n = 20, array $attributes = []) {
        $users = new collection();

        for ($i = 0; $i < $n; $i++) {
            $users->append($this->create_user($attributes));
        }

        return $users;
    }

    /**
     * Create a user
     *
     * @param array $attributes
     * @return user
     */
    protected function create_user(array $attributes = []) {
        return new user($this->getDataGenerator()->create_user($attributes));
    }

    /**
     * Get competency data generator
     *
     * @return tassign_competency_generator
     */
    public function generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }

    /**
     * Get competency data generator
     *
     * @return totara_hierarchy_generator
     */
    public function hierarchy_generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
    }

}