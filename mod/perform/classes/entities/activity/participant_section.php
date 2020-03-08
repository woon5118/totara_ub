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

use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;

/**
 * Participant section entity
 *
 * @property int $section_id ID of activity section
 * @property int $participant_instance_id ID of participant instance
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read section $section
 * @property-read participant_instance $participant_instance
 *
 * @package mod_perform\entities
 */
class participant_section extends entity {
    public const TABLE = 'perform_participant_section';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    /**
     * Relationship with section entities.
     *
     * @return belongs_to
     */
    public function section(): belongs_to {
        return $this->belongs_to(section::class, 'section_id');
    }

    /**
     * Relationship with participant_instance entities.
     *
     * @return belongs_to
     */
    public function participant_instance(): belongs_to {
        return $this->belongs_to(participant_instance::class, 'participant_instance_id');
    }
}