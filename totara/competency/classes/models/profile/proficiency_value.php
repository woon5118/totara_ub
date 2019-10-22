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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com
 * @package totara_competency
 */

namespace totara_competency\models\profile;

use coding_exception;
use totara_competency\entities\assignment;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;
use totara_competency\models\basic_model;

/**
 * Class proficiency value model
 *
 * This model represents a relative proficiency value
 */
class proficiency_value {

    /**
     * @var assignment
     */
    protected $assignment;

    /**
     * Current achievement scale value id
     *
     * @var int
     */
    protected $id;

    /**
     * Current achievement scale value name
     *
     * @var string
     */
    protected $name;

    /**
     * A flag whether the current value is proficient
     *
     * @var bool
     */
    protected $proficient;

    /**
     * Relative percentage for the current proficient value
     * Where 0% is no value (value not achieved)
     * and a 100% is the highest value on the scale
     *
     * @var float
     */
    protected $percentage;

    /**
     * Related scale id for a given proficient value
     *
     * @var int
     */
    protected $scale_id;

    /**
     * Array of attributes publicly available on the model
     *
     * @var array
     */
    protected $public_attributes = [
        'id',
        'name',
        'proficient',
        'percentage',
        'scale_id'
    ];

    /**
     * proficiency_value constructor.
     *
     * @param assignment $assignment
     */
    public function __construct(assignment $assignment) {
        $this->assignment = $assignment;
    }

    /**
     * Create my proficiency value based on a competency assignment
     * Note, the assignment that you supply MUST have current_achievement relation pre-loaded,
     * otherwise it doesn't make sense and will return you a value of the first random user on the assignment
     *
     * @param assignment $assignment
     * @return proficiency_value
     */
    public static function my_value(assignment $assignment) {
        $value = new static($assignment);

        if (!$assignment->relation_loaded('current_achievement')) {
            throw new coding_exception('You must preload "current_achievement" relation with a user filter included, otherwise it does not make sense...');
        }

        $value->scale_id = $assignment->competency->scale->id;

        if ($assignment->current_achievement) {
            $value->id = $assignment->current_achievement->value->id;
            $value->name = $assignment->current_achievement->value->name;
            $value->proficient = boolval($assignment->current_achievement->value->proficient);
            $value->percentage = static::calculate_scale_value_percentage(
                $assignment->current_achievement->value,
                $assignment->competency->scale
            );
        } else {
            $value->id = 0; // It's a pseudo value, with no actual record in the db
            $value->name = get_string('no_value_achieved', 'totara_competency');
            $value->proficient = false; // No value is always not proficient
            $value->percentage = 0; // No value is always 0 percent.
        }

        return $value;
    }

    /**
     * Create a minimum proficient value of the competency scale based on assignment
     *
     * @param assignment $assignment
     * @return proficiency_value
     */
    public static function min_value(assignment $assignment) {
        $value = new static($assignment);

        $value->id = $assignment->competency->scale->min_proficient_value->id;
        $value->name = $assignment->competency->scale->min_proficient_value->name;
        $value->scale_id = $assignment->competency->scale->id;
        $value->proficient = true; // This is a min proficient value, so always proficient :)
        $value->percentage = static::calculate_scale_value_percentage(
            $assignment->competency->scale->min_proficient_value,
            $assignment->competency->scale
        );

        return $value;
    }

    /**
     * Calculate a percentage of a scale value relative to the scale including no value
     *
     * @param scale_value $value Scale value to calculate relative percentage
     * @param scale $scale Scale to calculate
     * @return float
     */
    protected static function calculate_scale_value_percentage(scale_value $value, scale $scale): float {
        $pos = $scale->values->reduce(function($pos, $scale_value) use ($value) {
            if ($value->sortorder <= $scale_value->sortorder) {
                $pos += 1;
            }
            return $pos;
        }, 0);

        return round($pos / count($scale->values) * 100);
    }

    /**
     * Return whether publicly available attribute is set on the model
     *
     * @param $name
     * @return bool
     */
    public function __isset($name): bool {
        return in_array($name, $this->public_attributes);
    }

    /**
     * Get publicly available attribute
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        // Having ?? automatically triggers isset check and returns only publicly available attributes
        return $this->{$name} ?? null;
    }
}