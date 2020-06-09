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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\entities\user;
use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;
use totara_core\entities\relationship;

/**
 * Participant instance entity
 *
 * @property int $core_relationship_id ID of core relationship
 * @property int $participant_source
 * @property int $participant_id
 * @property int $subject_instance_id
 * @property int $progress
 * @property int $availability
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read relationship $core_relationship
 * @property-read subject_instance $subject_instance
 * @property-read collection|participant_section[] $participant_sections
 * @property-read user $participant_user
 *
 * @method static participant_instance_repository repository
 *
 * @package mod_perform\entities
 */
class participant_instance extends entity {

    public const TABLE = 'perform_participant_instance';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    /**
     * Relationship with core_relationship entities.
     *
     * @return belongs_to
     */
    public function core_relationship(): belongs_to {
        return $this->belongs_to(relationship::class, 'core_relationship_id');
    }

    /**
     * Relationship with subject_instance entities.
     *
     * @return belongs_to
     */
    public function subject_instance(): belongs_to {
        return $this->belongs_to(subject_instance::class, 'subject_instance_id');
    }

    /**
     * @return has_many
     */
    public function participant_sections(): has_many {
        return $this->has_many(participant_section::class, 'participant_instance_id');
    }

    /**
     * Get the user this participant instance belongs to.
     *
     * @return belongs_to
     */
    public function participant_user(): belongs_to {
        return $this->belongs_to(user::class, 'participant_id');
    }

}