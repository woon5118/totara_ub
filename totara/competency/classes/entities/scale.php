<?php
/*
 * This file is part of Totara Learn
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package tassign_competency
 */

namespace totara_competency\entities;


use coding_exception;
use core\orm\collection;
use core\orm\entity\entity;

/**
 * Resource competency_scale
 *
 * @property-read int $id ID
 * @property string $name Scale name
 * @property string $description Scale description
 * @property int $timemodified Time modified
 * @property int $usermodified User modified
 * @property int $defaultid Default id
 * @property int $minproficiencyid
 *
 * @property_read scale_value $default_value
 * @property_read scale_value $min_proficient_value
 * @property-read scale_value[]|collection $scale_values
 */
class scale extends entity {

    public const TABLE = 'comp_scale';

    private $scale_value_cache;

    /**
     * @return collection
     * @throws coding_exception
     */
    protected function get_scale_values_attribute() {
        if (!isset($this->scale_value_cache)) {
            $this->scale_value_cache = scale_value::repository()
                    ->where('scaleid', $this->id)
                    ->order_by('sortorder', 'asc')
                    ->get();
        }

        return $this->scale_value_cache;
    }

    protected function get_default_value_attribute() {
        return $this->scale_values->item($this->defaultid);
    }

    protected function get_min_proficient_value_attribute() {
        return $this->scale_values->item($this->minproficiencyid);
    }
}
