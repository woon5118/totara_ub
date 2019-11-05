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

use core\entities\user;
use core\orm\collection;
use core\orm\query\builder;
use core\orm\query\field;
use pathway_manual\entities\pathway_manual;
use pathway_manual\entities\role;
use pathway_manual\manual;
use pathway_manual\models\rateable_competency;
use pathway_manual\models\user_competencies;
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
     * @var user|null
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $role;

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
     * Get all rateable competencies that the specific user is assigned to.
     *
     * @param int|user $user User ID or entity
     * @return rateable_competencies
     */
    public static function for_user($user) {
        if (!$user instanceof user) {
            $user = new user($user);
        }

        $provider = new static();
        $provider->user = $user;
        $provider->filters['user_id'] = $user->id;

        return $provider;
    }

    /**
     * Get all rateable competencies for a particular role and assigned user.
     *
     * @param int|user $user User ID or entity
     * @param string $role e.g. manual::ROLE_SELF, manual::ROLE_MANUAL etc
     * @return rateable_competencies
     */
    public static function for_user_and_role($user, string $role) {
        manual::check_is_valid_role($role, true);

        $provider = self::for_user($user);

        $provider->role = $role;
        $provider->filters['roles'] = [$role];

        return $provider;
    }

    /**
     * Only get competencies that the user is assigned to.
     *
     * @param competency_repository $repository
     * @param int $user_id
     */
    protected function filter_by_user_id(competency_repository $repository, int $user_id) {
        $assignments = builder::table(competency_assignment_user::TABLE)
            ->where_field('competency_id', new field('id', $repository->get_builder()))
            ->where('user_id', $user_id);

        $repository->where_exists($assignments);
    }

    /**
     * Only get competencies that have the specified pathway roles enabled.
     *
     * @param competency_repository $repository
     * @param string[] $roles
     */
    protected function filter_by_roles(competency_repository $repository, array $roles) {
        manual::check_is_valid_role($roles, true);

        $roles = builder::table(role::TABLE)
            ->join([pathway_manual::TABLE, 'manual'], 'path_manual_id', 'manual.id')
            ->join([pathway::TABLE, 'pathway'], 'manual.id', 'pathway.path_instance_id')
            ->where_field('pathway.comp_id', new field('id', $repository->get_builder()))
            ->where_in('role', $roles);

        $repository->where_exists($roles);
    }

    /**
     * Filter by the roles that the current user has.
     *
     * @return self
     */
    public function add_current_user_roles_filter(): self {
        $this->filters['roles'] = manual::get_current_user_roles($this->user->id);

        return $this;
    }

    /**
     * Set filters for this provider.
     *
     * @param array $filters
     * @return $this
     */
    public function set_filters(array $filters) {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Build the query and apply filters for obtaining the data.
     *
     * @return competency_repository
     */
    protected function build_query(): competency_repository {
        $repository = competency::repository()
            ->with('scale')
            ->set_filters($this->filters)
            ->order_by('fullname')
            ->as('competency');

        $this->apply_filters($repository);

        return $repository;
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
                throw new \moodle_exception("Filtering by '{$key}' is currently not supported");
            }
        }

        return $this;
    }

    /**
     * Run the query with any added filters and store the result.
     *
     * @return $this
     */
    public function fetch() {
        if (!$this->fetched) {
            $this->items = $this->build_query()->get();
            $this->fetched = true;
        }

        return $this;
    }

    /**
     * Get just a list of the competencies.
     *
     * @return rateable_competency[]
     */
    public function get_competencies(): array {
        if (!$this->fetched) {
            $this->fetch();
        }

        return $this->items->map(function (competency $competency) {
            if (isset($this->role)) {
                return new rateable_competency($competency, $this->user, $this->role);
            } else {
                return new rateable_competency($competency, $this->user);
            }
        })->all();
    }

    /**
     * Get the competencies available for the user.
     *
     * @return user_competencies
     */
    public function get(): user_competencies {
        return new user_competencies($this->user, $this->role, $this->get_competencies());
    }

    /**
     * Get the number of competencies available.
     *
     * @return int
     */
    public function count(): int {
        return $this->build_query()->count();
    }

}
