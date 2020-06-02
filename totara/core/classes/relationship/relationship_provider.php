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
use totara_core\entities\relationship as relationship_entity;

/**
 * Data provider for relationships.
 *
 * @package totara_core\relationship
 */
class relationship_provider {

    /**
     * Fetch all relationships that can be used.
     *
     * @return relationship[]|collection
     */
    public static function fetch_all_relationships(): collection {
        return relationship_entity::repository()
            ->with('resolvers')
            ->order_by('id')
            ->get()
            ->map_to(relationship::class);
    }

    /**
     * Fetch the relationships that are compatible with the specified fieldset.
     *
     * @param string[] $compatible_fields fieldset to check e.g. ['job_assignment_id'] or ['user_id', 'course_id']
     * @return relationship[]|collection
     */
    public static function fetch_compatible_relationships(array $compatible_fields): collection {
        if (empty($compatible_fields)) {
            throw new coding_exception(
                'Must specify at least one field to relationship_provider::fetch_compatible_relationships()'
            );
        }

        return static::fetch_all_relationships()
            ->filter(static function (relationship $relationship) use ($compatible_fields) {
                return $relationship->is_acceptable_input($compatible_fields);
            });
    }

}
