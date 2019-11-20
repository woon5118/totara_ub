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

use core\entities\cohort;
use core\orm\query\builder;
use degeneration\App;
use degeneration\Cache;
use totara_competency\entities\course as course_entity;

class audience extends item {

    /**
     * Short name counter
     *
     * @var int|null
     */
    protected static $sn_counter = null;


    /**
     * Table name of the item to generate
     *
     * @return string
     */
    public function get_entity_class(): string {
        return cohort::class;
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function get_properties(): array {
        return [
            'name' => App::faker()->catchPhrase,
            'description' => App::faker()->bs,
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

        $cohort = (array) App::generator()->create_cohort($properties);

        $this->data = new cohort($cohort);

        Cache::get()->add($this);

        return true;
    }

    /**
     * Add user to this audience
     *
     * @param user $user
     * @return bool
     */
    public function add_member(user $user) {
        return cohort_add_member($this->get_data('id'), $user->get_data()->id);
    }
}