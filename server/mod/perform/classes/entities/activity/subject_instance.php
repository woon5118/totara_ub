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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\entities\user;
use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;
use core\orm\entity\relations\has_one_through;
use totara_core\entities\relationship as core_relationship;

/**
 * Subject instance id
 *
 * @property int $subject_user_id
 * @property int $track_user_assignment_id
 * @property int $progress
 * @property int $availability
 * @property int|null $job_assignment_id
 * @property int|null $due_date
 * @property int $status 0=unused, 1=default when new subject instance is active, 2=pending/waiting for manual selection"
 * @property int|null $completed_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read track_user_assignment $user_assignment
 * @property-read track $track
 * @property-read user $subject_user
 * @property-read collection|participant_instance[] $participant_instances
 * @property-read collection|manual_relationship_selection_progress[] $manual_relationship_selection_progress
 *
 * @method static subject_instance_repository repository()
 *
 * @package mod_perform\entities
 */
class subject_instance extends entity {

    public const TABLE = 'perform_subject_instance';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    /**
     * Get the user assignment this instance belongs to
     *
     * @return belongs_to
     */
    public function user_assignment(): belongs_to {
        return $this->belongs_to(track_user_assignment::class, 'track_user_assignment_id');
    }

    /**
     * Get the track this instance belongs to
     *
     * @return has_one_through
     */
    public function track(): has_one_through {
        return $this->has_one_through(
            track_user_assignment::class,
            track::class,
            'track_user_assignment_id',
            'id',
            'track_id',
            'id'
        );
    }

    /**
     * Get the user this subject instance belongs to
     *
     * @return belongs_to
     */
    public function subject_user(): belongs_to {
        return $this->belongs_to(user::class, 'subject_user_id');
    }

    /**
     * Get the activity this subject instance belongs to.
     * This does not use a relation as we cannot go through three tables in one go
     *
     * @return activity
     */
    public function activity(): activity {
        return $this->track->activity;
    }

    /**
     * @return has_many
     */
    public function participant_instances(): has_many {
        return $this->has_many(participant_instance::class, 'subject_instance_id')
            ->left_join([core_relationship::TABLE, 'cr'], 'core_relationship_id', 'cr.id')
            ->order_by('cr.id');
    }

    /**
     * @return has_many
     */
    public function manual_relationship_selection_progress(): has_many {
        return $this->has_many(manual_relationship_selection_progress::class, 'subject_instance_id');
    }
}