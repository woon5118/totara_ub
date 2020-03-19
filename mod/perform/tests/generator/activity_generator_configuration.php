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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

/**
 * This class just holds the configuration needed to create full activities.
 *
 * This allows for easier changes later when new configuration options are added.
 */
class mod_perform_activity_generator_configuration {

    /**
     * The overall amount of activities to be created
     *
     * @var int
     */
    private $number_of_activities = 1;

    /**
     * The number of assignments per activity
     *
     * @var int
     */
    private $cohort_assignments_per_activity = 1;

    /**
     * The number of users per assignment which should be created
     *
     * @var int
     */
    private $number_of_users_per_user_group_type = 5;

    /**
     * @var bool
     */
    private $generate_user_assignments = true;

    /**
     * @var bool
     */
    private $generate_subject_instances = true;

    /**
     * shortcut function to get new object
     *
     * @return static
     */
    public static function new(): self {
        return new self();
    }

    /**
     * @return int
     */
    public function get_number_of_activities(): int {
        return $this->number_of_activities;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_number_of_activities(int $number): self {
        $this->number_of_activities = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function get_cohort_assignments_per_activity(): int {
        return $this->cohort_assignments_per_activity;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_cohort_assignments_per_activity(int $number): self {
        $this->cohort_assignments_per_activity = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function get_number_of_users_per_user_group_type(): int {
        return $this->number_of_users_per_user_group_type;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_number_of_users_per_user_group_type(int $number): self {
        $this->number_of_users_per_user_group_type = $number;

        return $this;
    }

    /**
     * Disables the creation of user assignments.
     * This also automatically disables the creation of subject instances.
     *
     * @return $this
     */
    public function disable_user_assignments() {
        $this->generate_user_assignments = false;

        return $this;
    }

    /**
     * Disables the creation of subject instances.
     *
     * @return $this
     */
    public function disable_subject_instances() {
        $this->generate_subject_instances = false;

        return $this;
    }

    /**
     * Should user assignments be created?
     *
     * @return bool
     */
    public function should_generate_user_assignments(): bool {
        return $this->generate_user_assignments;
    }

    /**
     * Should subject instances be created?
     *
     * @return bool
     */
    public function should_generate_subject_instances(): bool {
        return $this->generate_subject_instances;
    }

}