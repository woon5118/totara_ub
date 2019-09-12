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


use stdClass;
use totara_competency\entities\assignment;
use core\orm\collection;
use tassign_competency\models\assignment as assignment_model;

class competency_progress extends user_data_provider {

    /**
     * Progress object
     *
     * @var progress
     */
    protected $progress = null;

    /**
     * Order of items to load
     *
     * @var null
     */
    protected $order = null;


    public static function for_progress(progress $progress) {
        return static::for($progress->get_user())->set_progress($progress);
    }

    public function set_progress(progress $progress) {
        $this->progress = $progress;

        return $this;
    }

    public function set_order(string $order = null) {
        $this->order = $order;

        return $this;
    }

    public function fetch() {
        if (!$this->progress) {
            $this->set_progress(progress::for($this->user));
        }

        $this->set_filters($this->filters);

        if (!$this->progress->fetched) {
            $this->progress->fetch();
        }

        return $this->build_competency_progress_list();
    }

    public function fetch_for_competency(int $competency_id) {
        $this->set_filters([
            'competency_id' => $competency_id,
        ]);

        return $this->fetch()->get()->first();
    }

    protected function build_competency_progress_list() {
        $competencies = [];

        foreach ($this->progress->get_assignments()->get() as $assignment) {
            $id = $assignment->competency_id;
            $competencies[$id] = $this->build_competency_progress_object($assignment, $competencies[$id] ?? null);
        }

        $this->items = new collection($competencies);

        return $this->filter_progress()->order_progress();
    }

    protected function build_competency_progress_object(assignment $assignment, ?stdClass $current = null) {
        if (!$current) {
            $current = (object) [
                'competency' => $assignment->competency,
                'achievement' => $assignment->achievement,
                'my_value' => $assignment->achievement->scale_value ?? null,
                'assignments' => []
            ];
        }

        $current->assignments[] = assignment_model::load_by_entity($assignment);

        return $current;
    }

    protected function order_progress() {

        $latestAssignment = function($assignments, $field) {
            $maxDate = 0;
            foreach ($assignments as $assignment) {
                if ($assignment->get_field($field) > $maxDate) {
                    $maxDate = $assignment->get_field($field);
                }
            }

            return $maxDate;
        };

        switch ($this->order) {
            case 'recently-assigned': // TODO const?
                $this->items->sort(function ($a, $b) use ($latestAssignment) {
                    return $latestAssignment($b->assignments, 'assigned_at') <=> $latestAssignment($a->assignments, 'assigned_at');
                });
                break;

            case 'recently-archived':
                $this->items->sort(function ($a, $b) use ($latestAssignment) {
                    return $latestAssignment($b->assignments, 'archived_at') <=> $latestAssignment($a->assignments, 'archived_at');
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

    protected function filter_progress() {
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

        $progress_filters = array_filter($filters, function($key) {
            return in_array($key, ['status', 'type', 'user_group_type', 'user_group_id', 'search', 'competency_id']);
        }, ARRAY_FILTER_USE_KEY);

        if ($this->progress) {
            $this->progress->set_filters($progress_filters);
        }

        return $this;
    }

}