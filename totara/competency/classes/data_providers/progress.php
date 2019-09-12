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


use totara_competency\entities\assignment;
use totara_assignment\entities\user;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;
use core\orm\collection;

class progress extends user_data_provider {

    /**
     * @var assignments
     */
    protected $assignments;

    /**
     * Create progress model
     *
     * @param int|user $user User id or instance
     */
    public function __construct($user) {
        parent::__construct($user);

        // Init empty collections
        $this->assignments = new assignments($this->user);
    }

    public function fetch() {
        $this->fetch_assignments();
        $this->fetched = true;

        return $this;
    }

    public function get_assignments() {
        return $this->assignments;
    }

    public function build_progress_data_per_user_group() {
        $progress = new collection();

        foreach ($this->assignments->get() as $assignment) {
            if (!$progress->item($key = $this->build_key($assignment))) {

                // TODO do something about it, either everything should operate on those or nothing...
                $model = \tassign_competency\models\assignment::load_by_entity($assignment);

                $progress->set((object) [
                    'key' => $key,
                    'name' => $model->get_progress_name(),
                    'user' => $this->user,
                    'assignments' => new collection([$assignment]),
                    'overall_progress' => 0,
                    'graph' => []
                ], $key);
            } else {
                $progress->item($key)->assignments->append($assignment);
            }
        }

        foreach ($progress as $item) {
            // Let's iterate over progress items and calculate individual progress percentage
            $competent_count = array_reduce($item->assignments->all(), function($count, $assignment) {
                if (!$assignment->achievement) {
                    return $count;
                } else {
                    return $count + intval($assignment->achievement->proficient);
                }
            }, 0);

            $item->overall_progress = round($competent_count / count($item->assignments) * 100);

            // Now let's insert my value percentage and minimum value percentage.
            $item->assignments->transform(function(assignment $assignment) {
                return $this->calculate_proficiency_chart_data($assignment);
            });

        }

        return $progress;
    }

    public function get_latest_achievement() {
        return competency_achievement::repository()
            ->select(['*', 'comp.fullname as competency_name'])
            ->join('comp', 'comp_id', 'id')
            ->where('status', [0, 1])
            ->where('proficient', true)
            ->where('user_id', $this->user->id)
            ->order_by('time_created', 'desc')
            ->first();
    }

    protected function build_key_old($type, $user_group_type, $user_group_id) {
        return md5("$type/$user_group_type/$user_group_id");
    }

    protected function build_key(assignment $assignment) {
        $type = $assignment->type;

        // We are grouping individual admin and manager assignments together...
        if ($type === assignment::TYPE_OTHER) {
            $type = assignment::TYPE_ADMIN;
        }

        return md5("$assignment->status/$type/$assignment->user_group_type/$assignment->user_group_id");
    }

    public function build_filters() {
        // For filters we need all assignments, then group them in the way for progress, e.g. by key
        // Then we need to iterate again and also include status in there OMG.

        // For filters we re-request data as we need to base it on all assignments, not the ones that might have been filtered already.
        $assignments = assignments::for($this->user)->fetch()->get();

        $filters = [];

        $ass_info = function ($assignment, $key) {
            // TODO do something about it, either everything should operate on those or nothing...
            $model = \tassign_competency\models\assignment::load_by_entity($assignment);

            return (object) [
                'name' => $model->get_progress_name(),
                'status_name' => $assignment->human_status,
                'status' => $assignment->status,
                'user_group_type' => $assignment->user_group_type,
                'user_group_id' => $assignment->user_group_id,
                'type' => $assignment->type,
                'key' => $key
            ];
        };

        foreach ($assignments as $assignment) {
            $key = $this->build_key($assignment);
            if (!isset($filters[$key])) {
                $filters[$key] = $ass_info($assignment, $key);
            }
        }

        return $filters;
    }

    protected function calculate_scale_value_percentage(scale_value $value, scale $scale) {
        if (!$scale->values) {
            throw new \coding_exception('Scale entity must have values loaded on it');
        }

        $count = count($scale->values);

        $pos = array_reduce($scale->values->all(), function($pos, $scale_value) use ($value) {
            if ($scale_value->sortorder >= $value->sortorder) {
                $pos += 1;
            }
            return $pos;
        }, 0);

        return round($pos / $count * 100);
    }

    protected function calculate_proficiency_chart_data(assignment $assignment) {

        //var_dump($assignment->competency); die;

        // Min would always have a value
        $min = (object) [
            'id' => $assignment->competency->scale->min_proficient_value->id,
            'name' => $assignment->competency->scale->min_proficient_value->name,
            'proficient' => true,
            'percentage' => $this->calculate_scale_value_percentage($assignment->competency->scale->min_proficient_value, $assignment->competency->scale),
        ];

        // Then we need to calculate value for our achievement
        if ($assignment->achievement) {
            $my = (object) [
                'id' => $assignment->achievement->scale_value->id,
                'name' => $assignment->achievement->scale_value->name,
                'proficient' => boolval($assignment->achievement->scale_value->proficient),
                'percentage' => $this->calculate_scale_value_percentage($assignment->achievement->scale_value, $assignment->competency->scale),
            ];
        } else {
            $my = (object) [
                'id' => 0,
                'name' => get_string('no_value_achieved', 'totara_competency'),
                'proficient' => false,
                'percentage' => 0,
            ];
        }

        $assignment = $this->append_property_to_entity($assignment, 'my_value', $my);
        return $this->append_property_to_entity($assignment, 'min_value', $min);
    }

    protected function fetch_assignments() {
        $this->assignments->with_competencies()
            ->with_achievements()
            ->with_scale_values()
            ->fetch();

        return $this;
    }

    public function set_filters(array $filters)
    {
        $this->assignments->set_filters($filters);

        return $this;
    }
}