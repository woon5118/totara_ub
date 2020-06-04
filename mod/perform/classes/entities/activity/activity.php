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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\has_many;
use core\orm\entity\relations\has_many_through;
use core\orm\entity\relations\has_one;
use totara_core\entities\relationship;

/**
 * Activity entity
 *
 * Properties:
 * @property-read int $id ID
 * @property int $type_id activity type
 * @property int $course ID of parent course
 * @property string $description
 * @property string $name Activity name
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * Relationships:
 * @property-read collection|section[] $sections
 * @property-read collection|section[] $sections_ordered
 * @property-read collection|activity_relationship[] $activity_relationships
 * @property-read collection|relationship[] $relationships
 * @property-read collection|track[] $tracks
 * @property-read activity_type $type
 *
 * @method static activity_repository repository()
 *
 * @package mod_perform\entities
 */
class activity extends entity {
    public const TABLE = 'perform';

    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';
    public const SET_UPDATED_WHEN_CREATED = true;

    /**
     * Relationship with section entities.
     *
     * @return has_many
     */
    public function sections(): has_many {
        return $this->has_many(section::class, 'activity_id');
    }

    /**
     * Relationship with section entities ordered by sort order.
     *
     * @return has_many
     */
    public function sections_ordered(): has_many {
        return $this->has_many(section::class, 'activity_id')
            ->order_by('sort_order');
    }

    /**
     * Activity relationships that are active for this activity.
     *
     * @return has_many
     */
    public function activity_relationships(): has_many {
        return $this->has_many(activity_relationship::class, 'activity_id');
    }

    /**
     * Tracks for this activity.
     *
     * @return has_many
     */
    public function tracks(): has_many {
        return $this->has_many(track::class, 'activity_id');
    }

    /**
     * Relationships that are active for this activity.
     *
     * @return has_many_through
     */
    public function relationships(): has_many_through {
        return $this->has_many_through(
            activity_relationship::class,
            relationship::class,
            'id',
            'activity_id',
            'core_relationship_id',
            'id'
        );
    }

    /**
     * Activity type.
     *
     * @return has_one the relationship.
     */
    public function type(): has_one {
        return $this->has_one(activity_type::class, 'id', 'type_id');
    }
}
