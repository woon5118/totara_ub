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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;

/**
 * Activity relationship entity
 *
 * Properties:
 * @property-read int $id ID
 * @property int $activity_id ID of activity
 * @property string $class_name Name of relationship class.
 * @property int $created_at
 *
 * Relationships:
 * @property-read activity $activity
 * @property-read collection|section_relationship[] $section_relationships
 */
class activity_relationship extends entity {
    public const TABLE = 'perform_relationship';
    public const CREATED_TIMESTAMP = 'created_at';

    /**
     * Relationship with activity entities.
     *
     * @return belongs_to
     */
    public function activity(): belongs_to {
        return $this->belongs_to(activity::class, 'activity_id');
    }

    public function section_relationships(): has_many {
        return $this->has_many(section_relationship::class, 'activity_relationship_id');
    }
}