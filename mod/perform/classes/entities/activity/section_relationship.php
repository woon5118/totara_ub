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

use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;

/**
 * Activity section relationship entity
 *
 * Properties:
 * @property-read int $id ID
 * @property int $section_id ID of activity section
 * @property int $activity_relationship_id ID of activity relationship
 * @property int $can_view Flag indicating whether relationship is able to view
 * @property int $can_answer Flag indicating whether relationship is able to answer
 * @property int $created_at
 *
 * Relationships:
 * @property-read section $section
 * @property-read activity_relationship $activity_relationship
 *
 * @package mod_perform\entities
 */
class section_relationship extends entity {
    public const TABLE = 'perform_section_relationship';
    public const CREATED_TIMESTAMP = 'created_at';

    /**
     * Temp solution for name attribute. Must be updated when relationship classes are implemented in TL-24046.
     *
     * @return mixed|null
     */
    protected function get_name_attribute() {
        return $this->activity_relationship->class_name;
    }

    /**
     * Relationship with section entities.
     *
     * @return belongs_to
     */
    public function section(): belongs_to {
        return $this->belongs_to(section::class, 'section_id');
    }

    /**
     * Relationship with activity_relationship entities.
     *
     * @return belongs_to
     */
    public function activity_relationship(): belongs_to {
        return $this->belongs_to(activity_relationship::class, 'activity_relationship_id');
    }
}