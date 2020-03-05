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
 * @property string $status track status eg active, archived
 * @property int $created_at record creation time
 * @property int $updated_at record modification time
 *
 * @property-read collection|subject_instance[] $subject_instances
 */
final class track extends entity {

    public const TABLE = 'perform_track';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

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

}
