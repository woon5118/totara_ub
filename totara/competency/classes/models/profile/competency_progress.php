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
use totara_competency\models\assignment as assignment_model;
use core\entities\user;
use totara_competency\data_providers\assignments;
use totara_competency\entities\assignment;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale_value;

/**
 * This is a profile progress item model scaffolding, it has the following properties available:
 *
 *  - Assignments -> [Assignment] - a collection of related assignment models
 *  - Overall progress -> int - Overall progress value per this group
 *
 * @property-read collection $assignments Collection of assignments for this user group
 * @property-read collection $items Collection of assignments for this user group
 * @property-read collection $filters Collection of filters
 * @property-read string $latest_achievement Latest achieved competency name (if any)
 * @package totara_competency\models
 */
class competency_progress {

    /**
     * @var collection
     */
    protected $assignments;

    /**
     * @var competency
     */
    protected $competency;

    /**
     * @var competency_achievement
     */
    protected $achievement;

    /**
     * @var scale_value|null
     */
    protected $my_value;

    /**
     * Attributes available publicly on the model
     *
     * @var array
     */
    protected $public_attributes = [
        'assignments',
        'competency',
        'achievement',
        'my_value',
    ];

    /**
     * competency_progress constructor.
     *
     * @param assignment $assignment Assignment entity
     */
    public function __construct(assignment $assignment) {
        $this->competency = $assignment->competency;
        $this->achievement = $assignment->current_achievement;
        $this->my_value = $assignment->current_achievement->value ?? null;
        $this->assignments = new collection([assignment_model::load_by_entity($assignment)]);
    }

    /**
     * Return assignments for a given competency
     *
     * @return collection
     */
    public function get_assignments(): collection {
        return $this->assignments;
    }

    /**
     * Build a collection of competency progress models using assignments
     *
     * @param collection $assignments
     * @return collection
     */
    public static function build_from_assignments(collection $assignments) {
        $progress = new collection();

        $assignments->map(function (assignment $assignment) use ($progress) {
            if (!$progress->item($assignment->competency_id)) {
                $progress->set(new static($assignment), $assignment->competency_id);
            } else {
                /** @var self $item */
                $item = $progress->item($assignment->competency_id);
                $item->append_assignment($assignment);
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
            ])->fetch()->get()
        )->first();
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

        // Calling ?? will automatically trigger isset allowing only public attributes
        return $this->{$name} ?? null;
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

        return in_array($name, $this->public_attributes);
    }

    /**
     * Append assignment to the current progress item model.
     * Only adds the assignment to the collection if there's none with the same
     * user_group_type and user_group_id to not show duplicates.
     *
     * @param assignment $assignment
     * @return $this
     */
    protected function append_assignment(assignment $assignment) {
        // In the case when there's the same competency assigned to the same
        // group and user we only add it once. Currently, all assignments for the same
        // competency will have the same achievements value as assignment specific criteria
        // are not yet implemented so there's no reason to show it multiple times in one group
        $existing_assignment = $this->assignments->find(function (assignment_model $item) use ($assignment) {
            $item_entity = $item->get_entity();

            return $item_entity->competency_id == $assignment->competency_id
                && $item_entity->user_group_type == $assignment->user_group_type
                && $item_entity->user_group_id == $assignment->user_group_id
                && $item_entity->status == $assignment->status;
        });
        if (!$existing_assignment) {
            $this->assignments->append(assignment_model::load_by_entity($assignment));
        }

        return $this;
    }
}