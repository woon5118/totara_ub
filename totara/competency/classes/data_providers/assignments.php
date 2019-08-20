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


use core\orm\query\subquery;
use tassign_competency\entities\assignment;
use tassign_competency\entities\assignment_repository;
use tassign_competency\entities\competency;
use totara_assignment\user_groups;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;
use core\orm\query\builder;
use core\orm\collection;

class assignments extends user_data_provider {

    protected $with_competencies = false;

    protected $with_achievements = false;

    protected $with_scale_values = false;

    /**
     * @var collection
     */
    protected $scales;

    /**
     * @var collection
     */
    protected $scale_values;

    public function fetch() {
        $this->fetch_assignments()
            ->fetch_competencies()
            ->fetch_achievements()
            ->fetch_scale_values()
            ->order_assignments();

        return $this;
    }

    public function get_all_scale_values() {
        return $this->scale_values;
    }

    public function get_all_scales() {
        return $this->scales;
    }

    public function get_scale_for_competency($competency) {
        $id = ($competency instanceof competency) ? $competency->id : intval($competency);

        $comp = $this->items->find('competency_id', $id);

        if ($comp) {
            $comp = $comp->competency;
        } else {
            return null;
        }

        return $this->scales->item($comp->scale_id);
    }

    protected function fetch_assignments() {

        // Let's apply filters first
        $repo = assignment::repository();

        $this->apply_filters($repo);

        // TODO this is a temporary workaround to accommodate for archived assignments
        // There probably should be something better

        $positions = builder::table('job_assignment')
            ->select_raw('distinct positionid as position_id')
            ->where('userid', $this->user->id)
            ->get()
            ->pluck('position_id');

        $organisations = builder::table('job_assignment')
            ->select_raw('distinct organisationid as organisation_id')
            ->where('userid', $this->user->id)
            ->get()
            ->pluck('organisation_id');

        $audiences = builder::table('cohort_members')
            ->select_raw('distinct cohortid as audience_id')
            ->where('userid', $this->user->id)
            ->get()
            ->pluck('audience_id');

        $this->items = $repo
            ->select('*')
            ->add_select((new subquery(function (builder $builder) {
                $builder->from('totara_assignment_competency_users')
                    ->select('created_at')
                    ->where_field('assignment_id', 'totara_assignment_competencies.id')
                    ->where('user_id', $this->user->id);
                }))->as('assigned_at'))
            ->with_user_group_name()
            ->with_competency_name()
            ->where('status', '!=', assignment::STATUS_DRAFT)
            ->where(function(builder $builder) use ($positions, $organisations, $audiences) {
                $builder->where(function(builder $builder) use ($audiences) {
                    $builder->where('user_group_type', user_groups::COHORT)
                        ->where('user_group_id', $audiences);
                })->or_where(function(builder $builder) use ($positions) {
                    $builder->where('user_group_type', user_groups::POSITION)
                        ->where('user_group_id', $positions);
                })->or_where(function(builder $builder) use ($organisations) {
                    $builder->where('user_group_type', user_groups::ORGANISATION)
                        ->where('user_group_id', $organisations);
                })->or_where(function(builder $builder) use ($organisations) {
                    $builder->where('user_group_type', user_groups::USER)
                        ->where('user_group_id', $this->user->id);
                });
            })
            ->get();

        return $this;
    }

    protected function fetch_competencies() {
        if ($this->with_competencies) {
            $competencies = competency::repository()
                // We are going to go ahead and fetch scale_id to help ourselves in the future
                ->select(['*', 'comp_scale_assignments.scaleid as scale_id'])
                ->join('comp_scale_assignments', 'frameworkid', 'frameworkid')
                ->where('id', $this->items->pluck('competency_id'))
                ->get();

            $this->append_property_to_assignment('competency', $competencies, 'competency_id');
        }

        return $this;
    }

    protected function fetch_achievements() {
        if ($this->with_achievements) {
            $achievements = competency_achievement::repository()
                ->where('assignment_id', $this->items->pluck('id'))
                ->where('user_id', $this->user->id)
                ->where(function(builder $builder) {
                    // We have to set status to 0 to ensure that there will be only one achievement per assignment
                    $builder->where('status', 0)
                        ->or_where('status', 1);
                })
                ->get()
                ->key_by('assignment_id');

            $this->append_property_to_assignment('achievement', $achievements, 'id');
        }

        return $this;
    }

