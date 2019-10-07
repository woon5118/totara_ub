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

namespace totara_competency\data_providers;

use core\collection;
use tassign_competency\models\assignment;
use totara_competency\models\profile\competency_progress as competency_progress_model;

class competency_progress extends user_data_provider {

    /**
     * @var assignments
     */
    protected $assignments;

    /**
     * Order of items to load
     *
     * @var null
     */
    protected $order = null;

    /**
     * Set column to order by
     *
     * @param string|null $order
     * @return $this
     */
    public function set_order(string $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Fetch competency progress data from the database
     *
     * @return competency_progress
     */
    public function fetch() {
        if (!$this->assignments) {
            $this->assignments = assignments::for($this->get_user());
        }

        $this->set_filters($this->filters);

        $this->assignments->fetch();
        $this->fetched = true;
        $this->items = competency_progress_model::build_from_assignments($this->assignments->get());

        return $this->filter()->order();
    }

    protected function order() {

        switch ($this->order) {
            case 'recently-assigned': // TODO const?
                $this->items->sort(function ($a, $b) {
                    return $this->get_latest_assignment_by_field($b->assignments, 'assigned_at') <=>
                        $this->get_latest_assignment_by_field($a->assignments, 'assigned_at');
                });
                break;

            case 'recently-archived':
                $this->items->sort(function ($a, $b) {
                    return $this->get_latest_assignment_by_field($b->assignments, 'archived_at') <=>
                        $this->get_latest_assignment_by_field($a->assignments, 'archived_at');
                });
                break;

            case 'alphabetical':
                $this->items->sort(function($a, $b) {
                    return $a->competency->fullname <=> $b->competency->fullname;
                });
                break;

            case null:
                break;

            default:
                throw new \moodle_exception("Can not order by " . $this->order);
        }

        return $this;
    }

    protected function get_latest_assignment_by_field(collection $assignments, string $field) {
        return $assignments->reduce(function(int $maxDate, assignment $assignment) use ($field) {
            if ($assignment->get_field($field) > $maxDate) {
                $maxDate = $assignment->get_field($field);
            }

            return $maxDate;
        }, 0);
    }

    protected function filter() {
        $filters = array_filter($this->filters, function($key) {
            return in_array($key, ['proficient']);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($filters as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if (method_exists($this, $method = 'filter_by_' . $key)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    protected function filter_by_proficient($value) {
        $this->items = $this->items->filter(function($item) use ($value) {

            $proficient = false;

            if (!is_null($item->my_value)) {
                $proficient = !! $item->my_value->proficient;
            }

            return $value ? $proficient : !$proficient;
        });
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function set_filters(array $filters) {
        parent::set_filters($filters);

        // This filters are proxied to the assignments data provider
        $ass_progress = array_filter($filters, function($key) {
            return in_array($key, ['status', 'type', 'user_group_type', 'user_group_id', 'search', 'competency_id']);
        }, ARRAY_FILTER_USE_KEY);

        if ($this->assignments) {
            $this->assignments->set_filters($ass_progress);
        }

        return $this;
    }

}