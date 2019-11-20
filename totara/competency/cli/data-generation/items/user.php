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

use core\entities\user as user_entity;
use core\orm\query\builder;
use degeneration\App;
use degeneration\Cache;

/**
 * Class user
 *
 * @method user_entity get_data()
 *
 * @package degeneration\items
 */
class user extends item {

    /**
     * Array of courses user enrolled in.
     *
     * @var array
     */
    protected $courses = [];

    /**
     * Get a list of courses a user is enrolled in. This returns ONLY courses enrolled via this object
     *
     * @return array
     */
    public function get_courses(): array {
        return $this->courses;
    }

    /**
     * Add a course to the list of courses a user enrolled to. It doesn't actually check whether a user is enrolled.
     *
     * @param course $course
     * @return $this
     */
    public function add_enrolled_course(course $course) {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Table name of the item to generate
     *
     * @return string
     */
    public function get_table(): string {
        return 'user';
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function get_properties(): array {
        return [

        ];
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

        $this->data = new user_entity((array) App::generator()->create_user($properties));

        Cache::get()->add($this);

        return true;
    }

    /**
     * Enrol user to a given course
     *
     * @param course $course
     * @param string $role
     * @return bool
     */
    public function enrol(course $course, string $role = 'student'): bool {
        $role_entity = builder::table('role')
            ->where('shortname', $role)
            ->one(true);

        $this->add_enrolled_course($course);

        return App::generator()->enrol_user($this->get_data()->id, $course->get_data()->id, $role_entity->id);
    }

    /**
     * Enrol user to a given course
     *
     * @param audience $audience
     * @return bool
     */
    public function add(audience $audience): bool {
        return $audience->add_member($this);
    }

}