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

namespace mod_perform\models\activity;

use core\orm\entity\entity;
use mod_perform\entities\activity\activity as activity_entity;

class activity {

    /**
     * @var activity_entity
     */
    protected $entity;

    /**
     * activity constructor.
     * @param activity_entity $entity
     */
    private function __construct(activity_entity $entity) {
        $this->entity = $entity;
    }

    /**
     * Create activity
     * @param activity_entity $entity
     *
     * @return activity
     */
    public static function create(activity_entity $entity): activity {
        return new activity($entity);
    }

    /**
     * @return entity
     */
    public function get_entity(): entity {
        return $this->entity;
    }
}