    protected function fetch_scale_values() {
        if (!$this->with_scale_values) {
            // GOTO 180;
            return $this;
        }

        $this->scales = scale::repository()
            ->select('*')
            ->add_select_raw('(SELECT id FROM {comp_scale_values} WHERE scaleid = "comp_scale".id and proficient = 1 AND sortorder = (SELECT max(sortorder) FROM {comp_scale_values} WHERE scaleid = "comp_scale".id and proficient = 1)) as min_proficient_value_id')
            ->where('id', array_unique((new collection($this->items->pluck('competency')))->pluck('scale_id')))
            ->get();

        // Now let's get values for all those pesky scales
        $this->scale_values = scale_value::repository()
            ->where('scaleid', $this->scales->pluck('id'))
            ->order_by('sortorder', 'desc')
            ->get();

        // For convenience let's inject scale values into scales, it should not affect memory usage, since these are
        // going to be references anyway
        $this->scales = $this->scales->map(function($item) {
            $entity = $this->append_property_to_entity($item, 'values', $this->scale_values->filter('scaleid', $item->id));

            return $this->append_property_to_entity($entity, 'min_proficient_value', $this->scale_values->item($entity->min_proficient_value_id));
        });

        // Let's also map my value to achievements
        $this->items = $this->items->map(function(assignment $assignment) {

            if ($assignment->achievement) {
                $assignment->achievement =  $this->append_property_to_entity($assignment->achievement, 'scale_value', $this->scale_values->item($assignment->achievement->scale_value_id));
            }

            // And now let's inject scales into competencies, fun times.
            $assignment->competency = $this->append_property_to_entity($assignment->competency, 'scale', $this->scales->item($assignment->competency->scale_id));

            return $assignment;
        });

        return $this;
    }

    /**
     * Order competency assignments for profile
     *
     * @return $this
     */
    public function order_assignments() {
        $this->items->sort(\Closure::fromCallable([$this, 'order_assignments_callback']));

        return $this;
    }

    /**
     * Callback for sorting assignments in the way required for ordering assignments in the profile.
     *
     * @param assignment $first
     * @param assignment $second
     * @return int
     */
    protected function order_assignments_callback(assignment $first, assignment $second) {
        $type_map = [
            assignment::TYPE_ADMIN => 0,
            assignment::TYPE_OTHER => 1,
            assignment::TYPE_SELF => 2,
            assignment::TYPE_SYSTEM => 3,
        ];

        $ug_type_map = [
            user_groups::POSITION => 0,
            user_groups::ORGANISATION => 1,
            user_groups::COHORT => 2,
            user_groups::USER => 3,
        ];

        // Let's compare status
        if ($first->status != $second->status) {
            return $first->status <=> $second->status;
        }

        // Let's compare types first
        if ($first->type != $second->type) {
            return $type_map[$first->type] <=> $type_map[$second->type];
        }

        // Let's compare user group first
        if ($first->user_group_type != $second->user_group_type) {
            return $ug_type_map[$first->user_group_type] <=> $ug_type_map[$second->user_group_type];
        }

        // Then assignment type is the same, let's compare assignment creation date then
        if ($first->created_at != $second->created_at) {
            // Most recent first
            return $second->created_at <=> $first->created_at;
        }

        // If assignments were fetched with competency names, we use their name to sort alphabetically if
        // Creation order is the same
        if (isset($first->competency_name) && isset($second->competency_name)) {
            if ($first->competency_name != $second->competency_name) {
                return $first->competency_name < $second->competency_name ? -1 : 1;
            }
        }

        // All is lost, we can't figure out their exact order.
        return 0;
    }

    public function with_competencies(bool $with = true) {
        $this->with_competencies = $with;

        return $this;
    }

    public function with_achievements(bool $with = true) {
        $this->with_achievements = $with;

        return $this;
    }

    public function with_scale_values(bool $with = true) {
        $this->with_scale_values = $with;

        return $this;
    }

    protected function apply_filters(assignment_repository $repository) {
        foreach ($this->filters as $key => $value) {
            $method = 'filter_by_' . $key;
            if (method_exists($this, $method)) {
                !is_null($value) && $this->{$method}($repository, $value);
            } else {
                if (!is_null($value)) {
                    throw new \moodle_exception('Filtering by "' . $key . '" is currently not supported' );
                }
            }
        }

        return $this;
    }

    protected function filter_by_status(assignment_repository $repository, $value) {
        $repository->where('status', $value);
    }

    protected function filter_by_type(assignment_repository $repository, $value) {
        $repository->where('type', $value);
    }

    protected function filter_by_user_group_type(assignment_repository $repository, $value) {
        $repository->where('user_group_type', $value);
    }

    protected function filter_by_user_group_id(assignment_repository $repository, $value) {
        $repository->where('user_group_id', $value);
    }

    protected function filter_by_competency_id(assignment_repository $repository, int $value) {
        $repository->where('competency_id', $value);
    }

    protected function filter_by_search(assignment_repository $repository, $value) {
        if (!$repository->has_join('comp')) {
            $repository->join('comp', 'competency_id', 'id');
        }

        $repository->where(function(builder $builder) use ($value) {
            $builder->where('comp.fullname', 'ilike', $value)
                ->or_where('comp.description', 'ilike', $value);
        });
    }

    protected function append_property_to_assignment(string $property, collection $collection, $key) {
        $this->items = $this->items->map(function(assignment $assignment) use ($property, $collection, $key) {
            return $this->append_property_to_entity($assignment, $property, $collection->item($assignment->get_attribute($key)));
        });

        return $this;
    }
}