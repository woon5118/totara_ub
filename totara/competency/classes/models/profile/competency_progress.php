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

use core\orm\collection;
use stdClass;
use tassign_competency\models\assignment as assignment_model;
use tassign_competency\quickaccessmenu\competency_assignment;
use totara_assignment\entities\user;
use totara_competency\data_providers\assignments;
use totara_competency\entities\assignment;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;
use totara_competency\models\basic_model;

/**
 * Class profile_progress
 *
 * This is a profile progress item model scaffolding, it has the following properties available:
 *
 *  - Key -> md5 of some attributes it's grouped by
 *  - Assignments -> [Assignment] - a collection of related assignment models
 *  - Overall progress -> int - Overall progress value per this group
 *
 *
 * @property-read string $key A key uniquely identifying this progress item
 * @property-read collection $assignments Collection of assignments for this user group
 * @property-read collection $filters Collection of filters
 * @property-read string $latest_achievement Latest achieved competency name (if any)
 * @package totara_competency\models
 */
class competency_progress {

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Key
     *
     * @var string
     */
    protected $key;

    public function __construct(assignment $assignment) {
        $this->attributes = [
            'competency' => $assignment->competency,
            'achievement' => $assignment->current_achievement,
            'my_value' => $assignment->current_achievement->value ?? null,
            'assignments' => new collection([
                assignment_model::load_by_entity($assignment),
            ]),
        ];
    }

    public function get_assignments(): collection {
        return $this->assignments;
    }

    public static function build_from_assignments(collection $assignments) {
        $progress = new collection();

        $assignments->map(function(assignment $assignment) use ($progress) {
            if (!$progress->item($assignment->competency_id)) {
                $progress->set(new static($assignment), $assignment->competency_id);
            } else {
                $progress->item($assignment->competency_id)->append_assignment($assignment);
            }
        });

        return $progress;
    }

    /**
     * Build progress for one competency for a given user
     *
     * @param int|user|stdClass $user User id or object
     * @param int $competency_id Competency id
     * @return competency_progress|null
     */
    public static function build_for_competency($user, int $competency_id): ?self {
        return static::build_from_assignments(
            assignments::for($user)->set_filters([
                'competency_id' => $competency_id,
            ])->fetch()->get())->first();
    }

    /**
     * Get attribute
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        if ($name === 'items') {
            $name = 'assignments';
        }

        return $this->attributes[$name] ?? null;
    }

    /**
     * Check whether an attribute is set
     *
     * @param $name
     * @return bool
     */
    public function __isset($name) {

        if ($name === 'items') {
            $name = 'assignments';
        }

        return array_key_exists($name, $this->attributes);
    }

    /**
     * Append assignment
     *
     * @param assignment $assignment
     * @return $this
     */
    protected function append_assignment(assignment $assignment) {
        $this->assignments->append(assignment_model::load_by_entity($assignment));

        return $this;
    }
}