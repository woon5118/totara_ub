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

use mod_perform\constants;
use mod_perform\state\activity\active;

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
     * The number of tracks per activity that should be created
     *
     * @var int
     */
    private $number_of_tracks_per_activity = 1;

    /**
     * The number of sections per activity that should be created
     *
     * @var int
     */
    private $number_of_sections_per_activity = 1;

    /**
     * List of section relationships that should be created
     *
     * @var string[]
     */
    private $relationships_per_section = [constants::RELATIONSHIP_SUBJECT];

    /**
     * The number of elements that should be created per section.
     *
     * @var int
     */
    private $elements_per_section = 0;

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
     * @var bool
     */
    private $create_appraiser_for_each_subject_user = false;

    /**
     * @var bool
     */
    private $create_manager_for_each_subject_user = false;

    /**
     * @var int|null
     */
    private $activity_status;

    /**
     * @var bool
     */
    private $use_anonymous_responses = false;

    /**
     * shortcut function to get new object
     *
     * @return static
     */
    public static function new(): self {
        return new self();
    }

    /**
     * @param int $status_code
     * @return $this
     */
    public function set_activity_status(int $status_code): self {
        $this->activity_status = $status_code;

        return $this;
    }

    /**
     * Activity status, defaults to active if not set
     *
     * @return int
     */
    public function get_activity_status(): int {
        return $this->activity_status ?? active::get_code();
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
    public function get_number_of_tracks_per_activity(): int {
        return $this->number_of_tracks_per_activity;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_number_of_tracks_per_activity(int $number): self {
        $this->number_of_tracks_per_activity = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function get_number_of_sections_per_activity(): int {
        return $this->number_of_sections_per_activity;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_number_of_sections_per_activity(int $number): self {
        $this->number_of_sections_per_activity = $number;

        return $this;
    }

    /**
     * Get relationship idnumbers
     * @return array
     */
    public function get_relationships_per_section(): array {
        return $this->relationships_per_section;
    }

    /**
     * @param array $relationship_idnumber
     * @return $this
     */
    public function set_relationships_per_section(array $relationship_idnumber): self {
        $this->relationships_per_section = $relationship_idnumber;

        return $this;
    }

    /**
     * @return int
     */
    public function get_number_of_elements_per_section(): int {
        return $this->elements_per_section;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function set_number_of_elements_per_section(int $number): self {
        $this->elements_per_section = $number;

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
     * @return bool
     */
    public function should_use_anonymous_responses(): bool {
        return $this->use_anonymous_responses;
    }

    /**
     * @return $this
     */
    public function enable_anonymous_responses(): self {
        $this->use_anonymous_responses = true;

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

    /**
     * Enables the creation of appraisers.
     *
     * @return $this
     */
    public function enable_appraiser_for_each_subject_user() {
        $this->create_appraiser_for_each_subject_user = true;

        return $this;
    }

    /**
     * Enables the creation of managers.
     *
     * @return $this
     */
    public function enable_manager_for_each_subject_user() {
        $this->create_manager_for_each_subject_user = true;

        return $this;
    }

    /**
     * Should an appraiser be created for each subject user?
     *
     * @return bool
     */
    public function should_create_appraiser_for_each_subject_user(): bool {
        return $this->create_appraiser_for_each_subject_user;
    }

    /**
     * Should a manager be created for each subject user?
     *
     * @return bool
     */
    public function should_create_manager_for_each_subject_user(): bool {
        return $this->create_manager_for_each_subject_user;
    }

}