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
use core\orm\entity\relations\has_many_through;
use totara_core\entities\relationship;

/**
 * Represents an activity section record.
 *
 * Properties:
 * @property-read int $id ID
 * @property string $title Title of the section
 * @property int $activity_id ID of linked activity
 * @property int $sort_order order of sections within an activity
 * @property int $created_at
 * @property int $updated_at
 *
 * Relationships:
 * @property-read activity $activity
 * @property-read collection|relationship[] $core_relationships
 * @property-read collection|section_element[] $section_elements
 * @property-read collection|section_relationship[] $section_relationships
 * @property-read collection|participant_section[] $participant_sections
 */
class section extends entity {
    public const TABLE = 'perform_section';
    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';
    public const SET_UPDATED_WHEN_CREATED = true;

    /**
     * Each section belongs to a particular activity.
     *
     * @return belongs_to
     */
    public function activity(): belongs_to {
        return $this->belongs_to(activity::class, 'activity_id');
    }

    /**
     * A section owns a collection of section elements.
     *
     * @return has_many
     */
    public function section_elements(): has_many {
        return $this->has_many(section_element::class, 'section_id');
    }

    /**
     * A section owns a collection of participant sections.
     *
     * @return has_many
     */
    public function participant_sections(): has_many {
        return $this->has_many(participant_section::class, 'section_id');
    }

    /**
     * A section relates to a collection of activity relationships, through the activity that it belongs to.
     *
     * @return has_many_through
     */
    public function core_relationships(): has_many_through {
        return $this->has_many_through(
            section_relationship::class,
            relationship::class,
            'id',
            'section_id',
            'core_relationship_id',
            'id'
        );
    }

    /**
     * A section own a collection of section relationships.
     *
     * @return has_many
     */
    public function section_relationships(): has_many {
        return $this->has_many(section_relationship::class, 'section_id')
            ->as('sr')
            ->join([relationship::TABLE, 'cr'], 'sr.core_relationship_id', '=', 'cr.id')
            ->order_by('cr.id');
    }
}