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

/**
 * Activity entity
 *
 * @property-read int $id ID
 * @property int $course ID of parent course
 * @property string $description
 * @property string $name Activity name
 * @property int $status
 * @property int $updated_at
 * @property-read collection|section[] $sections
 *
 * @method static activity_repository repository()
 *
 * @package mod_perform\entities
 */
class activity extends entity {
    public const TABLE = 'perform';

    public const UPDATED_TIMESTAMP = 'updated_at';
    public const SET_UPDATED_WHEN_CREATED = true;

    /**
     * get activity sections
     *
     * @return has_many
     */
    public function sections(): has_many {
        return $this->has_many(section::class, 'activity_id');
    }
}