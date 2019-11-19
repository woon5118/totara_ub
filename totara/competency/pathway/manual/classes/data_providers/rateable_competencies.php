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
use core\orm\query\builder;
use core\orm\query\field;
use pathway_manual\entities\pathway_manual;
use pathway_manual\entities\role;
use pathway_manual\manual;
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
class rateable_competencies {

    /**
     * Array of filters to apply when fetching the data
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Return whether data has been fetched
     *
     * @var bool
     */
    protected $fetched = false;

    /**
     * @var collection
     */
    protected $items;

    /**
     * Get the names of the filters that we want to display options of.
     *
     * @return string[]
     */
    protected static function get_enabled_filter_options(): array {
        return [];
    }

    /**
     * Get the filter options available for the filters enabled.
     *
     * @return array[] Array of filter name => filter options array
     */
    protected function fetch_filter_options() {
        if (!$this->fetched) {
            $this->fetch();
        }

        $filter_options = [];

        foreach (static::get_enabled_filter_options() as $filter) {
            if (method_exists($this, 'get_' . $filter . '_filter_options')) {
                $filter_options[$filter] = $this->{'get_' . $filter . '_filter_options'}();
            } else {
                throw new \coding_exception("Filtering by '{$filter}' is currently not supported");
            }
        }

        return $filter_options;
    }

    /**
     * Add filters for this provider.
     *
     * @param array $filters
     * @return $this
     */
    public function add_filters(array $filters) {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Apply filters to a given repository.
     *
     * @param competency_repository $repository Repository to apply filters
     * @return $this
     */
    protected function apply_filters(competency_repository $repository) {
        foreach ($this->filters as $key => $value) {
            if (is_null($value)) {
                continue;
            } else if (method_exists($this, 'filter_by_' . $key)) {
                $this->{'filter_by_' . $key}($repository, $value);
            } else {
                throw new \coding_exception("Filtering by '{$key}' is currently not supported");
            }
        }

        return $this;
    }

    /**
     * Only get competencies that the user is assigned to.
     *
     * @param competency_repository $repository
     * @param int $user_id
     */
    private function filter_by_user_id(competency_repository $repository, int $user_id) {
        $assigned = competency_assignment_user::repository()
            ->where('user_id', $user_id)
            ->where_field('competency_id', new field('id', $repository->get_builder()));

        $repository->where_exists($assigned->get_builder());
    }

    /**
     * Only get competencies that have the specified pathway roles enabled.
     *
     * @param competency_repository $repository
     * @param string[] $roles
     */
    private function filter_by_roles(competency_repository $repository, array $roles) {
        manual::check_is_valid_role($roles, true);

        $roles = role::repository()
            ->join([pathway_manual::TABLE, 'manual'], 'path_manual_id', 'manual.id')
            ->join([pathway::TABLE, 'pathway'], 'manual.id', 'pathway.path_instance_id')
            ->where_field('pathway.comp_id', new field('id', $repository->get_builder()))
            ->where_in('role', $roles);

        $repository->where_exists($roles->get_builder());
    }

    /**
     * Build the query and apply filters for obtaining the data.
     *
     * @return competency_repository
     */
    protected function build_query(): competency_repository {
        $repository = competency::repository();

        $this->apply_filters($repository);

        return $repository;
    }

    /**
     * Run the query with any added filters and store the result.
     *
     * @return $this
     */
    public function fetch() {
        if (!$this->fetched) {
            $this->items = $this->build_query()
                ->order_by('fullname')
                ->get();

            $this->fetched = true;
        }

        return $this;
    }

    /**
     * Get the competencies.
     *
     * @return competency[]
     */
    public function get_competencies(): array {
        if (!$this->fetched) {
            $this->fetch();
        }

        return $this->items->all();
    }

    /**
     * Get the number of competencies available.
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

}
