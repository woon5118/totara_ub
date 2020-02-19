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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\data_providers;

use core\orm\collection;
use core\orm\entity\repository;
use core\orm\query\field;
use core\orm\query\raw_field;
use pathway_manual\entities\role;
use pathway_manual\manual;
use pathway_manual\models\roles\role_factory;
use totara_competency\entities\competency;
use totara_competency\entities\competency_assignment_user;
use totara_competency\entities\competency_repository;
use totara_competency\entities\pathway;

/**
 * Class rateable_competencies
 *
 * Fetch and arrange competencies that can be rated.
 *
 * @package pathway_manual\data_providers
 */
class rateable_competencies extends provider {

    private const ROLE_CONCAT_SEPARATOR = '|';

    /**
     * Only get competencies that the user is assigned to.
     *
     * @param repository $repository
     * @param int $user_id
     */
    protected function filter_by_user_id(repository $repository, int $user_id) {
        $assigned = competency_assignment_user::repository()
            ->where('user_id', $user_id)
            ->where_field('competency_id', new field('id', $repository->get_builder()));

        $repository->where_exists($assigned->get_builder());
    }

    /**
     * Only get competencies that have at least one of the specified pathway roles enabled.
     * Adds "manual_roles" field which is a concatenated list of activated pathway roles for each competency.
     *
     * @param repository $repository
     * @param string[] $roles
     */
    protected function filter_by_roles(repository $repository, array $roles) {
        role_factory::roles_exist($roles, true);

        $repository
            ->join([pathway::TABLE, 'path'], 'id', 'competency_id')
            ->join([role::TABLE, 'manual_role'], 'path.path_instance_id', 'path_manual_id')
            ->add_select(new raw_field(
                $repository->group_concat('manual_role.role', self::ROLE_CONCAT_SEPARATOR) . ' as manual_roles'
            ))
            ->where('path.path_type', 'manual')
            ->where('path.status', manual::PATHWAY_STATUS_ACTIVE)
            ->where_in('manual_role.role', $roles)
            ->group_by('id');
    }

    /**
     * Build the query and apply filters for obtaining the data.
     *
     * @return competency_repository
     */
    protected function build_query(): repository {
        $repository = competency::repository();
        $repository->select('*');
        $this->apply_filters($repository);

        return $repository;
    }

    /**
     * Run the query with any added filters and store the result.
     *
     * @return collection
     */
    protected function fetch_from_query(): collection {
        return $this
            ->build_query()
            ->order_by('fullname')
            ->get();
    }

    /**
     * Get the competencies.
     *
     * @return competency[]
     */
    public function get() {
        return $this->fetch()->items->all();
    }

    /**
     * Count the number of items.
     *
     * @return int
     */
    public function count(): int {
        if ($this->fetched) {
            return $this->items->count();
        } else {
            return $this->build_query()->count();
        }
    }

    /**
     * When we have results filtered by roles, narrow them down to one role.
     *
     * @param string $role
     * @return collection
     */
    public function get_by_role(string $role): collection {
        if (!array_key_exists('roles', $this->filters)) {
            throw new \coding_exception("Can only get by role when roles filter is set.");
        }
        if ($this->fetched) {
            $items = $this->items;
        } else {
            $items = $this->fetch_from_query();
        }
        return $items->filter(function ($item) use ($role) {
            $roles = explode(self::ROLE_CONCAT_SEPARATOR, $item->manual_roles);
            return in_array($role, $roles);
        });
    }
}
