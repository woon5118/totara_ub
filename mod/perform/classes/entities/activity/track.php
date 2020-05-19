<?php
/**
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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;
use core\orm\entity\relations\has_many_through;

/**
 * Represents an activity track record in the repository.
 *
 * @property-read int $id record id
 * @property int $activity_id parent activity record id
 * @property string $description track description
 * @property int $status track status
 * @property bool $schedule_is_open
 * @property bool $schedule_is_fixed
 * @property int $schedule_fixed_from when schedule type is FIXED, contains the start date of assignment
 * @property int $schedule_fixed_to when schedule type is CLOSED_FIXED, contains the end date of assignment
 * @property int $schedule_dynamic_count_from number of units
 * @property int $schedule_dynamic_count_to number of units
 * @property int $schedule_dynamic_unit one of SCHEDULE_DYNAMIC_UNIT_XXX or null
 * @property int $schedule_dynamic_direction one of SCHEDULE_DYNAMIC_DIRECTION_XXX or null
 * @property bool $due_date_is_enabled
 * @property bool $schedule_needs_sync Flag indicating that the schedule sync task should run for this track
 * @property bool $repeating_is_enabled
 * @property int $created_at record creation time
 * @property int $updated_at record modification time
 * @property-read collection|subject_instance[] $subject_instances
 * @property-read activity $activity
 * @property-read collection|track_assignment[] $assignments
 *
 * @method static track_repository repository()
 */
class track extends entity {
    public const TABLE = 'perform_track';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    public const STATUS_ACTIVE = 1;
    public const STATUS_PAUSED = 0;

    public const SCHEDULE_DYNAMIC_UNIT_DAY = 0;
    public const SCHEDULE_DYNAMIC_UNIT_WEEK = 1;
    public const SCHEDULE_DYNAMIC_UNIT_MONTH = 2;

    public const SCHEDULE_DYNAMIC_DIRECTION_AFTER = 0;
    public const SCHEDULE_DYNAMIC_DIRECTION_BEFORE = 1;

    /**
     * Establishes the relationship with activity entities.
     *
     * @return belongs_to the relationship.
     */
    public function activity(): belongs_to {
        return $this->belongs_to(activity::class, 'activity_id');
    }

    /**
     * Establishes the relationship with track assignments.
     *
     * @return has_many the relationship.
     */
    public function assignments(): has_many {
        return $this->has_many(track_assignment::class, 'track_id');
    }

    /**
     * Get all subject instance for this track
     *
     * @return has_many_through
     */
    public function subject_instances(): has_many_through {
        return $this->has_many_through(
            track_user_assignment::class,
            subject_instance::class,
            'id',
            'track_id',
            'id',
            'track_user_assignment_id'
        );
    }

    /**
     * Cast schedule_is_fixed to bool type.
     *
     * @return bool
     */
    protected function get_schedule_is_fixed_attribute(): bool {
        return (bool) $this->get_attributes_raw()['schedule_is_fixed'];
    }

    /**
     * Cast schedule_is_open to bool type.
     *
     * @return bool
     */
    protected function get_schedule_is_open_attribute(): bool {
        return (bool) $this->get_attributes_raw()['schedule_is_open'];
    }

    /**
     * Cast due_date_is_enabled to bool type.
     *
     * @return bool
     */
    protected function get_due_date_is_enabled_attribute(): bool {
        return (bool) $this->get_attributes_raw()['due_date_is_enabled'];
    }

    /**
     * Cast repeating_is_enabled to bool type.
     *
     * @return bool
     */
    protected function get_repeating_is_enabled_attribute(): bool {
        return (bool) $this->get_attributes_raw()['repeating_is_enabled'];
    }

}
