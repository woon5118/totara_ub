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

namespace degeneration\items;

use core\orm\query\builder;
use degeneration\App;
use totara_competency\entities\course as course_entity;

class course extends item {

    /**
     * Enrolled users list
     *
     * @var array
     */
    protected $enrolled_users = [];

    /**
     * Short name counter
     *
     * @var int|null
     */
    protected static $sn_counter = null;

    /**
     * Get short name prefix
     *
     * @return string
     */
    public function get_sn_prefix() {
        return 'csp_';
    }

    /**
     * Table name of the item to generate
     *
     * @return string
     */
    public function get_table(): string {
        return 'course';
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function get_properties(): array {
        return [
            'shortname' => $this->get_next_shortname(),
            'fullname' => App::faker()->catchPhrase,
            'description' => App::faker()->bs,
        ];
    }

    /**
     * Get enrolled users. This returns ONLY users enrolled via this object
     *
     * @return array
     */
    public function get_enrolled_users(): array {
        return $this->enrolled_users;
    }

    /**
     * Add enrolled user to this course, this is informative only
     *
     * @param user $user
     */
    public function add_enrolled_user(user $user) {
        $this->enrolled_users[] = $user;
    }

    /**
     * Save a user
     *
     * @return bool
     */
    public function save(): bool {
        $properties = [];

        foreach ($this->get_properties() as $key => $property) {
            $properties[$key] = $this->evaluate_property($property);
        }

        $course = (array) App::generator()->create_course($properties);

        unset($course['numsections']);
        unset($course['hiddensections']);
        unset($course['coursedisplay']);

        $this->data = new course_entity($course);

        return true;
    }

    /**
     * Check whether the model is saved
     *
     * @return bool
     */
    public function is_saved(): bool {
        return $this->data->exists() ?? false;
    }

    /**
     * Enrol user to this course
     *
     * @param user $user
     * @return bool
     */
    public function enrol(user $user): bool {
        $this->add_enrolled_user($user);

        return $user->enrol($this);
    }

    /**
     * Get next counter for course short name
     *
     * @return int
     */
    public function get_next_sn_count(): int {
        if (is_null(static::$sn_counter)) {
            static::$sn_counter = builder::table($this->get_table())
                ->where_like_starts_with('shortname', $this->get_sn_prefix())
                ->count();
        }

        return static::$sn_counter += 1;
    }

    /**
     * Get next short name for the course
     *
     * @return string
     */
    public function get_next_shortname(): string {
        return $this->get_sn_prefix() . $this->get_next_sn_count();
    }

}