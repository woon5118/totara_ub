<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\relationship;

use coding_exception;
use core\collection;
use core\orm\entity\repository;
use totara_core\entities\relationship as relationship_entity;

/**
 * Data provider for relationships.
 *
 * @package totara_core\relationship
 */
class relationship_provider {

    /**
     * @var repository
     */
    private $query;

    /**
     * @var bool
     */
    private $fetched = false;

    /**
     * @var collection|relationship[]
     */
    private $items;

    public function __construct() {
        $this->query = relationship_entity::repository()
            ->with('resolvers')
            ->order_by('component', 'DESC') // Places the non-plugin exclusive relationships at the top
            ->order_by('id');
    }

    /**
     * Restrict the returned relationships to ones that are compatible with a plugin.
     *
     * @param string $component
     * @return $this
     */
    public function filter_by_component(string $component): self {
        if ($this->fetched) {
            throw new coding_exception('Must call filter_by_component() before calling fetch()');
        }

        $this->query
            ->where('component', $component)
            ->or_where_null('component');

        return $this;
    }

    /**
     * Fetch the relationships that are compatible with the specified fieldset.
     *
     * @param string[] $compatible_fields fieldset to check e.g. ['job_assignment_id'] or ['user_id', 'course_id']
     * @return $this
     */
    public function filter_by_compatible(array $compatible_fields): self {
        if (!$this->fetched) {
            throw new coding_exception('Must call fetch() before calling filter_by_compatible()');
        }

        if (empty($compatible_fields)) {
            throw new coding_exception('Must specify at least one field to filter_by_compatible()');
        }

        $this->items = $this->items->filter(static function (relationship $relationship) use ($compatible_fields) {
            return $relationship->is_acceptable_input($compatible_fields);
        });

        return $this;
    }

    /**
     * Query the data from the database.
     *
     * @return $this
     */
    public function fetch(): self {
        $this->items = $this->query
            ->get()
            ->map_to(relationship::class);
        $this->fetched = true;

        return $this;
    }

    /**
     * Get a collection of the relationship objects that has been queried.
     *
     * @return collection|relationship[]
     */
    public function get(): collection {
        return $this->items;
    }

}
