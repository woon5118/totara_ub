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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace tassign_competency\entities;


use core\orm\query\builder;
use core\orm\query\field;
use core\orm\query\subquery;
use tassign_competency\filter\competency_assignment_status;
use tassign_competency\filter\competency_assignment_type;
use totara_assignment\entities\hierarchy_item_repository;

class competency_repository extends hierarchy_item_repository {

    /**
     * Define available default filters
     *
     * @return array
     */
    protected function get_default_filters(): array {
        return array_merge(
            parent::get_default_filters(),
            [
                'assignmenttype' => new competency_assignment_type(),
                'status' => new competency_assignment_status(),
            ]
        );
    }

    /**
     * Filter by competencies which are only self assignable
     *
     * @return $this
     */
    public function filter_by_self_assignable() {
        if (!$this->has_join('comp_assign_availability', 'availabilityself')) {
            $this->join(['comp_assign_availability', 'availabilityself'], 'id', 'comp_id');
            $this->where('availabilityself.availability', \totara_competency\entities\competency::ASSIGNMENT_CREATE_SELF);
        }

        return $this;
    }

    /**
     * Filter by competencies which are only other assignable
     *
     * @return $this
     */
    public function filter_by_other_assignable() {
        if (!$this->has_join('comp_assign_availability', 'availabilityother')) {
            $this->join(['comp_assign_availability', 'availabilityother'], 'id', 'comp_id');
            $this->where('availabilityother.availability', \totara_competency\entities\competency::ASSIGNMENT_CREATE_OTHER);
        }

        return $this;
    }

    /**
     * Set order by column and direction
     *
     * @param string $column
     * @param string $direction
     * @return $this
     *
     */
    public function order_by(string $column, string $direction = 'asc') {
        switch ($column) {
            case 'framework_hierarchy':
                return $this->order_by('frameworkid')
                    ->order_by('sortthread')
                    ->order_by('id');
                break;
            default:
                parent::order_by($column, $direction);
                break;
        }
        return $this;
    }

    /**
     * Include competency assignments as well
     *
     * @return $this
     */
    public function with_assignments_count(): competency_repository {
        $this->add_select((new subquery(function(builder $builder) {
            $builder->from('totara_assignment_competencies')
                ->as('count_tac')
                ->select('count(*)')
                ->where_field('competency_id', new field('id', $this->builder));
        }))->as('assignments_count'));

        return $this;
    }

}
