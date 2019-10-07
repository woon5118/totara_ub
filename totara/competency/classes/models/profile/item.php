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
use tassign_competency\models\assignment as assignment_model;
use totara_competency\data_providers\assignments;
use totara_competency\entities\assignment;
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
class item extends basic_model {

    /**
     * @var collection
     */
    protected $assignments;

    /**
     * Overall progress percentage
     *
     * @var int
     */
    protected $overall_progress = 0;

    /**
     * Progress item (group of competencies) name.
     * This would be a user group (pos,org audience) name or a string for
     * special cases such as self-assigned or others or legacy.
     *
     * @var string
     */
    protected $name;

    /**
     * Array of graph data
     *
     * @var array
     */
    protected $graph = [];

    /**
     * Key
     *
     * @var string
     */
    protected $key;

    /**
     * item constructor.
     *
     * @param string $key Item key (hash of some attributes)
     * @param string $name Progress name (see above)
     */
    public function __construct(string $key, string $name) {
        $this->key = $key;
        $this->name = $name;

        $this->assignments = new collection();
    }

    /**
     * Append assignment to this progress groups
     *
     * @param $assignment
     * @return $this
     */
    public function append_assignment($assignment) {
        $this->assignments->append($assignment);

        return $this;
    }

    /**
     * Get assignments of this progress group
     *
     * @return collection
     */
    public function get_assignments(): collection {
        return $this->assignments;
    }

    /**
     * Create progress group from assignments data provider
     *
     * @param assignments $provider Assignments data provider
     * @return collection
     */
    public static function build_from_assignments_provider(assignments $provider) {
        return static::build_from_assignments($provider->get());
    }

    /**
     * Create a progress group from a collection of assignments
     *
     * @param collection $assignments
     * @return collection
     */
    public static function build_from_assignments(collection $assignments) {
        $progress = new collection();

        $assignments->map(function(assignment $assignment) use ($progress) {
            $model = assignment_model::load_by_entity($assignment);
            if (!$progress->item($key = static::build_key(
                $assignment->type,
                $assignment->user_group_type,
                $assignment->user_group_id
            ))) {
                $progress->set(new static($key, $model->get_progress_name()), $key);
            }

            $progress->item($key)->append_assignment($model);
        });

        $progress->map(function (self $item) {
            $item->calculate_overall_progress();
        });

        return $progress;
    }

    /**
     * Allow magic access for some attributes
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'overall_progress':
                return $this->overall_progress;
            case 'assignments':
            case 'items':
                // ^^ Fallthrough intended, items is a synonym of assignments
                return $this->assignments;
            case 'name':
                return $this->name;
            case 'graph':
                return $this->graph;
            default:
                return null;
        }
    }

    /**
     * Is set must be implemented for it to be used with is_null which is used in GraphQL
     *
     * @param $name
     * @return bool
     */
    public function __isset($name): bool {
        return in_array($name, [
            'overall_progress',
            'assignments',
            'items',
            'name',
            'graph'
        ]);
    }

    /**
     * We need to calculate overall progress
     *
     * @return $this
     */
    public function calculate_overall_progress() {
        // Let's iterate over progress items and calculate individual progress percentage
        $competent_count = $this->get_assignments()->reduce(function($count, $assignment) {
            if (!$assignment->current_achievement) {
                return $count;
            } else {
                return $count + intval($assignment->current_achievement->proficient);
            }
        }, 0);

        $this->overall_progress = round($competent_count / count($this->get_assignments()) * 100);

        return $this;
    }

    /**
     * Helper function to build a unique progress item hash
     *
     * @param $type
     * @param $user_group_type
     * @param $user_group_id
     * @return string
     */
    protected static function build_key($type, $user_group_type, $user_group_id) {
        return md5("$type/$user_group_type/$user_group_id");
    }
}