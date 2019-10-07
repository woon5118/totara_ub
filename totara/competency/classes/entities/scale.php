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
 * @package totara_competency
 */

namespace totara_competency\entities;

use coding_exception;
use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;

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
 * @property-read collection $values
 */
class scale extends entity {

    public const TABLE = 'comp_scale';

    private $scale_value_cache;

    /**
     * Values for this scale
     *
     * @return has_many
     */
    public function values(): has_many {
        return $this->has_many(scale_value::class, 'scaleid')
            ->order_by('sortorder', 'desc');
    }

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

    /**
     * Default value for this scale
     *
     * @return belongs_to
     */
    public function default_value(): belongs_to {
        return $this->belongs_to(scale_value::class, 'defaultid');
    }

    /**
     * Min proficient value for this scale
     *
     * @return belongs_to
     */
    public function min_proficient_value(): belongs_to {
        return $this->belongs_to(scale_value::class, 'minproficiencyid');
    }
}
